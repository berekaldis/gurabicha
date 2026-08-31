"use client";

// Fixtures calendar: shows every Premier League & Champions League match of
// the current year on a month grid (gold dot = Premier League, silver dot =
// Champions League), with a day-detail panel listing kickoffs (Addis time),
// stages, venues and results.

import { ChevronLeft, ChevronRight, MapPin, Trophy, Zap } from "lucide-react";
import { useMemo, useState } from "react";
import {
  addisDayKey,
  formatDayLong,
  formatKickoffTime,
  isMatchLive,
  isMatchPast,
  parseISODate,
  teamInitials,
  toISODate,
  type DraftMatch,
  type GbMatch,
  type Slot,
} from "@/lib/gb/types";
import { GbErrorBox, GbPrimaryButton, GbSecondaryButton, GbSkeleton } from "./gb-ui";

const WEEKDAYS = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];

/* ---------------- helpers ---------------- */

function groupByDay(matches: GbMatch[]): Map<string, GbMatch[]> {
  const map = new Map<string, GbMatch[]>();
  for (const m of matches) {
    const key = addisDayKey(new Date(m.kickoff));
    const list = map.get(key);
    if (list) list.push(m);
    else map.set(key, [m]);
  }
  return map;
}

/** True when any match of `matches` is a live/fresh kickoff window match. */
function hasLive(matches: GbMatch[] | undefined): boolean {
  return Boolean(matches?.some((m) => m.status !== "FINISHED" && isMatchLive(m.kickoff)));
}

export function CompetitionBadge({ competition }: { competition: string }) {
  const ucl = competition === "Champions League";
  return (
    <span
      className={`inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[9px] font-extrabold tracking-wider ${
        ucl ? "border-white/25 bg-white/[0.06] text-white/80" : "border-gold/40 bg-gold/10 text-gold"
      }`}
    >
      {ucl && <span aria-hidden>★</span>}
      {ucl ? "UCL" : "PL"}
    </span>
  );
}

/* ---------------- calendar grid ---------------- */

export function FixturesCalendar({
  matches,
  selected,
  onSelect,
  year,
}: {
  matches: GbMatch[] | null;
  selected: string | null;
  onSelect: (iso: string) => void;
  year: number;
}) {
  const today = useMemo(() => {
    const d = new Date();
    d.setHours(0, 0, 0, 0);
    return d;
  }, []);
  const [view, setView] = useState(() => {
    const base = selected ? parseISODate(selected) : new Date();
    return new Date(base.getFullYear(), base.getMonth(), 1);
  });

  const grouped = useMemo(() => (matches ? groupByDay(matches) : null), [matches]);

  const y = view.getFullYear();
  const m = view.getMonth();
  const firstDow = new Date(y, m, 1).getDay();
  const daysInMonth = new Date(y, m + 1, 0).getDate();
  const cells: (string | null)[] = [
    ...Array.from({ length: firstDow }, () => null),
    ...Array.from({ length: daysInMonth }, (_, i) => toISODate(new Date(y, m, i + 1))),
  ];

  const canGoPrev = new Date(y, m, 1) > new Date(year, 0, 1);
  const canGoNext = new Date(y, m, 1) < new Date(year, 11, 1);

  return (
    <div className="rounded-[14px] border border-white/[0.08] bg-panel p-4">
      <div className="mb-5 flex items-center justify-between">
        <button
          type="button"
          aria-label="Previous month"
          disabled={!canGoPrev}
          onClick={() => setView(new Date(y, m - 1, 1))}
          className="text-gold transition hover:text-gold-light disabled:opacity-25"
        >
          <ChevronLeft className="h-6 w-6" />
        </button>
        <span className="font-display text-lg uppercase text-white" aria-live="polite">
          {view.toLocaleDateString("en-US", { month: "long", year: "numeric" })}
        </span>
        <button
          type="button"
          aria-label="Next month"
          disabled={!canGoNext}
          onClick={() => setView(new Date(y, m + 1, 1))}
          className="text-gold transition hover:text-gold-light disabled:opacity-25"
        >
          <ChevronRight className="h-6 w-6" />
        </button>
      </div>

      <div className="mb-2 grid grid-cols-7 gap-1.5 text-center">
        {WEEKDAYS.map((w) => (
          <span key={w} className="text-[9px] font-extrabold tracking-wider text-gold">
            {w}
          </span>
        ))}
      </div>

      <div className="grid grid-cols-7 gap-1.5" role="grid" aria-label="Fixtures calendar">
        {cells.map((iso, i) => {
          if (!iso) return <div key={`empty-${i}`} aria-hidden />;
          const d = parseISODate(iso);
          const dayMatches = grouped?.get(iso);
          const hasPL = dayMatches?.some((x) => x.competition === "Premier League") ?? false;
          const hasUCL = dayMatches?.some((x) => x.competition === "Champions League") ?? false;
          const isPast = d.getTime() < today.getTime();
          const isToday = iso === toISODate(today);
          const isSelected = selected === iso;

          return (
            <button
              key={iso}
              type="button"
              onClick={() => onSelect(iso)}
              aria-pressed={isSelected}
              aria-label={`${formatDayLong(iso)}${dayMatches ? `, ${dayMatches.length} matches` : ""}`}
              className={`relative grid h-10 place-items-center rounded-full border text-[11px] transition ${
                isSelected
                  ? "border-gold bg-gold font-extrabold text-[#050505]"
                  : isPast
                    ? "border-transparent text-white/30 hover:border-white/15"
                    : "border-white/10 text-white/85 hover:border-gold/50 hover:text-gold-light"
              } ${isToday && !isSelected ? "border-gold/60 text-gold" : ""}`}
            >
              {d.getDate()}
              {(hasPL || hasUCL || hasLive(dayMatches)) && (
                <span className="absolute bottom-1 flex items-center gap-[3px]" aria-hidden>
                  {hasPL && (
                    <span
                      className={`h-[5px] w-[5px] rounded-full ${
                        isSelected ? "bg-[#050505]" : "bg-gold"
                      }`}
                    />
                  )}
                  {hasUCL && (
                    <span
                      className={`h-[5px] w-[5px] rounded-full ${
                        isSelected ? "bg-[#050505]" : "bg-white/85"
                      }`}
                    />
                  )}
                  {!hasPL && !hasUCL && hasLive(dayMatches) && (
                    <span className={`h-[5px] w-[5px] rounded-full ${isSelected ? "bg-[#050505]" : "bg-red-500"}`} />
                  )}
                </span>
              )}
            </button>
          );
        })}
      </div>

      <div className="mt-4 flex flex-wrap items-center justify-center gap-x-4 gap-y-1.5 text-[9px] font-bold tracking-wider text-white/40">
        <span className="flex items-center gap-1.5">
          <span className="h-[6px] w-[6px] rounded-full bg-gold" aria-hidden /> PREMIER LEAGUE
        </span>
        <span className="flex items-center gap-1.5">
          <span className="h-[6px] w-[6px] rounded-full bg-white/85" aria-hidden /> CHAMPIONS LEAGUE
        </span>
        <span className="flex items-center gap-1.5">
          <span className="h-[6px] w-[6px] rounded-full bg-red-500" aria-hidden /> LIVE NOW
        </span>
      </div>
    </div>
  );
}

