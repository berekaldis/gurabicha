<?php

namespace Database\Seeders;

use App\Models\Reservation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear reservations on fresh seed
        Reservation::truncate();

        $this->call([
            AdminUserSeeder::class,
            SettingSeeder::class,
            VenueTableSeeder::class,
            EventSeeder::class,
            MatchSeeder::class,
            MenuItemSeeder::class,
            CustomerSeeder::class,
            TelegramSubscriberSeeder::class,
        ]);
    }
}
