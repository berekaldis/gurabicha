<?php

namespace App\Console\Commands;

use App\Models\SportMatch;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RefreshFixtures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixtures:refresh {--wipe : Wipe existing matches before re-importing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh and import Premier League & Champions League matches from data/matches-2026.json';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = base_path('data/matches-2026.json');
        if (!File::exists($path)) {
            $this->error("Matches file not found at: {$path}");
            return Command::FAILURE;
        }

        $json = json_decode(File::get($path), true);
        $matches = $json['matches'] ?? [];

        if (empty($matches)) {
            $this->error('No matches found in data/matches-2026.json');
            return Command::FAILURE;
        }

        if ($this->option('wipe')) {
            $this->warn('Wiping existing matches...');
            SportMatch::truncate();
        }

        $this->info("Importing " . count($matches) . " matches into database...");
        $bar = $this->output->createProgressBar(count($matches));
        $bar->start();

        foreach (array_chunk($matches, 100) as $chunk) {
            $insertData = [];
            foreach ($chunk as $m) {
                $insertData[] = [
                    'id' => (string) Str::uuid(),
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
            $bar->advance(count($chunk));
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully loaded " . SportMatch::count() . " matches.");

        return Command::SUCCESS;
    }
}
