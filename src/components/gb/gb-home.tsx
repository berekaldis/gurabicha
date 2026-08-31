"use client";

import { ArrowRight, Music2, Tv, UtensilsCrossed } from "lucide-react";
import Image from "next/image";
import { useCallback, useEffect, useState } from "react";
import { toast } from "sonner";
import { gbApi } from "@/lib/gb/api";
import {
  formatDayShort,
  formatKickoff,
  teamInitials,
  type GbEvent,
  type GbMatch,
} from "@/lib/gb/types";
import {
  GbErrorBox,
  GbPill,
  GbSecondaryButton,
  GbSectionHeader,
  GbSkeleton,
} from "./gb-ui";

function EventCard({ event, wide }: { event: GbEvent; wide?: boolean }) {
  const { dow, day } = formatDayShort(event.date);
  return (
    <article
      className={`overflow-hidden rounded-[13px] border border-white/[0.08] bg-panel ${
        wide ? "w-full" : "min-w-[220px] max-w-[220px] shrink-0"
      }`}
    >
      <div className="relative h-[120px] w-full">
        <Image
          src={event.imageUrl ?? "/images/event-acoustic.jpg"}
          alt={event.title}
          fill
          sizes="(max-width: 700px) 90vw, 480px"
          className="object-cover"
        />
        <div className="absolute left-2.5 top-2.5 rounded-[7px] bg-gold px-2 py-1.5 text-center font-extrabold leading-none text-[#080808]">
          <span className="block text-[8px] tracking-wider">{dow}</span>
          <span className="mt-0.5 block text-[15px]">{day}</span>
        </div>
      </div>
      <div className="p-3">
        <h3 className="truncate font-display text-[17px] uppercase text-white">{event.title}</h3>
        <p className="mt-1 text-[11px] text-white/50">
          {event.category} · {event.startTime}
        </p>
        {wide && <p className="mt-2 line-clamp-2 text-[11px] leading-relaxed text-white/40">{event.description}</p>}
      </div>
    </article>
  );
}

function MatchCard({ match }: { match: GbMatch }) {
  const kickoff = formatKickoff(match.kickoff);
  return (
    <div className="relative overflow-hidden rounded-[14px] border border-gold/25 bg-panel p-4">
      <div
        aria-hidden
        className="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_50%_0%,rgba(245,189,25,0.09),transparent_55%)]"
      />
      <div className="relative">
        <div className="mb-4 flex items-center justify-center gap-2">
          <span className="text-center text-[10px] font-extrabold tracking-[0.15em] text-gold">
            {match.competition.toUpperCase()}
          </span>
          {match.live && (
            <span className="inline-flex items-center gap-1 rounded-full border border-red-500/30 bg-red-500/15 px-2 py-0.5 text-[9px] font-bold text-red-400">
              <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-red-500" aria-hidden />
              LIVE
            </span>
          )}
        </div>

        <div className="flex items-center justify-center gap-5">
          <div className="w-[92px] text-center">
            <div className="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-full border border-white/10 bg-[#181818] font-display text-sm text-gold">
              {teamInitials(match.homeTeam)}
            </div>
            <div className="truncate text-[11px] font-bold uppercase text-white">{match.homeTeam}</div>
          </div>
          <span className="font-display text-lg text-gold">VS</span>
          <div className="w-[92px] text-center">
            <div className="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-full border border-white/10 bg-[#181818] font-display text-sm text-gold">
              {teamInitials(match.awayTeam)}
            </div>
            <div className="truncate text-[11px] font-bold uppercase text-white">{match.awayTeam}</div>
          </div>
        </div>

        <p className="mt-4 text-center text-[13px] font-extrabold text-gold-light">
          {kickoff.dayLabel} · {kickoff.time}
          {match.live ? " · FIRST HALF" : ""}
        </p>

        <GbSecondaryButton
          className="mt-3.5"
          onClick={() =>
            toast.info("Every seat has a screen view — arrive 30 minutes early for the big-screen tables.")
          }
        >
          WATCH IT LIVE
        </GbSecondaryButton>
      </div>
    </div>
  );
}

const FEATURES = [
  { icon: Tv, label: "6 BIG SCREENS" },
  { icon: UtensilsCrossed, label: "KITCHEN TILL LATE" },
  { icon: Music2, label: "LIVE SESSIONS" },
];

