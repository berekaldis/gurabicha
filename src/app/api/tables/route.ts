import { NextResponse } from "next/server";
import { db } from "@/lib/db";

export const dynamic = "force-dynamic";

/** Parses a YYYY-MM-DD string as UTC midnight and verifies it is a real calendar date. */
function parseUTCDate(dateStr: string): Date | null {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return null;
  const parsed = new Date(`${dateStr}T00:00:00.000Z`);
  if (Number.isNaN(parsed.getTime())) return null;
  // Round-trip check catches rollovers like 2025-02-30 -> 2025-03-02.
  if (parsed.toISOString().slice(0, 10) !== dateStr) return null;
  return parsed;
}

/**
 * GET /api/tables?date=YYYY-MM-DD&slot=LUNCH|EVENING
 * Returns every table with an `available` flag (false when a CONFIRMED
 * reservation exists for the same UTC calendar date + slot + tableCode),
 * plus `remaining` = count of available tables.
 */
export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url);
    const dateStr = searchParams.get("date");
    const slot = searchParams.get("slot");

    if (!dateStr) {
      return NextResponse.json(
        { error: "Missing required query param: date" },
        { status: 400 }
      );
    }
    if (!slot) {
      return NextResponse.json(
        { error: "Missing required query param: slot" },
        { status: 400 }
      );
    }
    if (slot !== "LUNCH" && slot !== "EVENING") {
      return NextResponse.json(
        { error: "Invalid slot. Must be LUNCH or EVENING." },
        { status: 400 }
      );
    }

    const date = parseUTCDate(dateStr);
    if (!date) {
      return NextResponse.json(
        { error: "Invalid date. Must be a real calendar date in YYYY-MM-DD format." },
        { status: 400 }
      );
    }

    const [tables, confirmed] = await Promise.all([
      db.venueTable.findMany({ orderBy: { code: "asc" } }),
      db.reservation.findMany({
        where: { date, slot, status: "CONFIRMED" },
        select: { tableCode: true },
      }),
    ]);

    const taken = new Set(confirmed.map((r) => r.tableCode));

    const result = tables.map((t) => ({
      code: t.code,
      zone: t.zone,
      capacity: t.capacity,
      seats: t.seats,
      available: !taken.has(t.code),
    }));

    return NextResponse.json({
      tables: result,
      remaining: result.filter((t) => t.available).length,
    });
  } catch (error) {
    console.error("GET /api/tables failed:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}
