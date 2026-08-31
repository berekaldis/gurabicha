"use client";

import { CalendarPlus, Check, ChevronLeft, ChevronRight, Clock, MapPin, RotateCw, Share2, Users, X } from "lucide-react";
import { motion } from "framer-motion";
import { useCallback, useEffect, useState } from "react";
import { toast } from "sonner";
import { gbApi } from "@/lib/gb/api";
import {
  cloudStorageRemove,
  cloudStorageSet,
  GB_DETAILS_KEY,
  haptics,
  inTelegram,
  openExternal,
  shareViaTelegram,
  useTelegram,
  useTelegramBackInterceptor,
  useTelegramClosingConfirmation,
  useTelegramMainButton,
} from "@/lib/gb/telegram";
import {
  SLOT_LIST,
  SLOT_META,
  addisDayKey,
  buildIcs,
  formatDayLong,
  formatDayShort,
  formatKickoffTime,
  googleCalendarUrl,
  parseISODate,
  toISODate,
  type DraftMatch,
  type GbMatch,
  type GbReservation,
  type GbTable,
  type ReserveDraft,
  type SavedDetails,
} from "@/lib/gb/types";
import { CompetitionBadge, FixturesCalendar } from "./gb-fixtures";
import {
  GbGhostButton,
  GbInfoCard,
  GbPrimaryButton,
  GbSecondaryButton,
  GbSkeleton,
} from "./gb-ui";

type Step = "date" | "table" | "guests" | "details" | "done";

const STEPS: { key: Exclude<Step, "done">; label: string }[] = [
  { key: "date", label: "DATE" },
  { key: "table", label: "TABLE" },
  { key: "guests", label: "GUESTS" },
  { key: "details", label: "DETAILS" },
];

const WEEKDAYS = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];

const TABLE_POSITIONS: Record<string, string> = {
  T1: "left-4 top-[80px]",
  T2: "left-1/2 -translate-x-1/2 top-[80px]",
  T3: "right-4 top-[80px]",
  T4: "left-4 top-[155px]",
  T5: "left-1/2 -translate-x-1/2 top-[155px]",
  T6: "right-4 top-[155px]",
};

async function shareReservation(r: GbReservation) {
  const text = `See you at Gura Bicha! ${r.code} · Table ${r.tableCode} · ${formatDayLong(r.date)} · ${SLOT_META[r.slot].time}`;
  if (shareViaTelegram(text)) return;
  if (typeof navigator !== "undefined" && navigator.share) {
    try {
      await navigator.share({ title: "Gura Bicha Sports Bar", text });
      return;
    } catch {
      // user cancelled or share failed — fall through to clipboard
    }
  }
  try {
    await navigator.clipboard.writeText(text);
    toast.success("Invitation copied to clipboard");
  } catch {
    toast.error("Could not share — please copy your reservation code manually.");
  }
}

/** Adds the reservation to the calendar: Google Calendar inside Telegram
 *  (downloads don't work in the webview), .ics file in normal browsers. */
function addToCalendar(r: GbReservation) {
  if (inTelegram()) {
    openExternal(googleCalendarUrl(r));
    toast.success("Opening Google Calendar");
    return;
  }
  const blob = new Blob([buildIcs(r)], { type: "text/calendar;charset=utf-8" });
  const url = URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = `gura-bicha-${r.code}.ics`;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
  toast.success("Calendar file downloaded");
}

/* ============ CALENDAR (fixtures-aware, shared with the fixtures screen) ============ */

/* ============ MATCH PICKER (for a selected date) ============ */

