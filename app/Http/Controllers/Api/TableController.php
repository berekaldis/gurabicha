<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\VenueTable;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TableController extends Controller
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
     * GET /api/tables?date=YYYY-MM-DD&slot=LUNCH|EVENING
     * Returns every table with an `available` flag (false when a CONFIRMED
     * reservation exists for the same UTC calendar date + slot + tableCode),
     * plus `remaining` = count of available tables.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $dateStr = $request->query('date');
            $slot = $request->query('slot');

            if (!$dateStr) {
                return response()->json(['error' => 'Missing required query param: date'], 400);
            }
            if (!$slot) {
                return response()->json(['error' => 'Missing required query param: slot'], 400);
            }
            if ($slot !== 'LUNCH' && $slot !== 'EVENING') {
                return response()->json(['error' => 'Invalid slot. Must be LUNCH or EVENING.'], 400);
            }

            $date = $this->parseUTCDate($dateStr);
            if (!$date) {
                return response()->json([
                    'error' => 'Invalid date. Must be a real calendar date in YYYY-MM-DD format.',
                ], 400);
            }

            $tables = VenueTable::orderBy('code', 'asc')->get();

            // Match calendar day and slot (both CONFIRMED and PENDING hold the table)
            $confirmed = Reservation::whereDate('date', $date->toDateString())
                ->where('slot', $slot)
                ->whereIn('status', ['CONFIRMED', 'PENDING'])
                ->pluck('tableCode')
                ->all();

            $taken = array_flip($confirmed);

            $result = $tables->map(function ($t) use ($taken) {
                return [
                    'code' => $t->code,
                    'zone' => $t->zone,
                    'capacity' => $t->capacity,
                    'seats' => $t->seats,
                    'available' => !isset($taken[$t->code]),
                ];
            });

            $remaining = $result->filter(fn ($t) => $t['available'])->count();

            return response()->json([
                'tables' => $result,
                'remaining' => $remaining,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
