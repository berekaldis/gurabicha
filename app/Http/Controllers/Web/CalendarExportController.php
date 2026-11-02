<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\CalendarService;
use Illuminate\Http\Response;

class CalendarExportController extends Controller
{
    public function __construct(
        protected CalendarService $calendar
    ) {}

    /**
     * Download .ics calendar event for a reservation.
     */
    public function download(string $code): Response
    {
        $reservation = Reservation::where('code', $code)->firstOrFail();

        $ics = $this->calendar->generateIcs($reservation);

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"reservation-{$code}.ics\"",
        ]);
    }
}