function MatchPicker({
  matches,
  value,
  onChange,
}: {
  matches: GbMatch[];
  value: DraftMatch | null;
  onChange: (m: DraftMatch | null) => void;
}) {
  if (matches.length === 0) {
    return (
      <div className="rounded-xl border border-white/[0.08] bg-panel p-4 text-center text-[11px] leading-relaxed text-white/45">
        No Premier League or Champions League fixtures this day — the big screens are all yours.
      </div>
    );
  }
  return (
    <div className="space-y-2" role="radiogroup" aria-label="Match to watch">
      {matches.map((m) => {
        const id = m.id;
        const active = value?.id === id;
        return (
          <button
            key={id}
            type="button"
            role="radio"
            aria-checked={active}
            onClick={() => {
              haptics.select();
              onChange(
                active
                  ? null
                  : { id, label: `${m.homeTeam} vs ${m.awayTeam} · ${m.competition} — ${m.stage}` }
              );
            }}
            className={`flex w-full items-center gap-3 rounded-xl border p-3 text-left transition ${
              active ? "border-gold bg-gold/10" : "border-white/[0.08] bg-panel hover:border-gold/40"
            }`}
          >
            <span
              className={`grid h-5 w-5 shrink-0 place-items-center rounded-full border ${
                active ? "border-gold bg-gold" : "border-white/25"
              }`}
              aria-hidden
            >
              {active && <Check className="h-3 w-3 text-[#050505]" strokeWidth={3} />}
            </span>
            <span className="min-w-0 flex-1">
              <span className="flex flex-wrap items-center gap-1.5">
                <CompetitionBadge competition={m.competition} />
                {m.status === "FINISHED" && (
                  <span className="rounded-full border border-white/15 px-2 py-0.5 text-[8px] font-bold text-white/45">FT</span>
                )}
              </span>
              <span className="mt-1 block truncate text-[13px] font-bold text-white">
                {m.homeTeam} <span className="text-gold">v</span> {m.awayTeam}
              </span>
              <span className="mt-0.5 block text-[10px] text-white/40">
                {m.stage} · {formatKickoffTime(m.kickoff)} EAT{m.venue ? ` · ${m.venue}` : ""}
              </span>
            </span>
          </button>
        );
      })}
      <button
        type="button"
        role="radio"
        aria-checked={value === null}
        onClick={() => {
          haptics.select();
          onChange(null);
        }}
        className={`flex w-full items-center gap-3 rounded-xl border p-3 text-left transition ${
          value === null ? "border-gold bg-gold/10" : "border-white/[0.08] bg-panel hover:border-gold/40"
        }`}
      >
        <span
          className={`grid h-5 w-5 shrink-0 place-items-center rounded-full border ${
            value === null ? "border-gold bg-gold" : "border-white/25"
          }`}
          aria-hidden
        >
          {value === null && <Check className="h-3 w-3 text-[#050505]" strokeWidth={3} />}
        </span>
        <span className="text-[12px] font-semibold text-white/75">
          No match — just food, drinks &amp; vibes
        </span>
      </button>
    </div>
  );
}

/* ============ STEP: DATE ============ */

