import { NextResponse } from "next/server";
import { db } from "@/lib/db";
import { verifyTelegramInitData } from "@/lib/telegram-server";

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

/** Keeps digits only — used to normalize phone numbers for storage/lookup. */
function normalizePhone(phone: string): string {
  return phone.replace(/\D+/g, "");
}

/** "GB-" + 4 random digits. */
function generateCode(): string {
  return `GB-${Math.floor(Math.random() * 10000)
    .toString()
    .padStart(4, "0")}`;
}

async function generateUniqueCode(): Promise<string> {
  for (let attempt = 0; attempt < 50; attempt++) {
    const candidate = generateCode();
    const exists = await db.reservation.findUnique({
      where: { code: candidate },
      select: { id: true },
    });
    if (!exists) return candidate;
  }
  throw new Error("Could not generate a unique reservation code");
}

/**
 * GET /api/reservations?phone=X     -> lookup by phone (normalized, startsWith)
 * GET /api/reservations?mine=1      -> lookup by Telegram identity (x-telegram-init-data header)
 */
export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url);
    const phoneParam = searchParams.get("phone");
    const mineParam = searchParams.get("mine");

    if (mineParam === "1") {
      const tgUser = verifyTelegramInitData(request.headers.get("x-telegram-init-data"));
      if (!tgUser) {
        return NextResponse.json(
          { error: "Telegram identity required. Open this app from Telegram." },
          { status: 401 }
        );
      }
      const reservations = await db.reservation.findMany({
        where: { telegramUserId: String(tgUser.id) },
        orderBy: { createdAt: "desc" },
        take: 20,
      });
      return NextResponse.json({ reservations });
    }

    if (!phoneParam || phoneParam.trim() === "") {
      return NextResponse.json(
        { error: "Missing required query param: phone (or mine=1 with Telegram header)" },
        { status: 400 }
      );
    }

    const normalized = normalizePhone(phoneParam);
    if (normalized === "") {
      return NextResponse.json(
        { error: "Invalid phone number." },
        { status: 400 }
      );
    }

    const reservations = await db.reservation.findMany({
      where: { phone: { startsWith: normalized } },
      orderBy: { createdAt: "desc" },
      take: 20,
    });

    return NextResponse.json({ reservations });
  } catch (error) {
    console.error("GET /api/reservations failed:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}

/**
 * POST /api/reservations
 * Body: { date, slot, tableCode, guests, name, phone, email?, request?, matchId? }
 * Validates capacity, double-booking (409) and more; binds the Telegram user
 * from the x-telegram-init-data header when present.
 */
export async function POST(request: Request) {
  try {
    let body: unknown;
    try {
      body = await request.json();
    } catch {
      return NextResponse.json({ error: "Invalid JSON body." }, { status: 400 });
    }

    const data = (body ?? {}) as Record<string, unknown>;

    /* ---- telegram identity ---- */
    const tgHeader = request.headers.get("x-telegram-init-data");
    let telegramUserId: string | null = null;
    let telegramUsername: string | null = null;
    if (tgHeader) {
      const tgUser = verifyTelegramInitData(tgHeader);
      if (!tgUser) {
        return NextResponse.json(
          { error: "Telegram identity verification failed. Please reopen the Mini App." },
          { status: 401 }
        );
      }
      telegramUserId = String(tgUser.id);
      telegramUsername = tgUser.username ?? null;
    }

    /* ---- date ---- */
    const dateStr = typeof data.date === "string" ? data.date : "";
    const date = parseUTCDate(dateStr);
    if (!date) {
      return NextResponse.json(
        { error: "Invalid date. Must be a real calendar date in YYYY-MM-DD format." },
        { status: 400 }
      );
    }

    /* ---- not in the past (compare UTC calendar dates) ---- */
    const now = new Date();
    const todayStr = new Date(
      Date.UTC(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate())
    )
      .toISOString()
      .slice(0, 10);
    if (dateStr < todayStr) {
      return NextResponse.json(
        { error: "Cannot book a date in the past." },
        { status: 400 }
      );
    }

    /* ---- slot ---- */
    const slot = typeof data.slot === "string" ? data.slot : "";
    if (slot !== "LUNCH" && slot !== "EVENING") {
      return NextResponse.json(
        { error: "Invalid slot. Must be LUNCH or EVENING." },
        { status: 400 }
      );
    }

    /* ---- table ---- */
    const tableCode =
      typeof data.tableCode === "string" ? data.tableCode.trim() : "";
    if (!tableCode) {
      return NextResponse.json({ error: "tableCode is required." }, { status: 400 });
    }
    const table = await db.venueTable.findUnique({ where: { code: tableCode } });
    if (!table) {
      return NextResponse.json(
        { error: `Table ${tableCode} does not exist.` },
        { status: 400 }
      );
    }

    /* ---- guests ---- */
    const guests = data.guests;
    if (typeof guests !== "number" || !Number.isInteger(guests) || guests < 1 || guests > 12) {
      return NextResponse.json(
        { error: "guests must be an integer between 1 and 12." },
        { status: 400 }
      );
    }
    if (guests > table.capacity) {
      return NextResponse.json(
        { error: `Table ${tableCode} seats at most ${table.capacity} guests.` },
        { status: 400 }
      );
    }

    /* ---- name ---- */
    const name = typeof data.name === "string" ? data.name.trim() : "";
    if (!name) {
      return NextResponse.json({ error: "Name is required." }, { status: 400 });
    }

    /* ---- phone ---- */
    const phoneRaw = typeof data.phone === "string" ? data.phone.trim() : "";
    if (!phoneRaw || phoneRaw.length < 7) {
      return NextResponse.json(
        { error: "Phone number is required (min 7 characters)." },
        { status: 400 }
      );
    }
    const phone = normalizePhone(phoneRaw);

    /* ---- match (optional) ---- */
    let matchId: string | null = null;
    let matchLabel: string | null = null;
    if (typeof data.matchId === "string" && data.matchId.trim() !== "") {
      const match = await db.match.findUnique({ where: { id: data.matchId.trim() } });
      if (!match) {
        return NextResponse.json({ error: "Unknown match selected." }, { status: 400 });
      }
      // The reservation must be for the same day the match kicks off.
      const kickoffDay = new Date(
        Date.UTC(
          match.kickoff.getUTCFullYear(),
          match.kickoff.getUTCMonth(),
          match.kickoff.getUTCDate()
        )
      );
      if (kickoffDay.getTime() !== date.getTime()) {
        return NextResponse.json(
          { error: "Selected match does not kick off on the chosen date." },
          { status: 400 }
        );
      }
      matchId = match.id;
      matchLabel = `${match.homeTeam} vs ${match.awayTeam} · ${match.competition} — ${match.stage}`;
    }

    /* ---- availability (409) ---- */
    const conflict = await db.reservation.findFirst({
      where: { date, slot, tableCode, status: "CONFIRMED" },
      select: { id: true },
    });
    if (conflict) {
      return NextResponse.json(
        {
          error: `Table ${tableCode} is no longer available for that slot. Please pick another.`,
        },
        { status: 409 }
      );
    }

    /* ---- create ---- */
    const code = await generateUniqueCode();
    const email =
      typeof data.email === "string" && data.email.trim() !== ""
        ? data.email.trim()
        : null;
    const reservationRequest =
      typeof data.request === "string" && data.request.trim() !== ""
        ? data.request.trim()
        : null;

    const reservation = await db.reservation.create({
      data: {
        code,
        date: new Date(dateStr),
        slot,
        tableCode,
        guests,
        name,
        phone,
        email,
        request: reservationRequest,
        matchId,
        matchLabel,
        telegramUserId,
        telegramUsername,
        status: "CONFIRMED",
      },
    });

    return NextResponse.json({ reservation }, { status: 201 });
  } catch (error) {
    console.error("POST /api/reservations failed:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}
