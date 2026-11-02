<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\SportMatch;
use App\Models\VenueTable;
use App\Services\TelegramAuthService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(
        protected TelegramAuthService $telegramAuth
    ) {}

    /**
     * Parses a YYYY-MM-DD string as UTC midnight and verifies it is a real calendar date.
     */
    private function parseUTCDate(string $dateStr): ?Carbon
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
            return null;
        }

        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $dateStr, 'UTC')->startOfDay();
            if ($parsed->format('Y-m-d') !== $dateStr) {
                return null;
            }
            return $parsed;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Keeps digits only — used to normalize phone numbers for storage/lookup.
     */
    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone);
    }

    /**
     * Generate unique GB-XXXX code.
     */
    private function generateUniqueCode(): string
    {
        for ($attempt = 0; $attempt < 50; $attempt++) {
            $candidate = 'GB-' . str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            if (!Reservation::where('code', $candidate)->exists()) {
                return $candidate;
            }
        }

        throw new \RuntimeException('Could not generate a unique reservation code');
    }

    /**
     * GET /api/reservations?phone=X     -> lookup by phone (normalized, startsWith)
     * GET /api/reservations?mine=1      -> lookup by Telegram identity (x-telegram-init-data header)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $phoneParam = $request->query('phone');
            $mineParam = $request->query('mine');

            if ($mineParam === '1') {
                $tgUser = $this->telegramAuth->verifyInitData($request->header('x-telegram-init-data'));
                if (!$tgUser) {
                    return response()->json([
                        'error' => 'Telegram identity required. Open this app from Telegram.',
                    ], 401);
                }

                $reservations = Reservation::where('telegramUserId', (string)$tgUser['id'])
                    ->orderBy('created_at', 'desc')
                    ->take(20)
                    ->get();

                return response()->json(['reservations' => $reservations]);
            }

            if ($phoneParam === null || trim($phoneParam) === '') {
                return response()->json([
                    'error' => 'Missing required query param: phone (or mine=1 with Telegram header)',
                ], 400);
            }

            $normalized = $this->normalizePhone($phoneParam);
            if ($normalized === '') {
                return response()->json(['error' => 'Invalid phone number.'], 400);
            }

            $reservations = Reservation::where('phone', 'like', "{$normalized}%")
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get();

            return response()->json(['reservations' => $reservations]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * POST /api/reservations
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            /* ---- telegram identity ---- */
            $tgHeader = $request->header('x-telegram-init-data');
            $telegramUserId = null;
            $telegramUsername = null;
            if ($tgHeader) {
                $tgUser = $this->telegramAuth->verifyInitData($tgHeader);
                if (!$tgUser) {
                    return response()->json([
                        'error' => 'Telegram identity verification failed. Please reopen the Mini App.',
                    ], 401);
                }
                $telegramUserId = (string)$tgUser['id'];
                $telegramUsername = $tgUser['username'] ?? null;
            }

            /* ---- date ---- */
            $dateStr = is_string($data['date'] ?? null) ? $data['date'] : '';
            $date = $this->parseUTCDate($dateStr);
            if (!$date) {
                return response()->json([
                    'error' => 'Invalid date. Must be a real calendar date in YYYY-MM-DD format.',
                ], 400);
            }

            /* ---- not in the past (compare UTC calendar dates) ---- */
            $todayStr = Carbon::now('UTC')->format('Y-m-d');
            if ($dateStr < $todayStr) {
                return response()->json([
                    'error' => 'Cannot book a date in the past.',
                ], 400);
            }

            /* ---- slot ---- */
            $slot = is_string($data['slot'] ?? null) ? $data['slot'] : '';
            if ($slot !== 'LUNCH' && $slot !== 'EVENING') {
                return response()->json([
                    'error' => 'Invalid slot. Must be LUNCH or EVENING.',
                ], 400);
            }

            /* ---- table ---- */
            $tableCode = is_string($data['tableCode'] ?? null) ? trim($data['tableCode']) : '';
            if ($tableCode === '') {
                return response()->json(['error' => 'tableCode is required.'], 400);
            }

            $table = VenueTable::where('code', $tableCode)->first();
            if (!$table) {
                return response()->json([
                    'error' => "Table {$tableCode} does not exist.",
                ], 400);
            }

            /* ---- guests ---- */
            $guests = $data['guests'] ?? null;
            if (!is_int($guests) || $guests < 1 || $guests > 12) {
                return response()->json([
                    'error' => 'guests must be an integer between 1 and 12.',
                ], 400);
            }
            if ($guests > $table->capacity) {
                return response()->json([
                    'error' => "Table {$tableCode} seats at most {$table->capacity} guests.",
                ], 400);
            }

            /* ---- name ---- */
            $name = is_string($data['name'] ?? null) ? trim($data['name']) : '';
            if ($name === '') {
                return response()->json(['error' => 'Name is required.'], 400);
            }

            /* ---- phone ---- */
            $phoneRaw = is_string($data['phone'] ?? null) ? trim($data['phone']) : '';
            if ($phoneRaw === '' || strlen($phoneRaw) < 7) {
                return response()->json([
                    'error' => 'Phone number is required (min 7 characters).',
                ], 400);
            }
            $phone = $this->normalizePhone($phoneRaw);

            /* ---- match (optional) ---- */
            $matchId = null;
            $matchLabel = null;
            if (isset($data['matchId']) && is_string($data['matchId']) && trim($data['matchId']) !== '') {
                $match = SportMatch::find(trim($data['matchId']));
                if (!$match) {
                    return response()->json(['error' => 'Unknown match selected.'], 400);
                }

                $kickoffDay = Carbon::parse($match->kickoff)->setTimezone('UTC')->format('Y-m-d');
                if ($kickoffDay !== $dateStr) {
                    return response()->json([
                        'error' => 'Selected match does not kick off on the chosen date.',
                    ], 400);
                }

                $matchId = $match->id;
                $matchLabel = "{$match->homeTeam} vs {$match->awayTeam} · {$match->competition} — {$match->stage}";
            }

            /* ---- availability (409) ---- */
            $conflict = Reservation::whereDate('date', $date->toDateString())
                ->where('slot', $slot)
                ->where('tableCode', $tableCode)
                ->where('status', 'CONFIRMED')
                ->exists();

            if ($conflict) {
                return response()->json([
                    'error' => "Table {$tableCode} is no longer available for that slot. Please pick another.",
                ], 409);
            }

            /* ---- create ---- */
            $code = $this->generateUniqueCode();
            $email = isset($data['email']) && is_string($data['email']) && trim($data['email']) !== ''
                ? trim($data['email'])
                : null;
            $reservationRequest = isset($data['request']) && is_string($data['request']) && trim($data['request']) !== ''
                ? trim($data['request'])
                : null;

            $reservation = Reservation::create([
                'code' => $code,
                'date' => $date,
                'slot' => $slot,
                'tableCode' => $tableCode,
                'guests' => $guests,
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'request' => $reservationRequest,
                'matchId' => $matchId,
                'matchLabel' => $matchLabel,
                'telegramUserId' => $telegramUserId,
                'telegramUsername' => $telegramUsername,
                'status' => 'CONFIRMED',
            ]);

            return response()->json(['reservation' => $reservation], 201);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * DELETE /api/reservations/{id} — soft cancel: sets status to CANCELLED.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $reservation = Reservation::find($id);
            if (!$reservation) {
                return response()->json(['error' => 'Reservation not found'], 404);
            }

            $reservation->status = 'CANCELLED';
            $reservation->save();

            return response()->json(['reservation' => $reservation]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