function DateStep({
  draft,
  patch,
  onNext,
  yearMatches,
  yearMatchesLoading,
}: {
  draft: ReserveDraft;
  patch: (p: Partial<ReserveDraft>) => void;
  onNext: () => void;
  yearMatches: GbMatch[] | null;
  yearMatchesLoading: boolean;
}) {
  const year = new Date().getFullYear();
  const dayMatches =
    draft.date && yearMatches
      ? yearMatches.filter((m) => addisDayKey(new Date(m.kickoff)) === draft.date)
      : [];

  return (
    <div>
      <FixturesCalendar
        matches={yearMatches}
        selected={draft.date}
        onSelect={(iso) => {
          haptics.select();
          // Keep the picked match only when it kicks off on the newly chosen day.
          const stillValid =
            draft.match && yearMatches?.some((m) => m.id === draft.match?.id && addisDayKey(new Date(m.kickoff)) === iso);
          patch(stillValid ? { date: iso } : { date: iso, match: null });
        }}
        year={year}
      />

      <p className="mb-2 mt-5 text-[10px] font-bold tracking-[0.15em] text-white/45">
        {dayMatches.length > 0 ? "MATCHES THIS DAY" : "WATCHING?"}
      </p>
      {yearMatchesLoading && yearMatches === null ? (
        <GbSkeleton className="h-[64px] w-full" />
      ) : (
        <MatchPicker
          matches={dayMatches}
          value={draft.match}
          onChange={(m) => patch({ match: m })}
        />
      )}

      <p className="mb-2 mt-5 text-[10px] font-bold tracking-[0.15em] text-white/45">SESSION</p>
      <div className="grid grid-cols-2 gap-2.5">
        {SLOT_LIST.map((slot) => {
          const meta = SLOT_META[slot];
          const active = draft.slot === slot;
          return (
            <button
              key={slot}
              type="button"
              onClick={() => {
                haptics.select();
                patch({ slot, table: null });
              }}
              aria-pressed={active}
              className={`rounded-xl border px-3 py-3 text-left transition ${
                active
                  ? "border-gold bg-gold/10 text-gold"
                  : "border-white/10 text-white/60 hover:border-gold/40"
              }`}
            >
              <span className="block text-[9px] font-bold uppercase tracking-wider opacity-70">{meta.label}</span>
              <span className="mt-0.5 block text-[13px] font-extrabold">{meta.time}</span>
            </button>
          );
        })}
      </div>

      <div className="mt-5">
        <GbInfoCard
          label="SELECTED DATE"
          value={draft.date ? formatDayLong(draft.date) : "Pick a date"}
          sub={
            draft.match
              ? `${draft.match.label} · ${SLOT_META[draft.slot].label}`
              : draft.date
                ? `${SLOT_META[draft.slot].label} · ${SLOT_META[draft.slot].time}`
                : undefined
          }
        />
        <GbPrimaryButton
          disabled={!draft.date}
          onClick={() => {
            haptics.tap();
            onNext();
          }}
        >
          CONTINUE
        </GbPrimaryButton>
      </div>
    </div>
  );
}

/* ============ STEP: TABLE ============ */

