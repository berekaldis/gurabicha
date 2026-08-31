"use client";

import { Ticket } from "lucide-react";
import { useCallback, useEffect, useState } from "react";
import { toast } from "sonner";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import { gbApi } from "@/lib/gb/api";
import { SLOT_META, formatDayLong, type GbReservation, type SavedDetails } from "@/lib/gb/types";
import { GbErrorBox, GbSkeleton } from "./gb-ui";

function ReservationCard({
  r,
  onCancel,
}: {
  r: GbReservation;
  onCancel: (r: GbReservation) => void;
}) {
  const cancelled = r.status === "CANCELLED";
  return (
    <li
      className={`rounded-xl border bg-panel p-4 ${
        cancelled ? "border-white/[0.07] opacity-60" : "border-gold/25"
      }`}
    >
      <div className="flex items-center justify-between">
        <strong className="font-display text-lg tracking-wider text-gold">{r.code}</strong>
        <span
          className={`rounded-full border px-2 py-0.5 text-[9px] font-extrabold tracking-wider ${
            cancelled ? "border-white/15 text-white/40" : "border-gold/40 bg-gold/15 text-gold"
          }`}
        >
          {r.status}
        </span>
      </div>
      <dl className="mt-3 space-y-1.5 text-[12px]">
        <div className="flex justify-between">
          <dt className="text-white/45">Date</dt>
          <dd className="font-semibold text-white">{formatDayLong(r.date)}</dd>
        </div>
        <div className="flex justify-between">
          <dt className="text-white/45">Session</dt>
          <dd className="font-semibold text-white">{SLOT_META[r.slot].time}</dd>
        </div>
        <div className="flex justify-between">
          <dt className="text-white/45">Table / Party</dt>
          <dd className="font-semibold text-white">
            {r.tableCode} · {r.guests} guests
          </dd>
        </div>
        <div className="flex justify-between">
          <dt className="text-white/45">Name</dt>
          <dd className="font-semibold text-white">{r.name}</dd>
        </div>
      </dl>
      {r.request && <p className="mt-2.5 border-t border-white/[0.06] pt-2.5 text-[11px] italic text-white/40">“{r.request}”</p>}
      {!cancelled && (
        <button
          type="button"
          onClick={() => onCancel(r)}
          className="mt-3.5 w-full rounded-[10px] border border-red-500/30 py-2.5 text-[11px] font-bold tracking-wider text-red-400 transition hover:bg-red-500/10"
        >
          CANCEL RESERVATION
        </button>
      )}
    </li>
  );
}

