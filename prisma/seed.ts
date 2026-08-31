/**
 * Seed script for Gura Bicha Sports Bar.
 * Run with: bun prisma/seed.ts
 *
 * Idempotent: wipes all models first, then recreates.
 * Dates for events are computed RELATIVE to "now" so data is always fresh.
 * Football fixtures are the REAL Premier League & Champions League schedules
 * for calendar year 2026, fetched from ESPN's public scoreboard API into
 * data/matches-2026.json (re-fetch anytime with: bun data/fetch-fixtures.ts).
 * Venue timezone: Africa/Addis_Ababa (UTC+3). Calendar dates are stored as
 * UTC midnights: new Date(Date.UTC(y, m, d)).
 */

import { readFileSync } from "node:fs";
import { PrismaClient } from "@prisma/client";

const db = new PrismaClient();

/** One match row inside data/matches-2026.json. */
interface SeededMatch {
  competition: "Premier League" | "Champions League";
  season: string;
  stage: string;
  kickoff: string; // ISO UTC, e.g. "2026-01-01T17:30Z"
  homeTeam: string;
  awayTeam: string;
  venue: string;
  status: "SCHEDULED" | "LIVE" | "FINISHED";
  homeScore: number | null;
  awayScore: number | null;
  featured: boolean;
}

function loadFixtures(): SeededMatch[] {
  const url = new URL("../data/matches-2026.json", import.meta.url);
  const raw = JSON.parse(readFileSync(url, "utf8")) as { matches: SeededMatch[] };
  if (!Array.isArray(raw.matches) || raw.matches.length === 0) {
    throw new Error("data/matches-2026.json is empty — run: bun data/fetch-fixtures.ts");
  }
  return raw.matches;
}

/* ---------------- date helpers (all UTC) ---------------- */

function startOfTodayUTC(): Date {
  const now = new Date();
  return new Date(
    Date.UTC(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate())
  );
}

function addDaysUTC(date: Date, days: number): Date {
  return new Date(date.getTime() + days * 24 * 60 * 60 * 1000);
}

/** Starting from `start`, collect the first `count` dates whose UTC weekday is `weekday`. */
function upcomingWeekdaysUTC(start: Date, weekday: number, count: number): Date[] {
  const out: Date[] = [];
  let cursor = new Date(start.getTime());
  while (out.length < count) {
    if (cursor.getUTCDay() === weekday) out.push(new Date(cursor.getTime()));
    cursor = addDaysUTC(cursor, 1);
  }
  return out;
}

/** UTC midnight Date for a given Date's calendar day. */
function utcMidnight(date: Date): Date {
  return new Date(
    Date.UTC(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate())
  );
}

/* ---------------- main ---------------- */