/* ---------------- match row ---------------- */

export function FixtureRow({
  match,
  action,
}: {
  match: GbMatch;
  action?: React.ReactNode;
}) {
  const finished = match.status === "FINISHED";
  const live = !finished && (match.live || isMatchLive(match.kickoff));
  const past = isMatchPast(match.kickoff);

  return (
    <div
      className={`flex items-center gap-3 rounded-xl border p-3 ${
        live ? "border-red-500/40 bg-red-500/[0.04]" : "border-white/[0.07] bg-panel"
      } ${finished ? "opacity-70" : ""}`}
    >
      <div className="flex w-11 shrink-0 flex-col items-center">
        <span className="text-[11px] font-extrabold text-gold-light">{formatKickoffTime(match.kickoff)}</span>
        <span className="text-[8px] tracking-wider text-white/35">EAT</span>
      </div>

      <div className="min-w-0 flex-1">
        <div className="flex flex-wrap items-center gap-1.5">
          <CompetitionBadge competition={match.competition} />
          {live && (
            <span className="inline-flex items-center gap-1 rounded-full border border-red-500/30 bg-red-500/15 px-2 py-0.5 text-[8px] font-bold text-red-400">
              <span className="h-1 w-1 animate-pulse rounded-full bg-red-500" aria-hidden />
              LIVE
            </span>
          )}
          {finished && (
            <span className="rounded-full border border-white/15 px-2 py-0.5 text-[8px] font-bold text-white/45">
              FT
            </span>
          )}
          {match.featured && !finished && (
            <span className="inline-flex items-center gap-0.5 rounded-full border border-gold/40 bg-gold/10 px-2 py-0.5 text-[8px] font-bold text-gold">
              <Zap className="h-2.5 w-2.5" aria-hidden />
              BIG GAME
            </span>
          )}
        </div>
        <p className="mt-1 truncate text-[13px] font-bold text-white">
          {match.homeTeam}{" "}
          {finished && match.homeScore !== null && match.awayScore !== null ? (
            <span className="font-display text-gold">
              {match.homeScore}–{match.awayScore}
            </span>
          ) : (
            <span className="text-gold">v</span>
          )}{" "}
          {match.awayTeam}
        </p>
        <p className="mt-0.5 flex items-center gap-1.5 truncate text-[10px] text-white/40">
          <span className="text-white/55">{match.stage}</span>
          {match.season && <span>· {match.season}</span>}
          {match.venue && (
            <span className="inline-flex items-center gap-0.5">
              · <MapPin className="h-2.5 w-2.5" aria-hidden /> {match.venue}
            </span>
          )}
        </p>
      </div>

      {action}
    </div>
  );
}

