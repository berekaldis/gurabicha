<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Models\VenueTable;
use Carbon\Carbon;
use Illuminate\Console\Command;

class VenueStatusCommand extends Command
{
    protected $signature = 'venue:status {--date= : Check specific date YYYY-MM-DD}';
    protected $description = 'Display real-time table availability and reservations';

    public function handle(): int
    {
        $dateStr = $this->option('date') ?: Carbon::now('UTC')->format('Y-m-d');
        $this->info("Venue Status for: {$dateStr}");

        $tables = VenueTable::orderBy('code')->get();

        foreach (['LUNCH', 'EVENING'] as $slot) {
            $this->newLine();
            $this->line("<comment>=== {$slot} SESSION ===</comment>");

            $confirmed = Reservation::whereDate('date', $dateStr)
                ->where('slot', $slot)
                ->where('status', 'CONFIRMED')
                ->get()
                ->keyBy('tableCode');

            $rows = [];
            foreach ($tables as $t) {
                $isBooked = isset($confirmed[$t->code]);
                $res = $isBooked ? $confirmed[$t->code] : null;

                $rows[] = [
                    'Table' => $t->code,
                    'Zone' => $t->zone,
                    'Capacity' => $t->capacity,
                    'Status' => $isBooked ? '<error>BOOKED</error>' : '<info>AVAILABLE</info>',
                    'Booking Code' => $res ? $res->code : '-',
                    'Guest' => $res ? "{$res->name} ({$res->guests}p)" : '-',
                    'Match' => $res && $res->matchLabel ? $res->matchLabel : '-',
                ];
            }

            $this->table(['Table', 'Zone', 'Capacity', 'Status', 'Code', 'Guest', 'Match'], $rows);
        }

        return Command::SUCCESS;
    }
}