function TableStep({
  draft,
  patch,
  onNext,
}: {
  draft: ReserveDraft;
  patch: (p: Partial<ReserveDraft>) => void;
  onNext: () => void;
}) {
  const [data, setData] = useState<{ key: string; tables: GbTable[] } | null>(null);
  const [refreshing, setRefreshing] = useState(false);
  const [reloadKey, setReloadKey] = useState(0);
  const dataKey = `${draft.date ?? ""}-${draft.slot}`;
  const loading = !data || data.key !== dataKey;

  useEffect(() => {
    if (!draft.date) return;
    let alive = true;
    const key = `${draft.date}-${draft.slot}`;
    gbApi.tables(draft.date, draft.slot).then(
      (res) => {
        if (!alive) return;
        setData({ key, tables: res.tables });
      },
      (err: unknown) => {
        if (!alive) return;
        toast.error((err as Error).message);
        setData({ key, tables: [] });
      }
    );
    return () => {
      alive = false;
    };
  }, [reloadKey, draft.date, draft.slot]);

  const tables = loading ? null : (data?.tables ?? []);
  const remaining = tables?.filter((t) => t.available).length ?? 0;
  const selectedTaken = Boolean(
    tables && draft.table && tables.some((t) => t.code === draft.table?.code && !t.available)
  );

  const refresh = () => {
    setRefreshing(true);
    setReloadKey((k) => k + 1);
    window.setTimeout(() => setRefreshing(false), 900);
  };

  return (
    <div>
      <div className="mb-4 flex flex-wrap items-center gap-2">
        {draft.date && (
          <span className="inline-flex items-center gap-1.5 rounded-full border border-gold/30 bg-black/50 px-3 py-1.5 text-[11px] font-semibold text-gold-light">
            <MapPin className="h-3 w-3" aria-hidden />
            {formatDayShort(draft.date).dow} {formatDayShort(draft.date).day} · {SLOT_META[draft.slot].short}
          </span>
        )}
        {tables && (
          <span className="text-[11px] text-white/45">
            {remaining} of {tables.length} tables free
          </span>
        )}
      </div>

      {selectedTaken && (
        <div className="mb-4 rounded-xl border border-amber-500/30 bg-amber-500/10 p-3 text-[11px] leading-relaxed text-amber-300">
          Table {draft.table?.code} was just booked by someone else — please pick another table.
        </div>
      )}

      {loading ? (
        <GbSkeleton className="h-[310px] w-full" />
      ) : (
        <div className="relative mb-4 h-[310px] overflow-hidden rounded-2xl border border-gold/25 bg-[#0c0c0c]">
          <div
            aria-hidden
            className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_100%,rgba(245,189,25,0.07),transparent_50%)]"
          />
          <div className="absolute left-1/2 top-4 w-[150px] -translate-x-1/2 rounded border border-white/15 py-1.5 text-center text-[9px] tracking-wider text-white/50">
            SCREEN
          </div>

          {tables?.map((t) => {
            const selected = draft.table?.code === t.code;
            const base =
              "absolute h-[42px] w-[55px] rounded-lg border text-[11px] font-extrabold transition grid place-items-center";
            if (!t.available) {
              return (
                <button
                  key={t.code}
                  type="button"
                  disabled
                  aria-label={`Table ${t.code} is booked`}
                  className={`${base} cursor-not-allowed border-white/[0.06] bg-white/[0.03] text-white/25 ${TABLE_POSITIONS[t.code] ?? ""}`}
                >
                  <span>
                    <X className="mx-auto h-3 w-3 text-red-400/70" aria-hidden />
                    <span className="mt-0.5 block text-[8px] tracking-wider">{t.code}</span>
                  </span>
                </button>
              );
            }
            return (
              <button
                key={t.code}
                type="button"
                onClick={() => {
                  haptics.selectMedium();
                  patch({ table: t });
                }}
                aria-pressed={selected}
                aria-label={`Table ${t.code}, ${t.zone}, seats ${t.seats}`}
                className={`${base} ${
                  selected
                    ? "border-gold bg-gold text-[#050505]"
                    : "border-gold-dark bg-[#111] text-gold-light hover:border-gold-light"
                } ${TABLE_POSITIONS[t.code] ?? ""}`}
              >
                {t.code}
              </button>
            );
          })}

          <div className="absolute bottom-5 left-1/2 flex h-[42px] w-[180px] -translate-x-1/2 items-center justify-center gap-1.5 rounded-lg border border-gold font-display text-[11px] font-bold tracking-wider text-gold">
            STAGE
          </div>

          <button
            type="button"
            aria-label="Refresh availability"
            onClick={refresh}
            className="absolute right-3 top-3 grid h-8 w-8 place-items-center rounded-lg border border-white/10 text-white/50 transition hover:text-gold"
          >
            <RotateCw className={`h-3.5 w-3.5 ${refreshing ? "animate-spin" : ""}`} />
          </button>
        </div>
      )}

      <div className="mb-4 flex items-center justify-center gap-4 text-[9px] font-bold tracking-wider text-white/40">
        <span className="flex items-center gap-1.5">
          <span className="h-2.5 w-2.5 rounded border border-gold-dark bg-[#111]" aria-hidden /> AVAILABLE
        </span>
        <span className="flex items-center gap-1.5">
          <span className="h-2.5 w-2.5 rounded bg-gold" aria-hidden /> SELECTED
        </span>
        <span className="flex items-center gap-1.5">
          <span className="h-2.5 w-2.5 rounded bg-white/10" aria-hidden /> BOOKED
        </span>
      </div>

      <GbInfoCard
        label="SELECTED TABLE"
        value={draft.table ? draft.table.code : "Tap a table on the map"}
        sub={draft.table ? `${draft.table.zone} · ${draft.table.seats}` : "Gold tables are still open"}
      />

      <GbPrimaryButton disabled={!draft.table || selectedTaken} onClick={onNext}>
        CONTINUE
      </GbPrimaryButton>
    </div>
  );
}

/* ============ STEP: GUESTS ============ */

