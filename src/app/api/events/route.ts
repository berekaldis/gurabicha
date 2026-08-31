import { NextResponse } from "next/server";
import { db } from "@/lib/db";

export const dynamic = "force-dynamic";

/** GET /api/events — upcoming events (date >= start of today UTC), sorted by date asc. */
export async function GET() {
  try {
    const now = new Date();
    const startOfTodayUTC = new Date(
      Date.UTC(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate())
    );

    const events = await db.event.findMany({
      where: { date: { gte: startOfTodayUTC } },
      orderBy: { date: "asc" },
    });

    return NextResponse.json({ events });
  } catch (error) {
    console.error("GET /api/events failed:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}