export function GbHome({ onReserve, onMenu }: { onReserve: () => void; onMenu: () => void }) {
  const [events, setEvents] = useState<GbEvent[] | null>(null);
  const [matches, setMatches] = useState<GbMatch[] | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [showAllEvents, setShowAllEvents] = useState(false);
  const [showAllMatches, setShowAllMatches] = useState(false);
  const [reloadKey, setReloadKey] = useState(0);

  useEffect(() => {
    let alive = true;
    Promise.all([gbApi.events(), gbApi.matches()]).then(
      ([e, m]) => {
        if (!alive) return;
        setEvents(e.events);
        setMatches(m.matches);
        setError(null);
      },
      (err: unknown) => {
        if (!alive) return;
        setError((err as Error).message);
      }
    );
    return () => {
      alive = false;
    };
  }, [reloadKey]);

  const retry = useCallback(() => {
    setEvents(null);
    setMatches(null);
    setError(null);
    setReloadKey((k) => k + 1);
  }, []);

  const featured = events?.find((e) => e.featured) ?? events?.[0];
  const main = matches?.[0];
  const otherMatches = matches?.slice(1) ?? [];

  return (
    <div>
      {/* ============ HERO ============ */}
      <section className="relative flex min-h-[470px] items-end overflow-hidden px-5 pb-7" aria-label="Featured event">
        <Image
          src="/images/hero-gura.jpg"
          alt="Gura Bicha Sports Bar stage lit in gold"
          fill
          priority
          sizes="(max-width: 700px) 100vw, 480px"
          className="object-cover"
        />
        <div
          aria-hidden
          className="absolute inset-0 bg-gradient-to-t from-[#050505] via-[#050505]/70 to-[#050505]/10"
        />
        <div className="relative w-full pt-24">
          <p className="mb-2 text-[12px] font-bold uppercase tracking-[0.2em] text-gold">Every Thursday</p>
          <h1 className="font-display text-[46px] uppercase leading-[0.95] tracking-tight text-white">
            Game &<br />
            <span className="text-gold">Groove</span>
          </h1>
          <p className="font-script mt-2 text-[22px] text-white">Acoustic Thursdays</p>

          <div className="mb-4 mt-4 flex flex-wrap gap-2">
            <GbPill>{featured ? `${featured.startTime} – ${featured.endTime}` : "12:30 PM – 3:30 PM"}</GbPill>
            <GbPill>Live Acoustic</GbPill>
          </div>

          <p className="mb-5 max-w-[350px] text-[13px] leading-relaxed text-white/75">
            Live music. Great company. The game on. Experience Thursday differently at Gura Bicha.
          </p>

          <button
            type="button"
            onClick={onReserve}
            className="flex w-full items-center justify-center gap-2 rounded-[10px] bg-gradient-to-br from-gold-light to-gold py-4 text-[13px] font-extrabold tracking-[0.05em] text-[#080808] transition hover:brightness-110 active:scale-[0.99]"
          >
            RESERVE YOUR TABLE
            <ArrowRight className="h-4 w-4" aria-hidden />
          </button>
          <button
            type="button"
            onClick={onMenu}
            className="mt-2.5 w-full rounded-[10px] border border-gold/30 py-3.5 text-[13px] font-bold tracking-[0.05em] text-gold-light transition hover:bg-gold/10"
          >
            SEE THE MENU
          </button>
        </div>
      </section>

      {/* ============ FEATURES ============ */}
      <section className="flex items-stretch gap-2.5 px-5 pt-5" aria-label="Venue highlights">
        {FEATURES.map(({ icon: Icon, label }) => (
          <div
            key={label}
            className="flex-1 rounded-xl border border-white/[0.06] bg-panel px-2 py-3 text-center"
          >
            <Icon className="mx-auto mb-1.5 h-4 w-4 text-gold" aria-hidden />
            <span className="block text-[9px] font-bold tracking-wider text-white/65">{label}</span>
          </div>
        ))}
      </section>

      {/* ============ MATCHES ============ */}
      <section id="gb-matches" className="scroll-mt-[76px] px-5 pt-7" aria-label="Tonight's matches">
        <GbSectionHeader
          title="Tonight at Gura Bicha"
          linkLabel={showAllMatches ? "SHOW LESS" : "VIEW ALL"}
          onLink={() => setShowAllMatches((v) => !v)}
        />
        {error && <GbErrorBox message={error} onRetry={retry} />}
        {!error && matches === null && <GbSkeleton className="h-[240px] w-full" />}
        {matches !== null && matches.length === 0 && !error && (
          <div className="rounded-[14px] border border-white/[0.08] bg-panel p-6 text-center text-[12px] text-white/50">
            No matches on the schedule right now — check back soon.
          </div>
        )}
        {main && <MatchCard match={main} />}
        {showAllMatches && otherMatches.length > 0 && (
          <ul className="mt-3 space-y-2">
            {otherMatches.map((m) => {
              const k = formatKickoff(m.kickoff);
              return (
                <li
                  key={m.id}
                  className="flex items-center justify-between rounded-xl border border-white/[0.07] bg-panel px-3.5 py-3"
                >
                  <span className="text-[12px] font-semibold text-white">
                    {m.homeTeam} <span className="text-gold">v</span> {m.awayTeam}
                  </span>
                  <span className="text-right text-[10px] text-white/45">
                    <span className="block text-white/70">{k.dayLabel} · {k.time}</span>
                    {m.competition}
                  </span>
                </li>
              );
            })}
          </ul>
        )}
      </section>

      {/* ============ UPCOMING EVENTS ============ */}
      <section className="px-5 pb-9 pt-7" aria-label="Upcoming events">
        <GbSectionHeader
          title="Upcoming"
          linkLabel={showAllEvents ? "SHOW LESS" : "ALL EVENTS"}
          onLink={() => setShowAllEvents((v) => !v)}
        />
        {!error && events === null && (
          <div className="flex gap-3">
            <GbSkeleton className="h-[190px] w-[220px] shrink-0" />
            <GbSkeleton className="h-[190px] w-[220px] shrink-0" />
          </div>
        )}
        {events !== null && events.length === 0 && !error && (
          <div className="rounded-[13px] border border-white/[0.08] bg-panel p-6 text-center text-[12px] text-white/50">
            No upcoming events yet — follow us for announcements.
          </div>
        )}
        {error === null && events !== null && events.length > 0 && !showAllEvents && (
          <div className="no-scrollbar -mx-5 flex gap-3 overflow-x-auto px-5 pb-1">
            {events.slice(0, 4).map((event) => (
              <EventCard key={event.id} event={event} />
            ))}
          </div>
        )}
        {events !== null && events.length > 0 && showAllEvents && (
          <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
            {events.map((event) => (
              <EventCard key={event.id} event={event} wide />
            ))}
          </div>
        )}
      </section>
    </div>
  );
}
