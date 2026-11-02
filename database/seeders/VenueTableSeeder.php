<?php

namespace Database\Seeders;

use App\Models\VenueTable;
use Illuminate\Database\Seeder;

class VenueTableSeeder extends Seeder
{
    public function run(): void
    {
        VenueTable::truncate();

        $tables = [
            ['code' => 'T1', 'zone' => 'Stage Left - Acoustic View', 'capacity' => 4, 'seats' => '2–4 people'],
            ['code' => 'T2', 'zone' => 'Center Acoustic Floor', 'capacity' => 6, 'seats' => '4–6 people'],
            ['code' => 'T3', 'zone' => 'Stage Right - Acoustic View', 'capacity' => 4, 'seats' => '2–4 people'],
            ['code' => 'T4', 'zone' => 'Acoustic Stage Front', 'capacity' => 6, 'seats' => '4–6 people'],
            ['code' => 'T5', 'zone' => 'Mezzanine Acoustic Lounge', 'capacity' => 8, 'seats' => '6–8 people'],
            ['code' => 'T6', 'zone' => 'Artist Circle Lounge', 'capacity' => 8, 'seats' => '6–8 people'],
        ];

        foreach ($tables as $t) {
            VenueTable::create($t);
        }
    }
}
