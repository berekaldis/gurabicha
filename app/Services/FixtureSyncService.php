<?php

namespace App\Services;

use App\Models\SportMatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FixtureSyncService
{
    /**
     * Common team name normalization map.
     */
    protected const TEAM_MAP = [
        'AFC Bournemouth' => 'Bournemouth',
        'Brighton and Hove Albion' => 'Brighton',
        'Brighton & Hove Albion' => 'Brighton',
        'Manchester City' => 'Man City',
        'Manchester United' => 'Man United',
        'Newcastle United' => 'Newcastle',
        'Tottenham Hotspur' => 'Tottenham',
        'West Ham United' => 'West Ham',
        'Wolverhampton Wanderers' => 'Wolves',
        'Internazionale' => 'Inter',
        'Inter Milan' => 'Inter',
        'Paris Saint-Germain' => 'PSG',
        'Bayern München' => 'Bayern Munich',
        'Atlético de Madrid' => 'Atletico Madrid',
        'Atlético Madrid' => 'Atletico Madrid',
        'PSV Eindhoven' => 'PSV',
        'Bodo/Glimt' => 'Bodø/Glimt',
        'F.C. København' => 'Copenhagen',
        'FC Copenhagen' => 'Copenhagen',
        'Slavia Praha' => 'Slavia Prague',
        'Olympiacos FC' => 'Olympiacos',
        'AS Monaco' => 'Monaco',
        'Villarreal CF' => 'Villarreal',
        'Real Betis Balompié' => 'Real Betis',
        'Fenerbahçe' => 'Fenerbahce',
        'Fenerbahce SK' => 'Fenerbahce',
        'Sporting Clube de Portugal' => 'Sporting CP',
        'Galatasaray SK' => 'Galatasaray',
        'FC Porto' => 'Porto',
        'SL Benfica' => 'Benfica',
        'AFC Ajax' => 'Ajax',
        'Club Brugge KV' => 'Club Brugge',
        'FC Shakhtar Donetsk' => 'Shakhtar Donetsk',
        'Bayer 04 Leverkusen' => 'Bayer Leverkusen',
        'Arsenal FC' => 'Arsenal',
        'Chelsea FC' => 'Chelsea',
        'Liverpool FC' => 'Liverpool',
        'Real Madrid CF' => 'Real Madrid',
        'FC Barcelona' => 'Barcelona',
    ];

    /**
     * Clean and normalize team names.
     */
    public function normalizeTeamName(string $name): string
    {
        $trimmed = trim($name);
        if (isset(self::TEAM_MAP[$trimmed])) {
            return self::TEAM_MAP[$trimmed];
        }

        $cleaned = preg_replace('/^F\.?C\.?\s+/i', '', $trimmed);
        $cleaned = preg_replace('/\s+(FC|CF|AFC|SK|JK|IF)$/i', '', $cleaned);
        $cleaned = trim($cleaned);

        return $cleaned ?: $trimmed;
    }

    /**
     * Fetch real football fixtures from ESPN public scoreboard API over the internet.
     *
     * @param string $league 'eng.1' (Premier League) or 'uefa.champions' (UCL) or 'all'
     * @param string|null $dateRange e.g. '20260801-20261231' or null for current scoreboard
     * @return array
     */
    public function fetchFromInternet(string $league = 'all', ?string $dateRange = null): array
    {
        $leagues = $league === 'all' ? ['eng.1', 'uefa.champions'] : [$league];
        $allEvents = [];
        $seen = [];

        // Build list of dates to query: if dateRange specified, use it;
        // otherwise query active scoreboard plus next 7 days to cover current matchweek & weekend.
        $datesToQuery = [];
        if ($dateRange) {
            $datesToQuery[] = $dateRange;
        } else {
            $datesToQuery[] = null; // null queries current active scoreboard
            $today = Carbon::now('UTC');
            for ($i = 0; $i <= 7; $i++) {
                $datesToQuery[] = $today->copy()->addDays($i)->format('Ymd');
            }
        }

        foreach ($leagues as $l) {
            foreach ($datesToQuery as $dateParam) {
                $url = "https://site.api.espn.com/apis/site/v2/sports/soccer/{$l}/scoreboard";
                if ($dateParam) {
                    $url .= "?dates={$dateParam}";
                }

                try {
                    $response = Http::withoutVerifying()
                        ->timeout(15)
                        ->withHeaders(['User-Agent' => 'curl/8.5.0'])
                        ->get($url);

                    if ($response->successful()) {
                        $json = $response->json();
                        $events = $json['events'] ?? [];
                        foreach ($events as $e) {
                            $id = $e['id'] ?? null;
                            if ($id && isset($seen[$id])) {
                                continue;
                            }
                            if ($id) {
                                $seen[$id] = true;
                            }
                            $e['_league_code'] = $l;
                            $allEvents[] = $e;
                        }
                    } else {
                        Log::warning("FixtureSyncService: HTTP {$response->status()} from {$url}");
                    }
                } catch (\Throwable $e) {
                    Log::error("FixtureSyncService error: " . $e->getMessage());
                }
            }
        }

        return $allEvents;
    }

    /**
     * Synchronize real live fixtures from the internet into the database.
     *
     * @param string $league 'all', 'eng.1', or 'uefa.champions'
     * @param string|null $dateRange Optional date range YYYYMMDD-YYYYMMDD
     * @return array ['synced' => int, 'live' => int, 'errors' => array]
     */
    public function syncLiveFixtures(string $league = 'all', ?string $dateRange = null): array
    {
        $events = $this->fetchFromInternet($league, $dateRange);
        $synced = 0;
        $liveCount = 0;
        $errors = [];

        foreach ($events as $event) {
            try {
                $comp = $event['competitions'][0] ?? null;
                if (!$comp || empty($comp['competitors']) || count($comp['competitors']) < 2) {
                    continue;
                }

                $homeCompetitor = null;
                $awayCompetitor = null;

                foreach ($comp['competitors'] as $c) {
                    if (($c['homeAway'] ?? '') === 'home') {
                        $homeCompetitor = $c;
                    } else {
                        $awayCompetitor = $c;
                    }
                }

                if (!$homeCompetitor || !$awayCompetitor) {
                    $homeCompetitor = $comp['competitors'][0];
                    $awayCompetitor = $comp['competitors'][1];
                }

                $homeTeam = $this->normalizeTeamName($homeCompetitor['team']['displayName'] ?? 'Home Team');
                $awayTeam = $this->normalizeTeamName($awayCompetitor['team']['displayName'] ?? 'Away Team');

                $leagueCode = $event['_league_code'] ?? 'eng.1';
                $competition = $leagueCode === 'uefa.champions' ? 'Champions League' : 'Premier League';

                $kickoff = Carbon::parse($event['date'])->timezone('UTC');
                $venue = $comp['venue']['fullName'] ?? ($comp['venue']['address']['city'] ?? 'Stadium');

                // State normalization from ESPN: 'pre' -> SCHEDULED, 'in' -> LIVE, 'post' -> FINISHED
                $rawState = strtolower($event['status']['type']['state'] ?? 'pre');
                $status = 'SCHEDULED';
                $isLive = false;

                if ($rawState === 'in') {
                    $status = 'LIVE';
                    $isLive = true;
                    $liveCount++;
                } elseif ($rawState === 'post') {
                    $status = 'FINISHED';
                }

                $homeScore = isset($homeCompetitor['score']) && is_numeric($homeCompetitor['score']) ? (int) $homeCompetitor['score'] : null;
                $awayScore = isset($awayCompetitor['score']) && is_numeric($awayCompetitor['score']) ? (int) $awayCompetitor['score'] : null;

                // Matchweek / stage
                $stage = $this->determineStage($kickoff, $competition);

                // Update or create in database
                // Match criteria: Same teams on the same day
                $existing = SportMatch::where('homeTeam', $homeTeam)
                    ->where('awayTeam', $awayTeam)
                    ->whereBetween('kickoff', [
                        $kickoff->copy()->subHours(12),
                        $kickoff->copy()->addHours(12)
                    ])
                    ->first();

                if ($existing) {
                    $existing->update([
                        'competition' => $competition,
                        'venue' => $venue,
                        'kickoff' => $kickoff,
                        'status' => $status,
                        'live' => $isLive,
                        'homeScore' => $homeScore ?? $existing->homeScore,
                        'awayScore' => $awayScore ?? $existing->awayScore,
                        'stage' => $stage,
                    ]);
                } else {
                    SportMatch::create([
                        'id' => (string) Str::uuid(),
                        'homeTeam' => $homeTeam,
                        'awayTeam' => $awayTeam,
                        'competition' => $competition,
                        'season' => '2026/27',
                        'stage' => $stage,
                        'venue' => $venue,
                        'kickoff' => $kickoff,
                        'status' => $status,
                        'live' => $isLive,
                        'homeScore' => $homeScore,
                        'awayScore' => $awayScore,
                        'featured' => false,
                    ]);
                }

                $synced++;
            } catch (\Throwable $e) {
                $errors[] = $e->getMessage();
            }
        }

        return [
            'synced' => $synced,
            'live' => $liveCount,
            'total_fetched' => count($events),
            'errors' => $errors,
        ];
    }

    /**
     * Compute stage/matchweek from kickoff date.
     */
    protected function determineStage(Carbon $kickoff, string $competition): string
    {
        if ($competition === 'Champions League') {
            $month = $kickoff->month;
            if ($month >= 9 && $month <= 12) {
                return 'League Phase';
            } elseif ($month === 2) {
                return 'Knockout Play-off';
            } elseif ($month === 3) {
                return 'Round of 16';
            } elseif ($month === 4) {
                return 'Quarter-final';
            } elseif ($month === 5) {
                return 'Semi-final';
            }
            return 'Champions League';
        }

        // Premier League: estimate matchweek by month
        $month = $kickoff->month;
        if ($month === 8) return 'Matchweek 1-3';
        if ($month === 9) return 'Matchweek 4-7';
        if ($month === 10) return 'Matchweek 8-10';
        if ($month === 11) return 'Matchweek 11-14';
        if ($month === 12) return 'Matchweek 15-20 (Festive)';
        if ($month === 1) return 'Matchweek 21-24';
        if ($month === 2) return 'Matchweek 25-27';
        if ($month === 3) return 'Matchweek 28-30';
        if ($month === 4) return 'Matchweek 31-34';
        if ($month === 5) return 'Matchweek 35-38 (Finale)';

        return 'Premier League Matchday';
    }
}
