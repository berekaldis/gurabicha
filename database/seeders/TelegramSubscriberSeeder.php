<?php

namespace Database\Seeders;

use App\Models\TelegramBroadcast;
use App\Models\TelegramSubscriber;
use Illuminate\Database\Seeder;

class TelegramSubscriberSeeder extends Seeder
{
    public function run(): void
    {
        $subscribers = [
            [
                'chat_id' => '100101',
                'first_name' => 'Dawit',
                'last_name' => 'Mengistu',
                'username' => 'dawit_m',
                'favorite_club' => 'Arsenal',
                'interest' => 'ALL',
                'is_active' => true,
                'last_interaction_at' => now()->subHours(2),
            ],
            [
                'chat_id' => '100102',
                'first_name' => 'Yared',
                'last_name' => 'Haile',
                'username' => 'yared_addis',
                'favorite_club' => 'Arsenal',
                'interest' => 'MATCHES',
                'is_active' => true,
                'last_interaction_at' => now()->subHours(5),
            ],
            [
                'chat_id' => '100103',
                'first_name' => 'Kassahun',
                'last_name' => 'Bekele',
                'username' => 'kassa_reddevils',
                'favorite_club' => 'Manchester United',
                'interest' => 'ALL',
                'is_active' => true,
                'last_interaction_at' => now()->subDay(),
            ],
            [
                'chat_id' => '100104',
                'first_name' => 'Meron',
                'last_name' => 'Tadesse',
                'username' => 'meron_chelsea',
                'favorite_club' => 'Chelsea',
                'interest' => 'MATCHES',
                'is_active' => true,
                'last_interaction_at' => now()->subDays(2),
            ],
            [
                'chat_id' => '100105',
                'first_name' => 'Selamawit',
                'last_name' => 'Worku',
                'username' => 'selam_acoustic',
                'favorite_club' => null,
                'interest' => 'ACOUSTIC',
                'is_active' => true,
                'last_interaction_at' => now()->subDays(3),
            ],
            [
                'chat_id' => '100106',
                'first_name' => 'Tariku',
                'last_name' => 'Girma',
                'username' => 'tariku_liv',
                'favorite_club' => 'Liverpool',
                'interest' => 'ALL',
                'is_active' => true,
                'last_interaction_at' => now()->subHours(8),
            ],
        ];

        foreach ($subscribers as $s) {
            TelegramSubscriber::updateOrCreate(
                ['chat_id' => $s['chat_id']],
                $s
            );
        }

        if (TelegramBroadcast::count() === 0) {
            TelegramBroadcast::create([
                'title' => '⚽ Matchday Screening: Arsenal vs Chelsea',
                'category' => 'FIXTURE',
                'target_audience' => 'ALL',
                'message' => 'Huge Premier League clash tonight on 4K projection screens with stadium audio! Enjoy fresh Kitfo specials and draft beer combos.',
                'action_button_text' => '⚽ Open Match Center',
                'action_button_url' => url('/#fixtures'),
                'recipients_count' => 6,
                'status' => 'SENT',
                'sent_by_name' => 'Super Admin',
            ]);

            TelegramBroadcast::create([
                'title' => '🎸 Saturday Acoustic Groove Sessions',
                'category' => 'EVENT',
                'target_audience' => 'ACOUSTIC',
                'message' => 'Join us this Saturday from 7:00 PM for live acoustic soul & Ethio-groove by our resident musicians. Reserve your VIP table now.',
                'action_button_text' => '🎸 Book Table',
                'action_button_url' => url('/#reserve'),
                'recipients_count' => 6,
                'status' => 'SENT',
                'sent_by_name' => 'Super Admin',
            ]);
        }
    }
}
