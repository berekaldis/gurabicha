import { NextResponse } from "next/server";
import { db } from "@/lib/db";

export const dynamic = "force-dynamic";

/** DELETE /api/reservations/[id] — soft cancel: sets status to CANCELLED. */
export async function DELETE(
  _request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;

    const existing = await db.reservation.findUnique({ where: { id } });
    if (!existing) {
      return NextResponse.json(
        { error: "Reservation not found" },
        { status: 404 }
      );
    }

    const reservation = await db.reservation.update({
      where: { id },
      data: { status: "CANCELLED" },
    });

    return NextResponse.json({ reservation });
  } catch (error) {
    console.error("DELETE /api/reservations/[id] failed:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}