async function main() {
  // 1. Wipe everything (order irrelevant — no FK relations), keeps reruns idempotent.
  await db.reservation.deleteMany();
  await db.menuItem.deleteMany();
  await db.venueTable.deleteMany();
  await db.match.deleteMany();
  await db.event.deleteMany();

  const today = startOfTodayUTC();
  const tomorrow = addDaysUTC(today, 1);

  // 2. Venue tables
  const tables = [
    { code: "T1", zone: "Big Screen View", capacity: 4, seats: "2–4 people" },
    { code: "T2", zone: "Center Floor", capacity: 6, seats: "4–6 people" },
    { code: "T3", zone: "Big Screen View", capacity: 4, seats: "2–4 people" },
    { code: "T4", zone: "Near Stage", capacity: 6, seats: "4–6 people" },
    { code: "T5", zone: "VIP Lounge", capacity: 8, seats: "6–8 people" },
    { code: "T6", zone: "VIP Lounge", capacity: 8, seats: "6–8 people" },
  ];
  for (const t of tables) await db.venueTable.create({ data: t });

  // 3. Events — next 4 Thursdays (starting from tomorrow, getUTCDay() === 4)
  const thursdays = upcomingWeekdaysUTC(tomorrow, 4, 4);
  for (const thursday of thursdays) {
    await db.event.create({
      data: {
        title: "Game & Groove",
        description:
          "Live acoustic sessions, great food and every big match on screen. Thursday nights done right at Gura Bicha.",
        category: "Acoustic Thursdays",
        date: utcMidnight(thursday),
        startTime: "12:30 PM",
        endTime: "3:30 PM",
        imageUrl: "/images/event-acoustic.jpg",
        featured: utcMidnight(thursday).getTime() === utcMidnight(thursdays[0]).getTime(),
      },
    });
  }

  // Plus one event on the nearest upcoming Wednesday (getUTCDay() === 3)
  const [wednesday] = upcomingWeekdaysUTC(tomorrow, 3, 1);
  await db.event.create({
    data: {
      title: "Champions League Night",
      description:
        "Every Champions League fixture live on our giant screens with match-day food & drink specials.",
      category: "Big Match",
      date: utcMidnight(wednesday),
      startTime: "7:00 PM",
      endTime: "11:00 PM",
      imageUrl: "/images/event-sports.jpg",
      featured: false,
    },
  });

  // 4. Matches — real Premier League & Champions League schedules for 2026
  const fixtures = loadFixtures();
  await db.match.createMany({
    data: fixtures.map((m) => ({
      homeTeam: m.homeTeam,
      awayTeam: m.awayTeam,
      competition: m.competition,
      season: m.season,
      stage: m.stage,
      venue: m.venue === "" ? null : m.venue,
      kickoff: new Date(m.kickoff),
      status: m.status,
      homeScore: m.homeScore,
      awayScore: m.awayScore,
      featured: m.featured,
      live: false, // liveness is derived from kickoff time client-side
    })),
  });

  // 5. Menu (prices in ETB)
  const menu: Array<{
    name: string;
    category: string;
    price: number;
    description: string;
    popular: boolean;
  }> = [
    // FOOD
    {
      name: "Gored Gored",
      category: "FOOD",
      price: 550,
      popular: true,
      description:
        "Cubes of prime beef flash-seared in spiced butter with awaze and fresh rosemary.",
    },
    {
      name: "Kitfo Special",
      category: "FOOD",
      price: 650,
      popular: true,
      description:
        "Hand-minced prime beef kissed with mitmita and kibbeh, served with ayib and gomen.",
    },
    {
      name: "Shekla Tibs",
      category: "FOOD",
      price: 600,
      popular: true,
      description:
        "Sizzling beef tibs served bubbling in a traditional clay shekla with onions and peppers.",
    },
    {
      name: "Doro Wot",
      category: "FOOD",
      price: 580,
      popular: false,
      description:
        "Slow-simmered chicken stew rich with berbere, served with boiled egg and fresh injera.",
    },
    {
      name: "Firfir",
      category: "FOOD",
      price: 450,
      popular: false,
      description:
        "Shredded injera tossed through a deeply spiced berbere sauce — pure Ethiopian comfort.",
    },
    {
      name: "Shiro Wot",
      category: "FOOD",
      price: 350,
      popular: false,
      description:
        "Silky ground-chickpea shiro simmered low with garlic and fragrant spiced butter.",
    },
    {
      name: "Gura Bicha Burger",
      category: "FOOD",
      price: 480,
      popular: true,
      description:
        "Juicy double-patty burger stacked with smoked cheese, crispy onions and house sauce.",
    },
    {
      name: "Crispy Chicken Wings",
      category: "FOOD",
      price: 520,
      popular: false,
      description:
        "Golden-fried wings glazed in your choice of BBQ, buffalo or honey-garlic.",
    },
    {
      name: "Loaded Fries",
      category: "FOOD",
      price: 320,
      popular: false,
      description:
        "Crispy fries piled high with cheese sauce, spiced beef and pickled jalapeños.",
    },
    // DRINKS
    {
      name: "Draft Beer (Meta)",
      category: "DRINKS",
      price: 180,
      popular: true,
      description: "Ice-cold Meta draft, poured fresh from tap all night long.",
    },
    {
      name: "Macchiato",
      category: "DRINKS",
      price: 90,
      popular: true,
      description:
        "Rich Ethiopian espresso topped with velvety steamed milk — the Addis classic.",
    },
    {
      name: "Traditional Tej",
      category: "DRINKS",
      price: 150,
      popular: false,
      description:
        "House-brewed honey wine served in a berele — sweet, golden and dangerously smooth.",
    },
    {
      name: "Fresh Mango Juice",
      category: "DRINKS",
      price: 140,
      popular: false,
      description:
        "Freshly blended ripe mangoes — thick, tropical and refreshing.",
    },
    {
      name: "Soft Drink",
      category: "DRINKS",
      price: 70,
      popular: false,
      description: "Chilled classics served over ice: Coke, Sprite, Fanta and more.",
    },
    {
      name: "Espresso",
      category: "DRINKS",
      price: 60,
      popular: false,
      description: "A bold double shot of premium Ethiopian coffee beans.",
    },
    // SHISHA
    {
      name: "Mint",
      category: "SHISHA",
      price: 300,
      popular: false,
      description: "Cool, crisp mint clouds — the timeless crowd-pleaser.",
    },
    {
      name: "Double Apple",
      category: "SHISHA",
      price: 320,
      popular: false,
      description: "Sweet anise-kissed apple flavor, smooth and aromatic.",
    },
    {
      name: "Grape Mint",
      category: "SHISHA",
      price: 320,
      popular: false,
      description: "Juicy grape fused with an icy mint finish. A house favorite.",
    },
  ];
  for (const item of menu) await db.menuItem.create({ data: item });

  const counts = {
    events: await db.event.count(),
    matches: await db.match.count(),
    tables: await db.venueTable.count(),
    menuItems: await db.menuItem.count(),
    reservations: await db.reservation.count(),
  };
  console.log("Seed complete:", counts);
  console.log(
    "Thursdays:",
    thursdays.map((d) => d.toISOString().slice(0, 10)).join(", ")
  );
  console.log("Wednesday event:", wednesday.toISOString().slice(0, 10));
}

main()
  .then(() => db.$disconnect())
  .catch(async (e) => {
    console.error(e);
    await db.$disconnect();
    process.exit(1);
  });