function GuestsStep({ draft, patch, onNext }: { draft: ReserveDraft; patch: (p: Partial<ReserveDraft>) => void; onNext: () => void }) {
  const max = draft.table?.capacity ?? 12;
  const setGuests = (n: number) => patch({ guests: Math.min(max, Math.max(1, n)) });

  return (
    <div>
      <div className="flex h-[300px] items-center justify-center gap-7">
        <button
          type="button"
          aria-label="Remove one guest"
          disabled={draft.guests <= 1}
          onClick={() => setGuests(draft.guests - 1)}
          className="grid h-12 w-12 place-items-center rounded-full border border-gold-dark text-2xl text-gold transition hover:bg-gold/10 disabled:opacity-25"
        >
          −
        </button>
        <div className="text-center" aria-live="polite">
          <strong className="block font-display text-[58px] leading-none text-white">{draft.guests}</strong>
          <span className="mt-1 block text-[11px] tracking-[0.2em] text-white/45">PEOPLE</span>
        </div>
        <button
          type="button"
          aria-label="Add one guest"
          disabled={draft.guests >= max}
          onClick={() => setGuests(draft.guests + 1)}
          className="grid h-12 w-12 place-items-center rounded-full border border-gold-dark text-2xl text-gold transition hover:bg-gold/10 disabled:opacity-25"
        >
          +
        </button>
      </div>

      <div className="mb-5 flex justify-center gap-2">
        {[2, 4, 6, 8].map((n) => (
          <button
            key={n}
            type="button"
            onClick={() => setGuests(n)}
            className={`rounded-full border px-3.5 py-1.5 text-[11px] font-bold transition ${
              draft.guests === n
                ? "border-gold bg-gold/15 text-gold"
                : "border-white/10 text-white/50 hover:border-gold/40"
            }`}
          >
            {n > max ? `${max} max` : n}
          </button>
        ))}
      </div>

      <GbInfoCard
        label="PARTY SIZE"
        value={`${draft.guests} ${draft.guests === 1 ? "Guest" : "Guests"}`}
        sub={
          draft.table
            ? `Table ${draft.table.code} fits up to ${max} guests`
            : "Select a table to see its capacity"
        }
      />

      <GbPrimaryButton onClick={onNext}>CONTINUE</GbPrimaryButton>
    </div>
  );
}

/* ============ STEP: DETAILS ============ */

