<?php

namespace App\Services;

use App\Models\Reservation;
use Carbon\Carbon;

class CalendarService
{
    /**
     * Slot session configurations.
     */
    public const SLOTS = [
        'LUNCH' => [
            'label' => 'Lunch Session',
            'time' => '12:30 PM – 3:30 PM',
            'short' => '12:30 PM',
            'startHour' => 9, // UTC (12:30 local is 09:30 UTC)
            'startMinute' => 30,
            'durationHours' => 3,
        ],
        'EVENING' => [
            'label' => 'Evening Session',
            'time' => '7:00 PM – 11:00 PM',
            'short' => '7:00 PM',
            'startHour' => 16, // UTC (19:00 local is 16:00 UTC)
            'startMinute' => 0,
            'durationHours' => 4,
        ],
    ];

    /**
     * Generate iCalendar (.ics) string for a reservation.
     */
    public function generateIcs(Reservation $reservation): string
    {
        $slotKey = $reservation->slot;
        $slot = self::SLOTS[$slotKey] ?? self::SLOTS['EVENING'];

        $date = Carbon::parse($reservation->date);
        $start = sprintf(
            '%04d%02d%02dT%02d%02d00Z',
            $date->year,
            $date->month,
            $date->day,
            $slot['startHour'],
            $slot['startMinute']
        );

        $endHour = $slot['startHour'] + $slot['durationHours'];
        $end = sprintf(
            '%04d%02d%02dT%02d%02d00Z',
            $date->year,
            $date->month,
            $date->day,
            $endHour,
            $slot['startMinute']
        );

        $stamp = gmdate('Ymd\THis\Z');
        $title = $reservation->matchLabel
            ? "Match Day at Gura Bicha ({$reservation->code})"
            : "Game & Groove at Gura Bicha ({$reservation->code})";

        $matchLine = $reservation->matchLabel ? ", {$reservation->matchLabel}" : "";
        $phone = \App\Models\Setting::get('venue_phone', '+251905888724');
        $description = "Table {$reservation->tableCode} · {$reservation->guests} guests · {$slot['time']}{$matchLine}. Questions? Call {$phone}.";

        $escape = function (string $text): string {
            return str_replace(['\\', ',', ';', "\n"], ['\\\\', '\\,', '\\;', '\\n'], $text);
        };

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Gura Bicha Sports Bar//Reservation//EN',
            'BEGIN:VEVENT',
            "UID:{$reservation->code}@gurabicha",
            "DTSTAMP:{$stamp}",
            "DTSTART:{$start}",
            "DTEND:{$end}",
            "SUMMARY:" . $escape($title),
            "DESCRIPTION:" . $escape($description),
            "LOCATION:Gura Bicha Sports Bar\\, Mexico Chamber of Commerce Building\\, Addis Ababa",
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return implode("\r\n", $lines);
    }

    /**
     * Generate Google Calendar Template URL.
     */
    public function generateGoogleCalendarUrl(Reservation $reservation): string
    {
        $slotKey = $reservation->slot;
        $slot = self::SLOTS[$slotKey] ?? self::SLOTS['EVENING'];

        $date = Carbon::parse($reservation->date);
        $start = sprintf(
            '%04d%02d%02dT%02d%02d00Z',
            $date->year,
            $date->month,
            $date->day,
            $slot['startHour'],
            $slot['startMinute']
        );

        $endHour = $slot['startHour'] + $slot['durationHours'];
        $end = sprintf(
            '%04d%02d%02dT%02d%02d00Z',
            $date->year,
            $date->month,
            $date->day,
            $endHour,
            $slot['startMinute']
        );

        $title = $reservation->matchLabel
            ? "Match Day at Gura Bicha ({$reservation->code})"
            : "Game & Groove at Gura Bicha ({$reservation->code})";

        $matchSuffix = $reservation->matchLabel ? " · {$reservation->matchLabel}" : "";
        $details = "Table {$reservation->tableCode} · {$reservation->guests} guests · {$slot['time']}{$matchSuffix}";

        return 'https://calendar.google.com/calendar/render?' . http_build_query([
            'action' => 'TEMPLATE',
            'text' => $title,
            'dates' => "{$start}/{$end}",
            'details' => $details,
            'location' => 'Gura Bicha Sports Bar, Mexico Chamber of Commerce Building, Addis Ababa',
        ]);
    }
}
