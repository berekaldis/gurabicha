<?php

namespace Database\Seeders;

use App\Models\SportMatch;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MatchSeeder extends Seeder
{
    public function run(): void
    {
        SportMatch::truncate();

        $path = base_path('data/matches-2026.json');
        if (!File::exists($path)) {
            $this->command->error("matches-2026.json not found at {$path}");
            return;
        }

        $json = json_decode(File::get($path), true);
        $matches = $json['matches'] ?? [];

        foreach (array_chunk($matches, 100) as $chunk) {
            $insertData = [];
            foreach ($chunk as $m) {
                $insertData[] = [
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'homeTeam' => $m['homeTeam'],
                    'awayTeam' => $m['awayTeam'],
                    'competition' => $m['competition'],
                    'season' => $m['season'] ?? '',
                    'stage' => $m['stage'] ?? '',
                    'venue' => !empty($m['venue']) ? $m['venue'] : null,
                    'kickoff' => Carbon::parse($m['kickoff'])->toDateTimeString(),
                    'status' => $m['status'] ?? 'SCHEDULED',
                    'homeScore' => $m['homeScore'] ?? null,
                    'awayScore' => $m['awayScore'] ?? null,
                    'featured' => (bool) ($m['featured'] ?? false),
                    'live' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            SportMatch::insert($insertData);
        }
    }
}
