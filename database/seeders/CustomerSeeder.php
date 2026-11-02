<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Abebe Kebede',
                'phone' => '+251911234567',
                'email' => 'abebe@example.com',
                'favorite_club' => 'Arsenal',
                'notify_matchday' => true,
                'notify_scores' => true,
                'notify_acoustic' => true,
                'notes' => 'Regular weekend patron, prefers Stage View.',
            ],
            [
                'name' => 'Tigist Haile',
                'phone' => '+251912345678',
                'email' => 'tigist@example.com',
                'favorite_club' => 'Manchester United',
                'notify_matchday' => true,
                'notify_scores' => true,
                'notify_acoustic' => false,
                'notes' => 'Comes for big derbies.',
            ],
            [
                'name' => 'Yonas Tesfaye',
                'phone' => '+251913456789',
                'email' => 'yonas@example.com',
                'favorite_club' => 'Chelsea',
                'notify_matchday' => true,
                'notify_scores' => false,
                'notify_acoustic' => true,
                'notes' => 'Acoustic Saturdays enthusiast.',
            ],
            [
                'name' => 'Bethlehem Desta',
                'phone' => '+251914567890',
                'email' => 'bethlehem@example.com',
                'favorite_club' => 'Real Madrid',
                'notify_matchday' => true,
                'notify_scores' => true,
                'notify_acoustic' => true,
                'notes' => 'Champions League matchday fan.',
            ],
        ];

        foreach ($customers as $c) {
            Customer::updateOrCreate(
                ['phone' => $c['phone']],
                $c
            );
        }
    }
}
