/**
 * Fetches real Premier League & UEFA Champions League fixtures for calendar
 * year 2026 from ESPN's public scoreboard API and writes data/matches-2026.json.
 *
 * Run with: bun data/fetch-fixtures.ts
 *
 * Sources:
 *  - site.api.espn.com soccer scoreboard (eng.1 = Premier League, uefa.champions = UCL)
 *  - Stage labels cross-checked against UEFA.com published matchday calendars.
 */

interface EspnEvent {
  date: string;
  name: string;
  season?: { year: number; slug?: string };
  status: { type: { state: string; completed: boolean; name: string } };
  competitions: Array<{
    venue?: { fullName?: string };
    competitors: Array<{
      homeAway: "home" | "away";
      score?: string;
      team: { displayName: string };
    }>;
  }>;
}

interface EspnResponse {
  events?: EspnEvent[];
}

interface RawMatch {
  competition: "Premier League" | "Champions League";
  season: "2025/26" | "2026/27";
  stage: string;
  kickoff: string; // ISO UTC
  homeTeam: string;
  awayTeam: string;
  venue: string;
  status: "SCHEDULED" | "LIVE" | "FINISHED";
  homeScore: number | null;
  awayScore: number | null;
  featured: boolean;
}

const TEAM_MAP: Record<string, string> = {
  "AFC Bournemouth": "Bournemouth",
  "Brighton and Hove Albion": "Brighton",
  "Brighton & Hove Albion": "Brighton",
  "Manchester City": "Man City",
  "Manchester United": "Man United",
  "Newcastle United": "Newcastle",
  "Tottenham Hotspur": "Tottenham",
  "West Ham United": "West Ham",
  "Wolverhampton Wanderers": "Wolves",
  "Internazionale": "Inter",
  "Inter Milan": "Inter",
  "Paris Saint-Germain": "PSG",
  "Bayern München": "Bayern Munich",
  "Atlético de Madrid": "Atletico Madrid",
  "Atlético Madrid": "Atletico Madrid",
  "PSV Eindhoven": "PSV",
  "Bodo/Glimt": "Bodø/Glimt",
  "F.C. København": "Copenhagen",
  "FC Copenhagen": "Copenhagen",
  "Slavia Praha": "Slavia Prague",
  "SK Slavia Praha": "Slavia Prague",
  "Olympiacos FC": "Olympiacos",
  "AS Monaco": "Monaco",
  "Villarreal CF": "Villarreal",
  "Real Betis Balompié": "Real Betis",
  "Fenerbahçe": "Fenerbahce",
  "Fenerbahce SK": "Fenerbahce",
  "Sporting Clube de Portugal": "Sporting CP",
  "Galatasaray SK": "Galatasaray",
  "FC Porto": "Porto",
  "SL Benfica": "Benfica",
  "AFC Ajax": "Ajax",
  "Club Brugge KV": "Club Brugge",
  "FC Shakhtar Donetsk": "Shakhtar Donetsk",
  "Bayer 04 Leverkusen": "Bayer Leverkusen",
  "Viking FK": "Viking",
  "Sabah FC": "Sabah",
  "AEK Athens FC": "AEK Athens",
  "LASK Linz": "LASK",
  "SSC Napoli": "Napoli",
  "Como 1907": "Como",
  "Chelsea FC": "Chelsea",
  "Arsenal FC": "Arsenal",
  "Liverpool FC": "Liverpool",
  "AS Roma": "Roma",
};

function norm(team: string): string {
  const t = team.trim();
  if (TEAM_MAP[t]) return TEAM_MAP[t];
  const cleaned = t
    .replace(/^F\.?C\.?\s+/, "")
    .replace(/\s+(FC|CF|AFC|SK|JK|IF)$/i, "")
    .trim();
  return cleaned || t;
}

