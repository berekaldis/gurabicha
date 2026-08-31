import { NextResponse } from "next/server";
import { db } from "@/lib/db";

export const dynamic = "force-dynamic";

const CATEGORY_ORDER = ["FOOD", "DRINKS", "SHISHA"];

/** GET /api/menu — all menu items ordered by category (FOOD, DRINKS, SHISHA) then name. */
export async function GET() {
  try {
    const items = await db.menuItem.findMany();

    const menu = items.sort((a, b) => {
      const ca = CATEGORY_ORDER.indexOf(a.category);
      const cb = CATEGORY_ORDER.indexOf(b.category);
      if (ca !== cb) return ca - cb;
      return a.name.localeCompare(b.name);
    });

    return NextResponse.json({ menu });
  } catch (error) {
    console.error("GET /api/menu failed:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}