function DetailsStep({
  draft,
  savedDetails,
  onSaveDetails,
  onTableConflict,
  onDone,
}: {
  draft: ReserveDraft;
  savedDetails: SavedDetails | null;
  onSaveDetails: (d: SavedDetails | null) => void;
  onTableConflict: () => void;
  onDone: (r: GbReservation) => void;
}) {
  const { user: tgUser } = useTelegram();
  const [name, setName] = useState(
    () =>
      savedDetails?.name ??
      (tgUser ? [tgUser.firstName, tgUser.lastName].filter(Boolean).join(" ") : "")
  );
  const [phone, setPhone] = useState(savedDetails?.phone ?? "");
  const [email, setEmail] = useState(savedDetails?.email ?? "");
  const [request, setRequest] = useState("");
  const [save, setSave] = useState(Boolean(savedDetails));
  const [errors, setErrors] = useState<{ name?: string; phone?: string }>({});
  const [submitting, setSubmitting] = useState(false);

  useTelegramClosingConfirmation(true);

  const submit = useCallback(async () => {
    const nextErrors: { name?: string; phone?: string } = {};
    if (name.trim().length < 2) nextErrors.name = "Please enter your full name.";
    if (phone.replace(/\D/g, "").length < 7) nextErrors.phone = "Enter a valid phone number (e.g. +251 911 234 567).";
    setErrors(nextErrors);
    if (Object.keys(nextErrors).length > 0 || !draft.date || !draft.table) {
      haptics.error();
      return;
    }

    setSubmitting(true);
    try {
      const res = await gbApi.createReservation({
        date: draft.date,
        slot: draft.slot,
        tableCode: draft.table.code,
        guests: draft.guests,
        name: name.trim(),
        phone: phone.trim(),
        email: email.trim() || undefined,
        request: request.trim() || undefined,
        matchId: draft.match?.id || undefined,
      });
      if (save) {
        const details: SavedDetails = { name: name.trim(), phone: phone.trim(), email: email.trim() };
        onSaveDetails(details);
        const serialized = JSON.stringify(details);
        try {
          localStorage.setItem(GB_DETAILS_KEY, serialized);
        } catch {
          // storage unavailable — ignore
        }
        // Telegram CloudStorage keeps the profile in the user's account —
        // survives evicted webview storage and device switches.
        void cloudStorageSet(GB_DETAILS_KEY, serialized);
      } else {
        onSaveDetails(null);
        try {
          localStorage.removeItem(GB_DETAILS_KEY);
        } catch {
          // ignore
        }
        void cloudStorageRemove(GB_DETAILS_KEY);
      }
      haptics.success();
      toast.success(`Reservation ${res.reservation.code} confirmed`);
      onDone(res.reservation);
    } catch (err) {
      const message = (err as Error).message;
      haptics.error();
      toast.error(message);
      if (message.toLowerCase().includes("no longer available")) {
        onTableConflict();
      }
    } finally {
      setSubmitting(false);
    }
  }, [name, phone, email, request, save, draft, onSaveDetails, onDone, onTableConflict]);

  // Mirror the in-app confirm button onto Telegram's native MainButton.
  useTelegramMainButton({
    text: "CONFIRM RESERVATION",
    onClick: () => void submit(),
    visible: true,
    enabled: !submitting,
  });

  return (
    <form
      onSubmit={(e) => {
        e.preventDefault();
        void submit();
      }}
    >
      <div className="mb-5 flex flex-wrap gap-2">
        {draft.date && (
          <span className="inline-flex items-center gap-1.5 rounded-full border border-gold/30 bg-black/50 px-3 py-1.5 text-[11px] font-semibold text-gold-light">
            <Clock className="h-3 w-3" aria-hidden />
            {formatDayLong(draft.date).replace(/,/, " ·")} · {SLOT_META[draft.slot].short}
          </span>
        )}
        {draft.table && (
          <span className="inline-flex items-center gap-1.5 rounded-full border border-gold/30 bg-black/50 px-3 py-1.5 text-[11px] font-semibold text-gold-light">
            <Users className="h-3 w-3" aria-hidden />
            {draft.table.code} · {draft.guests} guests
          </span>
        )}
      </div>

      {draft.match && (
        <div className="mb-4 rounded-xl border border-gold/30 bg-gold/[0.06] p-3.5">
          <small className="text-[9px] font-bold tracking-[0.2em] text-gold">WATCHING</small>
          <p className="mt-1 text-[12px] font-semibold leading-snug text-white">{draft.match.label}</p>
        </div>
      )}

      <div className="mb-4">
        <label htmlFor="gb-name" className="mb-1.5 block text-[10px] tracking-[0.15em] text-white/45">
          FULL NAME
        </label>
        <input
          id="gb-name"
          type="text"
          autoComplete="name"
          value={name}
          onChange={(e) => setName(e.target.value)}
          placeholder="Your name"
          className="w-full rounded-[10px] border border-gold/25 bg-panel px-3.5 py-3.5 text-[13px] text-white outline-none transition placeholder:text-white/25 focus:border-gold"
        />
        {errors.name && <p className="mt-1.5 text-[11px] text-red-400">{errors.name}</p>}
      </div>

      <div className="mb-4">
        <label htmlFor="gb-phone" className="mb-1.5 block text-[10px] tracking-[0.15em] text-white/45">
          PHONE NUMBER
        </label>
        <input
          id="gb-phone"
          type="tel"
          autoComplete="tel"
          value={phone}
          onChange={(e) => setPhone(e.target.value)}
          placeholder="+251 ..."
          className="w-full rounded-[10px] border border-gold/25 bg-panel px-3.5 py-3.5 text-[13px] text-white outline-none transition placeholder:text-white/25 focus:border-gold"
        />
        {errors.phone && <p className="mt-1.5 text-[11px] text-red-400">{errors.phone}</p>}
      </div>

      <div className="mb-4">
        <label htmlFor="gb-email" className="mb-1.5 block text-[10px] tracking-[0.15em] text-white/45">
          EMAIL — OPTIONAL
        </label>
        <input
          id="gb-email"
          type="email"
          autoComplete="email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          placeholder="you@example.com"
          className="w-full rounded-[10px] border border-gold/25 bg-panel px-3.5 py-3.5 text-[13px] text-white outline-none transition placeholder:text-white/25 focus:border-gold"
        />
      </div>

      <div className="mb-5">
        <label htmlFor="gb-request" className="mb-1.5 block text-[10px] tracking-[0.15em] text-white/45">
          SPECIAL REQUEST — OPTIONAL
        </label>
        <input
          id="gb-request"
          type="text"
          value={request}
          onChange={(e) => setRequest(e.target.value)}
          placeholder="Birthday cake, quiet corner, etc."
          className="w-full rounded-[10px] border border-gold/25 bg-panel px-3.5 py-3.5 text-[13px] text-white outline-none transition placeholder:text-white/25 focus:border-gold"
        />
      </div>

      <label className="mb-5 flex cursor-pointer items-center gap-2.5 text-[11px] text-white/60">
        <input
          type="checkbox"
          checked={save}
          onChange={(e) => setSave(e.target.checked)}
          className="h-4 w-4 accent-[#f5bd19]"
        />
        Save my details for next time
      </label>

      <GbPrimaryButton type="submit" loading={submitting}>
        CONFIRM RESERVATION
      </GbPrimaryButton>
    </form>
  );
}