async function fetchWindow(
  league: "eng.1" | "uefa.champions",
  from: string,
  to: string
): Promise<EspnEvent[]> {
  const url = `https://site.api.espn.com/apis/site/v2/sports/soccer/${league}/scoreboard?dates=${from}-${to}`;
  for (let attempt = 0; attempt < 3; attempt++) {
    try {
      // ESPN's CDN accepts curl-style UAs but 403s browser/default UAs
      const res = await fetch(url, { headers: { "User-Agent": "curl/8.5.0" } });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const json = (await res.json()) as EspnResponse;
      return json.events ?? [];
    } catch (err) {
      if (attempt === 2) throw err;
      await new Promise((r) => setTimeout(r, 1500 * (attempt + 1)));
    }
  }
  return [];
}

interface Win {
  league: "eng.1" | "uefa.champions";
  from: string;
  to: string;
}

/** ESPN caps scoreboard responses at 100 events, so slice into 30-day chunks. */
function chunkWindows(league: "eng.1" | "uefa.champions", from: string, to: string): Win[] {
  const chunks: Win[] = [];
  let cursor = new Date(`${from}T00:00:00Z`);
  const end = new Date(`${to}T00:00:00Z`);
  while (cursor < end) {
    const next = new Date(Math.min(cursor.getTime() + 29 * 86400000, end.getTime()));
    const fmt = (d: Date) => d.toISOString().slice(0, 10).replace(/-/g, "");
    chunks.push({ league, from: fmt(cursor), to: fmt(next) });
    cursor = new Date(next.getTime() + 86400000);
  }
  return chunks;
}

const WINDOWS: Win[] = [
  // Premier League 2025/26 — full season (needed to number matchweeks correctly)
  ...chunkWindows("eng.1", "2025-08-01", "2026-06-30"),
  // Premier League 2026/27 — from season start through December 2026
  ...chunkWindows("eng.1", "2026-08-01", "2026-12-31"),
  // Champions League 2025/26 — 2026 part (MD7 league phase + knockout phase)
  ...chunkWindows("uefa.champions", "2026-01-01", "2026-06-30"),
  // Champions League 2026/27 — league phase MD1-6 (Sep-Dec 2026)
  ...chunkWindows("uefa.champions", "2026-09-01", "2026-12-31"),
];

/* ---------------- stage assignment ---------------- */

// UCL matchday date bands (UTC days, inclusive) from the published UEFA calendars.
const UCL_BANDS_2526: Array<{ stage: string; from: string; to: string }> = [
  { stage: "League Phase MD7", from: "2026-01-19", to: "2026-01-22" },
  { stage: "League Phase MD8", from: "2026-01-27", to: "2026-01-29" },
  { stage: "Knockout Play-off — 1st Leg", from: "2026-02-16", to: "2026-02-19" },
  { stage: "Knockout Play-off — 2nd Leg", from: "2026-02-23", to: "2026-02-26" },
  { stage: "Round of 16 — 1st Leg", from: "2026-03-09", to: "2026-03-12" },
  { stage: "Round of 16 — 2nd Leg", from: "2026-03-16", to: "2026-03-19" },
  { stage: "Quarter-final — 1st Leg", from: "2026-04-06", to: "2026-04-09" },
  { stage: "Quarter-final — 2nd Leg", from: "2026-04-13", to: "2026-04-16" },
  { stage: "Semi-final — 1st Leg", from: "2026-04-27", to: "2026-04-30" },
  { stage: "Semi-final — 2nd Leg", from: "2026-05-04", to: "2026-05-07" },
  { stage: "Final", from: "2026-05-29", to: "2026-05-31" },
];

const UCL_BANDS_2627: Array<{ stage: string; from: string; to: string }> = [
  { stage: "League Phase MD1", from: "2026-09-07", to: "2026-09-11" },
  { stage: "League Phase MD2", from: "2026-10-12", to: "2026-10-15" },
  { stage: "League Phase MD3", from: "2026-10-19", to: "2026-10-22" },
  { stage: "League Phase MD4", from: "2026-11-02", to: "2026-11-05" },
  { stage: "League Phase MD5", from: "2026-11-23", to: "2026-11-26" },
  { stage: "League Phase MD6", from: "2026-12-07", to: "2026-12-10" },
];