/** Slot inferred from the kickoff time in venue timezone (EAT >= 16:00 = evening). */
export function slotForMatch(kickoff: string): Slot {
  const hour = Number(formatKickoffTime(kickoff).slice(0, 2));
  return hour >= 16 ? "EVENING" : "LUNCH";
}

/* ---------------- fixtures screen ---------------- */

export function GbFixturesScreen({
  matches,
  loading,
  error,
  onRetry,
  onReserveMatch,
}: {
  matches: GbMatch[] | null;
  loading: boolean;
  error: string | null;
  onRetry: () => void;
  onReserveMatch: (date: string, match: DraftMatch | null, slot: Slot) => void;
}) {
  const year = new Date().getFullYear();
  const [selected, setSelected] = useState<string | null>(() => toISODate(new Date()));

  const grouped = useMemo(() => (matches ? groupByDay(matches) : null), [matches]);
  const dayMatches = selected && grouped ? grouped.get(selected) ?? [] : [];

  const todayISO = toISODate(new Date());
  const isPastDay = selected ? parseISODate(selected) < (() => {
    const t = new Date();
    t.setHours(0, 0, 0, 0);
    return t;
  })() : false;

  const monthCount = useMemo(() => {
    if (!selected || !matches) return null;
    return matches.filter((m) => addisDayKey(new Date(m.kickoff)).slice(0, 7) === selected.slice(0, 7)).length;
  }, [matches, selected]);

  return (
    <div className="px-5 pb-9 pt-6">
      <div className="mb-1 flex items-center gap-2.5">
        <Trophy className="h-5 w-5 text-gold" aria-hidden />
        <h1 className="font-display text-2xl uppercase text-white">Season Fixtures</h1>
      </div>
      <p className="mb-5 text-[12px] leading-relaxed text-white/45">
        Every Premier League &amp; Champions League match of {year} — Addis Ababa time. Tap a day to see the games,
        then book your table.
      </p>

      {error && <GbErrorBox message={error} onRetry={onRetry} />}
      {loading && matches === null && !error && <GbSkeleton className="h-[380px] w-full" />}

      {matches !== null && !error && (
        <>
          <FixturesCalendar matches={matches} selected={selected} onSelect={setSelected} year={year} />

          {selected && (
            <section className="mt-5" aria-label="Fixtures for the selected day">
              <div className="mb-3 flex items-baseline justify-between">
                <h2 className="font-display text-lg uppercase text-white">{formatDayLong(selected)}</h2>
                {monthCount !== null && (
                  <span className="text-[10px] font-bold tracking-[0.15em] text-white/35">
                    {monthCount} THIS MONTH
                  </span>
                )}
              </div>

              {dayMatches.length === 0 ? (
                <div className="rounded-xl border border-white/[0.08] bg-panel p-6 text-center">
                  <p className="text-[12px] leading-relaxed text-white/50">
                    {selected.slice(5, 7) === "06" || selected.slice(5, 7) === "07"
                      ? "Summer break — no league fixtures. The bar is still open!"
                      : parseISODate(selected).getDay() === 4
                        ? "No fixtures that day — but it's Game & Groove Thursday! Live acoustic sessions from 12:30 PM."
                        : "No Premier League or Champions League fixtures that day — the big screens are all yours."}
                  </p>
                  {!isPastDay && (
                    <GbSecondaryButton
                      className="mt-4"
                      onClick={() => onReserveMatch(selected, null, "EVENING")}
                    >
                      RESERVE THIS DATE
                    </GbSecondaryButton>
                  )}
                </div>
              ) : (
                <div className="space-y-2.5">
                  {dayMatches.map((match) => (
                    <FixtureRow
                      key={match.id}
                      match={match}
                      action={
                        !isPastDay && match.status !== "FINISHED" ? (
                          <button
                            type="button"
                            onClick={() =>
                              onReserveMatch(
                                selected,
                                {
                                  id: match.id,
                                  label: `${match.homeTeam} vs ${match.awayTeam} · ${match.competition} — ${match.stage}`,
                                },
                                slotForMatch(match.kickoff)
                              )
                            }
                            className="shrink-0 rounded-lg border border-gold/40 bg-gold/10 px-3 py-2 text-[10px] font-extrabold tracking-wider text-gold transition hover:bg-gold/20"
                          >
                            BOOK
                          </button>
                        ) : undefined
                      }
                    />
                  ))}
                  {!isPastDay && (
                    <GbSecondaryButton
                      className="mt-2"
                      onClick={() => onReserveMatch(selected, null, "EVENING")}
                    >
                      RESERVE THIS DATE — NO MATCH
                    </GbSecondaryButton>
                  )}
                </div>
              )}
            </section>
          )}
        </>
      )}

      {matches !== null && matches.length === 0 && !error && (
        <div className="mt-4 rounded-xl border border-white/[0.08] bg-panel p-6 text-center text-[12px] text-white/50">
          Fixture data for {year} hasn&apos;t been loaded yet — check back soon.
        </div>
      )}
    </div>
  );
}
