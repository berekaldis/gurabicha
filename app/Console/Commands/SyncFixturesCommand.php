<?php

namespace App\Console\Commands;

use App\Services\FixtureSyncService;
use Illuminate\Console\Command;

class SyncFixturesCommand extends Command
{
    protected $signature = 'fixtures:sync 
                            {--league=all : Competition to sync (all, eng.1, uefa.champions)}
                            {--dates= : Optional date window YYYYMMDD-YYYYMMDD}';

    protected $description = 'Track and sync exact real football fixtures and live scores directly from the internet';

    public function handle(FixtureSyncService $service): int
    {
        $league = $this->option('league') ?: 'all';
        $dates = $this->option('dates');

        $this->info("Connecting to real-time internet football scoreboard API ({$league})...");
        
        $result = $service->syncLiveFixtures($league, $dates);

        $this->info("Sync completed successfully!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Fetched from Internet', $result['total_fetched']],
                ['Matches Synced / Updated', $result['synced']],
                ['Currently Live Matches', $result['live']],
                ['Errors Encountered', count($result['errors'])],
            ]
        );

        if (!empty($result['errors'])) {
            $this->warn("Warnings / Errors:");
            foreach (array_slice($result['errors'], 0, 5) as $err) {
                $this->line(" - {$err}");
            }
        }

        return Command::SUCCESS;
    }
}
