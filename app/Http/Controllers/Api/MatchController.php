<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SportMatch;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchController extends Controller
{
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
     * GET /api/matches                      -> upcoming matches (kickoff >= now - 4h), asc, max 40
     * GET /api/matches?year=2026            -> every match in calendar year 2026, asc
     * GET /api/matches?date=YYYY-MM-DD      -> every match on that UTC calendar day, asc
     */
    public function index(Request $request): JsonResponse
    {
        try {
            if ($request->has('status')) {
                $statusParam = strtoupper(trim($request->query('status')));
                $matches = SportMatch::where('status', $statusParam)
                    ->orderBy('kickoff', 'asc')
                    ->get();
                return response()->json(['matches' => $matches]);
            }

            if ($request->has('year')) {
                $yearParam = $request->query('year');
                if (!preg_match('/^\d{4}$/', $yearParam)) {
                    return response()->json(['error' => 'Invalid year. Must be a 4-digit year.'], 400);
                }

                $start = Carbon::createFromDate((int)$yearParam, 1, 1, 'UTC')->startOfDay();
                $end = Carbon::createFromDate((int)$yearParam + 1, 1, 1, 'UTC')->startOfDay();

                $matches = SportMatch::where('kickoff', '>=', $start)
                    ->where('kickoff', '<', $end)
                    ->orderBy('kickoff', 'asc')
                    ->get();

                return response()->json(['matches' => $matches]);
            }

            if ($request->has('date')) {
                $dateParam = $request->query('date');
                $day = $this->parseUTCDate($dateParam);
                if (!$day) {
                    return response()->json([
                        'error' => 'Invalid date. Must be a real calendar date in YYYY-MM-DD format.',
                    ], 400);
                }

                $nextDay = $day->copy()->addDay();
                $matches = SportMatch::where('kickoff', '>=', $day)
                    ->where('kickoff', '<', $nextDay)
                    ->orderBy('kickoff', 'asc')
                    ->get();

                return response()->json(['matches' => $matches]);
            }

            $now = Carbon::now('UTC');

            if ($request->has('period')) {
                $period = strtolower(trim($request->query('period')));

                if ($period === 'today') {
                    $start = $now->copy()->startOfDay();
                    $end = $now->copy()->endOfDay();
                    $matches = SportMatch::whereBetween('kickoff', [$start, $end])
                        ->orderBy('kickoff', 'asc')->get();
                    return response()->json(['matches' => $matches]);
                }

                if ($period === 'saturday') {
                    $sat = $now->copy();
                    if ($sat->dayOfWeek !== Carbon::SATURDAY) {
                        $sat->next(Carbon::SATURDAY);
                    }
                    $matches = SportMatch::whereBetween('kickoff', [$sat->copy()->startOfDay(), $sat->copy()->endOfDay()])
                        ->orderBy('kickoff', 'asc')->get();
                    return response()->json(['matches' => $matches]);
                }

                if ($period === 'results') {
                    $matches = SportMatch::where('status', 'FINISHED')
                        ->where('kickoff', '>=', $now->copy()->subDays(7))
                        ->orderBy('kickoff', 'desc')->take(20)->get();
                    return response()->json(['matches' => $matches]);
                }

                if ($period === 'live') {
                    $matches = SportMatch::where('status', 'LIVE')
                        ->orWhere('live', true)
                        ->orderBy('kickoff', 'asc')->get();
                    return response()->json(['matches' => $matches]);
                }
            }

            // Default & ?current=1: current-time matches (recent 3 days + upcoming 14 days)
            $recentCutoff = $now->copy()->subDays(3);
            $upcomingCutoff = $now->copy()->addDays(14);

            $matches = SportMatch::whereBetween('kickoff', [$recentCutoff, $upcomingCutoff])
                ->orderByRaw("CASE WHEN status = 'LIVE' THEN 1 WHEN kickoff >= ? THEN 2 ELSE 3 END", [$now->toDateTimeString()])
                ->orderBy('kickoff', 'asc')
                ->take(40)
                ->get();

            // If no matches found in window (e.g. before sync), fallback to upcoming
            if ($matches->isEmpty()) {
                $matches = SportMatch::where('kickoff', '>=', $now->copy()->subHours(4))
                    ->orderBy('kickoff', 'asc')
                    ->take(40)
                    ->get();
            }

            // Final fallback
            if ($matches->isEmpty()) {
                $matches = SportMatch::orderBy('kickoff', 'asc')->take(40)->get();
            }

            return response()->json(['matches' => $matches]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