function uclStage(kickoff: string, season: string): string {
  const day = kickoff.slice(0, 10);
  const bands = season === "2025/26" ? UCL_BANDS_2526 : UCL_BANDS_2627;
  const band = bands.find((b) => day >= b.from && day <= b.to);
  return band ? band.stage : season === "2025/26" ? "Knockout Phase" : "League Phase";
}

/** Season from kickoff date: Aug-Dec -> "Y/Y+1", Jan-Jul -> "Y-1/Y". */
function seasonOf(kickoff: string): "2025/26" | "2026/27" {
  const y = Number(kickoff.slice(0, 4));
  const m = Number(kickoff.slice(5, 7));
  if (m >= 7) return `${y}/${String((y + 1) % 100).padStart(2, "0")}`;
  return `${y - 1}/${String(y % 100).padStart(2, "0")}`;
}

/** Chronological round assignment: a team's Nth match of the season is its
 *  Nth matchweek (bounded 1..38), so postponed games never overflow. */
function assignMatchweeks(
  matches: Array<{ kickoff: string; homeTeam: string; awayTeam: string; stage: string }>
): void {
  const idx = new Map<string, number>();
  for (const m of matches) {
    const h = (idx.get(m.homeTeam) ?? 0) + 1;
    const a = (idx.get(m.awayTeam) ?? 0) + 1;
    idx.set(m.homeTeam, h);
    idx.set(m.awayTeam, a);
    const round = Math.min(38, Math.max(h, a));
    m.stage = `Matchweek ${round}`;
  }
}

/* ---------------- main ---------------- */

