import { NextResponse } from "next/server";
import { db } from "@/lib/db";

export const dynamic = "force-dynamic";

const DAY_MS = 24 * 60 * 60 * 1000;

/** UTC-midnight Date for a YYYY-MM-DD string (returns null when invalid). */
function parseUTCDate(dateStr: string): Date | null {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return null;
  const parsed = new Date(`${dateStr}T00:00:00.000Z`);
  if (Number.isNaN(parsed.getTime())) return null;
  if (parsed.toISOString().slice(0, 10) !== dateStr) return null;
  return parsed;
}

/**
 * GET /api/matches                      -> upcoming matches (kickoff >= now - 4h), asc, max 40
 * GET /api/matches?year=2026            -> every match in calendar year 2026, asc (fixtures calendar)
 * GET /api/matches?date=YYYY-MM-DD      -> every match on that UTC calendar day, asc
 */
export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url);
    const yearParam = searchParams.get("year");
    const dateParam = searchParams.get("date");

    if (yearParam !== null) {
      if (!/^\d{4}$/.test(yearParam)) {
        return NextResponse.json({ error: "Invalid year. Must be a 4-digit year." }, { status: 400 });
      }
      const start = new Date(Date.UTC(Number(yearParam), 0, 1));
      const end = new Date(Date.UTC(Number(yearParam) + 1, 0, 1));
      const matches = await db.match.findMany({
        where: { kickoff: { gte: start, lt: end } },
        orderBy: { kickoff: "asc" },
      });
      return NextResponse.json({ matches });
    }

    if (dateParam !== null) {
      const day = parseUTCDate(dateParam);
      if (!day) {
        return NextResponse.json(
          { error: "Invalid date. Must be a real calendar date in YYYY-MM-DD format." },
          { status: 400 }
        );
      }
      const matches = await db.match.findMany({
        where: { kickoff: { gte: day, lt: new Date(day.getTime() + DAY_MS) } },
        orderBy: { kickoff: "asc" },
      });
      return NextResponse.json({ matches });
    }

    const cutoff = new Date(Date.now() - 4 * 60 * 60 * 1000);
    const matches = await db.match.findMany({
      where: { kickoff: { gte: cutoff } },
      orderBy: { kickoff: "asc" },
      take: 40,
    });
    return NextResponse.json({ matches });
  } catch (error) {
    console.error("GET /api/matches failed:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}
