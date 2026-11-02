<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    /**
     * GET /api/events — upcoming events (date >= start of today UTC), sorted by date asc.
     */
    public function index(): JsonResponse
    {
        try {
            $startOfTodayUTC = Carbon::now('UTC')->startOfDay();

            $events = Event::where('date', '>=', $startOfTodayUTC)
                ->orderBy('date', 'asc')
                ->get();

            return response()->json(['events' => $events]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