/* ============ STEP: DONE ============ */

function DoneStep({
  reservation,
  onHome,
  onViewReservations,
}: {
  reservation: GbReservation;
  onHome: () => void;
  onViewReservations: () => void;
}) {
  return (
    <motion.div
      initial={{ opacity: 0, scale: 0.96 }}
      animate={{ opacity: 1, scale: 1 }}
      transition={{ type: "spring", stiffness: 260, damping: 22 }}
      className="pt-8 text-center"
    >
      <div className="mx-auto mb-5 grid h-[75px] w-[75px] place-items-center rounded-full border border-gold bg-gold/10">
        <Check className="h-9 w-9 text-gold" strokeWidth={2.5} aria-hidden />
      </div>
      <h1 className="font-display text-4xl uppercase text-gold">You&apos;re in!</h1>
      <p className="font-script mt-1 text-[19px] text-white">Game &amp; Groove</p>

      <div className="mt-7 border-y border-gold/25 py-4 text-left">
        {[
          ["Date", formatDayLong(reservation.date)],
          ["Time", SLOT_META[reservation.slot].time],
          ["Guests", `${reservation.guests} ${reservation.guests === 1 ? "Person" : "People"}`],
          ["Table", reservation.tableCode],
          ["Name", reservation.name],
        ].map(([k, v]) => (
          <div key={k} className="flex items-center justify-between py-1.5 text-[12px]">
            <span className="text-white/45">{k}</span>
            <span className="font-semibold text-white">{v}</span>
          </div>
        ))}
        {reservation.matchLabel && (
          <div className="flex items-start justify-between gap-4 py-1.5 text-[12px]">
            <span className="shrink-0 text-white/45">Match</span>
            <span className="text-right font-semibold text-gold-light">{reservation.matchLabel}</span>
          </div>
        )}
      </div>

      <p className="mt-5 text-[11px] tracking-[0.2em] text-white/45">RESERVATION #</p>
      <strong className="mt-1 block font-display text-2xl tracking-wider text-gold">{reservation.code}</strong>
      <p className="mt-2 text-[11px] text-white/40">
        Show this code at the door. Need changes? Call +251 911 234 567.
      </p>

      <div className="mt-7 space-y-2.5">
        <GbPrimaryButton onClick={() => addToCalendar(reservation)}>
          <CalendarPlus className="h-4 w-4" aria-hidden />
          ADD TO CALENDAR
        </GbPrimaryButton>
        <GbSecondaryButton onClick={() => void shareReservation(reservation)}>
          <Share2 className="h-4 w-4" aria-hidden />
          SHARE WITH FRIENDS
        </GbSecondaryButton>
        <GbSecondaryButton onClick={onViewReservations}>VIEW MY RESERVATIONS</GbSecondaryButton>
        <GbGhostButton onClick={onHome}>Back to home</GbGhostButton>
      </div>
    </motion.div>
  );
}

