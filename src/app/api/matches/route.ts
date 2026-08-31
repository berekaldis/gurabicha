import { NextResponse } from "next/server";
import { db } from "@/lib/db";

export const dynamic = "force-dynamic";

/** GET /api/matches — matches with kickoff >= now - 4h, sorted by kickoff asc. */
export async function GET() {
  try {
    const cutoff = new Date(Date.now() - 4 * 60 * 60 * 1000);

    const matches = await db.match.findMany({
      where: { kickoff: { gte: cutoff } },
      orderBy: { kickoff: "asc" },
    });

    return NextResponse.json({ matches });
  } catch (error) {
    console.error("GET /api/matches failed:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}