export function GbReservations({
  savedDetails,
  onReserve,
}: {
  savedDetails: SavedDetails | null;
  onReserve: () => void;
}) {
  const [phone, setPhone] = useState("");
  const [searchedPhone, setSearchedPhone] = useState<string | null>(null);
  const [reservations, setReservations] = useState<GbReservation[] | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [cancelTarget, setCancelTarget] = useState<GbReservation | null>(null);
  const [cancelling, setCancelling] = useState(false);

  const lookup = useCallback(async (rawPhone: string) => {
    const normalized = rawPhone.replace(/\D/g, "");
    if (normalized.length < 7) {
      toast.error("Enter the phone number you reserved with (e.g. +251 911 234 567).");
      return;
    }
    setLoading(true);
    setError(null);
    setSearchedPhone(rawPhone.trim());
    try {
      const res = await gbApi.reservationsByPhone(rawPhone.trim());
      setReservations(res.reservations);
    } catch (err) {
      setError((err as Error).message);
      setReservations(null);
    } finally {
      setLoading(false);
    }
  }, []);

  // Pre-fill and auto-search when details were saved on a previous booking.
  useEffect(() => {
    if (savedDetails?.phone) {
      setPhone(savedDetails.phone);
      void lookup(savedDetails.phone);
    }
  }, [savedDetails, lookup]);

  const confirmCancel = async () => {
    if (!cancelTarget) return;
    setCancelling(true);
    try {
      const res = await gbApi.cancelReservation(cancelTarget.id);
      setReservations((prev) =>
        prev ? prev.map((r) => (r.id === res.reservation.id ? res.reservation : r)) : prev
      );
      toast.success(`Reservation ${res.reservation.code} cancelled`);
    } catch (err) {
      toast.error((err as Error).message);
    } finally {
      setCancelling(false);
      setCancelTarget(null);
    }
  };

  return (
    <div className="px-5 pb-9 pt-6">
      <h1 className="font-display text-2xl uppercase text-white">My Reservations</h1>
      <p className="mt-1 text-[12px] text-white/45">Look up your bookings with the phone number you reserved with.</p>

      <form
        className="mt-4 flex gap-2"
        onSubmit={(e) => {
          e.preventDefault();
          void lookup(phone);
        }}
      >
        <input
          type="tel"
          value={phone}
          onChange={(e) => setPhone(e.target.value)}
          placeholder="+251 ..."
          aria-label="Phone number used for the reservation"
          className="min-w-0 flex-1 rounded-[10px] border border-gold/25 bg-panel px-3.5 py-3.5 text-[13px] text-white outline-none transition placeholder:text-white/25 focus:border-gold"
        />
        <button
          type="submit"
          disabled={loading}
          className="shrink-0 rounded-[10px] bg-gradient-to-br from-gold-light to-gold px-5 text-[12px] font-extrabold tracking-wider text-[#080808] transition hover:brightness-110 disabled:opacity-50"
        >
          {loading ? "…" : "FIND"}
        </button>
      </form>

      <div className="mt-5 space-y-3">
        {error && <GbErrorBox message={error} onRetry={() => void lookup(phone)} />}

        {loading && reservations === null && !error && (
          <>
            <GbSkeleton className="h-[170px] w-full" />
            <GbSkeleton className="h-[170px] w-full" />
          </>
        )}

        {!loading && reservations === null && !error && (
          <div className="rounded-xl border border-white/[0.08] bg-panel p-8 text-center">
            <Ticket className="mx-auto mb-2 h-7 w-7 text-gold/60" aria-hidden />
            <p className="text-[12px] leading-relaxed text-white/50">
              Enter your phone number above to see your upcoming bookings.
            </p>
          </div>
        )}

        {reservations !== null && reservations.length === 0 && (
          <div className="rounded-xl border border-white/[0.08] bg-panel p-8 text-center">
            <p className="text-[12px] text-white/50">
              No reservations found for <span className="text-white">{searchedPhone}</span>.
            </p>
            <button
              type="button"
              onClick={onReserve}
              className="mt-3 text-[11px] font-bold tracking-wider text-gold hover:text-gold-light"
            >
              MAKE A RESERVATION
            </button>
          </div>
        )}

        {reservations !== null && reservations.length > 0 && (
          <>
            <p className="text-[10px] font-bold tracking-[0.2em] text-white/35">
              {reservations.length} {reservations.length === 1 ? "BOOKING" : "BOOKINGS"} FOUND
            </p>
            <ul className="space-y-3">
              {reservations.map((r) => (
                <ReservationCard key={r.id} r={r} onCancel={setCancelTarget} />
              ))}
            </ul>
          </>
        )}
      </div>

      <AlertDialog open={cancelTarget !== null} onOpenChange={(open) => !open && setCancelTarget(null)}>
        <AlertDialogContent className="border-gold/25 bg-[#0b0b0b]">
          <AlertDialogHeader>
            <AlertDialogTitle className="font-display uppercase text-white">
              Cancel this reservation?
            </AlertDialogTitle>
            <AlertDialogDescription className="text-white/60">
              {cancelTarget && (
                <>
                  {cancelTarget.code} · Table {cancelTarget.tableCode} on{" "}
                  {formatDayLong(cancelTarget.date)} at {SLOT_META[cancelTarget.slot].short}. The table will
                  be released for other guests.
                </>
              )}
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel className="border-white/15 bg-transparent text-white hover:bg-white/10 hover:text-white">
              Keep it
            </AlertDialogCancel>
            <AlertDialogAction
              onClick={(e) => {
                e.preventDefault();
                void confirmCancel();
              }}
              disabled={cancelling}
              className="bg-red-600 text-white hover:bg-red-500"
            >
              {cancelling ? "Cancelling…" : "Yes, cancel it"}
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  );
}