async function main() {
  const all: RawMatch[] = [];

  for (const w of WINDOWS) {
    process.stdout.write(`fetching ${w.league} ${w.from}-${w.to} ... `);
    const events = await fetchWindow(w.league, w.from, w.to);
    console.log(`${events.length} events`);

    for (const e of events) {
      const comp = e.competitions?.[0];
      if (!comp) continue;
      const home = comp.competitors?.find((c) => c.homeAway === "home");
      const away = comp.competitors?.find((c) => c.homeAway === "away");
      if (!home || !away) continue;

      const slug = e.season?.slug ?? "";
      const season: "2025/26" | "2026/27" =
        slug.includes("2026-27") || slug.includes("2025-26")
          ? slug.includes("2026-27")
            ? "2026/27"
            : "2025/26"
          : // UCL slugs carry no year ("league-phase") -> derive from date
            seasonOf(e.date);

      const state = e.status?.type?.state;
      const status: RawMatch["status"] =
        state === "post" ? "FINISHED" : state === "in" ? "LIVE" : "SCHEDULED";

      all.push({
        competition: w.league === "eng.1" ? "Premier League" : "Champions League",
        season,
        stage: "",
        kickoff: e.date,
        homeTeam: norm(home.team.displayName),
        awayTeam: norm(away.team.displayName),
        venue: comp.venue?.fullName ?? "",
        status,
        homeScore: status === "FINISHED" && home.score != null ? Number(home.score) : null,
        awayScore: status === "FINISHED" && away.score != null ? Number(away.score) : null,
        featured: false,
      });
    }
    await new Promise((r) => setTimeout(r, 800));
  }

  // Dedupe overlapping fetches
  const seen = new Set<string>();
  const deduped = all.filter((m) => {
    const key = `${m.competition}|${m.kickoff}|${m.homeTeam}|${m.awayTeam}`;
    if (seen.has(key)) return false;
    seen.add(key);
    return true;
  });

  // Stage labels
  const pl2526 = deduped
    .filter((m) => m.competition === "Premier League" && m.season === "2025/26")
    .sort((a, b) => a.kickoff.localeCompare(b.kickoff));
  const pl2627 = deduped
    .filter((m) => m.competition === "Premier League" && m.season === "2026/27")
    .sort((a, b) => a.kickoff.localeCompare(b.kickoff));
  assignMatchweeks(pl2526);
  assignMatchweeks(pl2627);
  for (const m of deduped) {
    if (m.competition === "Champions League") m.stage = uclStage(m.kickoff, m.season);
  }

  // Keep only calendar year 2026
  const y2026 = deduped
    .filter((m) => m.kickoff.slice(0, 4) === "2026")
    .sort((a, b) => a.kickoff.localeCompare(b.kickoff));

  // Featured flags: the UCL final + heavyweight Premier League clashes
  const BIG = new Set(["Arsenal", "Liverpool", "Man City", "Chelsea", "Man United", "Tottenham"]);
  for (const m of y2026) {
    if (m.stage === "Final") m.featured = true;
    else if (m.competition === "Premier League" && BIG.has(m.homeTeam) && BIG.has(m.awayTeam)) {
      const mw = Number(m.stage.replace("Matchweek ", ""));
      if (mw >= 25 || mw <= 2) m.featured = true;
    }
  }

  const byMonth: Record<string, number> = {};
  for (const m of y2026) {
    const k = m.kickoff.slice(0, 7);
    byMonth[k] = (byMonth[k] ?? 0) + 1;
  }
  const finished = y2026.filter((m) => m.status === "FINISHED").length;
  const plMWs = pl2526
    .filter((m) => m.kickoff.slice(0, 4) === "2026")
    .map((m) => Number(m.stage.replace("Matchweek ", "")));
  const pl2526max = plMWs.length ? Math.max(...plMWs) : 0;

  const output = {
    generatedAt: new Date().toISOString(),
    sources: [
      "https://site.api.espn.com/apis/site/v2/sports/soccer/eng.1/scoreboard",
      "https://site.api.espn.com/apis/site/v2/sports/soccer/uefa.champions/scoreboard",
      "https://www.uefa.com/uefachampionsleague/news/02a8-2174c9e9019d-f909a77bd77a-1000--2026-27-champions-league-all-the-league-phase-fixtures",
    ],
    gaps: [] as string[],
    matches: y2026,
  };

  await Bun.write(new URL("./matches-2026.json", import.meta.url), JSON.stringify(output, null, 2));

  console.log("== SUMMARY ==");
  console.log("total 2026 matches:", y2026.length, JSON.stringify(byComp(y2026)));
  console.log("finished:", finished);
  console.log("per month:", JSON.stringify(byMonth));
  console.log("PL 2025/26 max matchweek in 2026:", pl2526max, "(should be 38)");
  console.log(
    "UCL 25/26 finals:",
    JSON.stringify(
      y2026.filter((m) => m.stage === "Final").map((m) => `${m.homeTeam} ${m.homeScore}-${m.awayScore} ${m.awayTeam}`)
    )
  );
  console.log(
    "UCL 26/27 MD1 count:",
    y2026.filter((m) => m.stage === "League Phase MD1").length,
    "(should be 18)"
  );
  // Diagnostics: distinct stages per competition/season
  const stageCounts: Record<string, number> = {};
  for (const m of y2026) {
    const k = `${m.competition} ${m.season} | ${m.stage}`;
    stageCounts[k] = (stageCounts[k] ?? 0) + 1;
  }
  console.log("== STAGE COUNTS ==");
  for (const k of Object.keys(stageCounts).sort()) console.log(` ${k}: ${stageCounts[k]}`);
}

function byComp(matches: RawMatch[]) {
  return {
    PremierLeague: matches.filter((m) => m.competition === "Premier League").length,
    ChampionsLeague: matches.filter((m) => m.competition === "Champions League").length,
  };
}

await main();