/* ============ FLOW CONTAINER ============ */

export function GbReserve({
  draft,
  onChange,
  savedDetails,
  onSaveDetails,
  yearMatches,
  yearMatchesLoading,
  startStep = "date",
  onHome,
  onViewReservations,
}: {
  draft: ReserveDraft;
  onChange: (d: ReserveDraft) => void;
  savedDetails: SavedDetails | null;
  onSaveDetails: (d: SavedDetails | null) => void;
  yearMatches: GbMatch[] | null;
  yearMatchesLoading: boolean;
  startStep?: "date" | "table";
  onHome: () => void;
  onViewReservations: () => void;
}) {
  const [step, setStep] = useState<Step>(startStep === "table" && draft.date && draft.table === null ? "table" : startStep);
  const patch = (p: Partial<ReserveDraft>) => onChange({ ...draft, ...p });
  const stepIndex = STEPS.findIndex((s) => s.key === step);

  const back = () => {
    if (step === "date") onHome();
    else if (step === "table") setStep("date");
    else if (step === "guests") setStep("table");
    else if (step === "details") setStep("guests");
  };

  // Telegram's hardware back steps through the wizard instead of dumping
  // the user out of the flow; on the confirmation screen it exits to home.
  useTelegramBackInterceptor(
    step !== "done"
      ? () => {
          back();
          return true;
        }
      : null
  );

  const titles: Record<Step, string> = {
    date: "Choose Date",
    table: "Choose Your Table",
    guests: "How Many Guests?",
    details: "Your Details",
    done: "Confirmed",
  };

  return (
    <div className="px-5 pb-9 pt-6">
      {step !== "done" && (
        <>
          <div className="mb-6 flex items-center gap-4">
            <button
              type="button"
              aria-label="Go back"
              onClick={back}
              className="grid h-9 w-9 place-items-center rounded-xl border border-white/10 text-white transition hover:border-gold/40 hover:text-gold"
            >
              <ChevronLeft className="h-5 w-5" />
            </button>
            <h1 className="font-display text-2xl uppercase text-white">{titles[step]}</h1>
          </div>

          <div className="mb-7" aria-hidden>
            <div className="flex gap-1.5">
              {STEPS.map((s, i) => (
                <span
                  key={s.key}
                  className={`h-1.5 flex-1 rounded-full transition-colors ${
                    i <= stepIndex ? "bg-gold" : "bg-white/15"
                  }`}
                />
              ))}
            </div>
            <p className="mt-2 text-[10px] font-bold tracking-[0.2em] text-white/40">
              STEP {stepIndex + 1} OF 4 · {STEPS[stepIndex].label}
            </p>
          </div>
        </>
      )}

      {step === "date" && (
        <DateStep
          draft={draft}
          patch={patch}
          yearMatches={yearMatches}
          yearMatchesLoading={yearMatchesLoading}
          onNext={() => setStep("table")}
        />
      )}

      {step === "table" && <TableStep draft={draft} patch={patch} onNext={() => setStep("guests")} />}

      {step === "guests" && <GuestsStep draft={draft} patch={patch} onNext={() => setStep("details")} />}

      {step === "details" && (
        <DetailsStep
          draft={draft}
          savedDetails={savedDetails}
          onSaveDetails={onSaveDetails}
          onTableConflict={() => {
            patch({ table: null });
            setStep("table");
          }}
          onDone={(r) => {
            patch({ reservation: r });
            setStep("done");
          }}
        />
      )}

      {step === "done" && draft.reservation && (
        <DoneStep reservation={draft.reservation} onHome={onHome} onViewReservations={onViewReservations} />
      )}

      {step === "done" && !draft.reservation && (
        <div className="pt-8 text-center">
          <p className="text-[13px] text-white/60">Something went wrong — let&apos;s start again.</p>
          <GbSecondaryButton className="mt-4" onClick={onHome}>
            BACK TO HOME
          </GbSecondaryButton>
        </div>
      )}
    </div>
  );
}
