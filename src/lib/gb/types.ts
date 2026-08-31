// Shared types, constants and helpers for the Gura Bicha Sports Bar app.

export type Slot = "LUNCH" | "EVENING";

export type Screen = "home" | "fixtures" | "reserve" | "menu" | "reservations";

export interface GbEvent {
  id: string;
  title: string;
  description: string;
  category: string;
  date: string;
  startTime: string;
  endTime: string;
  imageUrl: string | null;
  featured: boolean;
}

export interface GbMatch {
  id: string;
  homeTeam: string;
  awayTeam: string;
  competition: string;
  season: string;
  stage: string;
  venue: string | null;
  kickoff: string;
  status: "SCHEDULED" | "LIVE" | "FINISHED";
  homeScore: number | null;
  awayScore: number | null;
  featured: boolean;
  live: boolean;
}

/** Match reference attached to a reservation draft (id + display label). */
export interface DraftMatch {
  id: string;
  label: string;
}

export interface GbTable {
  code: string;
  zone: string;
  capacity: number;
  seats: string;
  available: boolean;
}

export interface GbMenuItem {
  id: string;
  name: string;
  description: string;
  category: string;
  price: number;
  popular: boolean;
}

export interface GbReservation {
  id: string;
  code: string;
  date: string;
  slot: Slot;
  tableCode: string;
  guests: number;
  name: string;
  phone: string;
  email: string | null;
  request: string | null;
  matchId: string | null;
  matchLabel: string | null;
  status: "CONFIRMED" | "CANCELLED";
  createdAt: string;
}

export interface SavedDetails {
  name: string;
  phone: string;
  email: string;
}

/** In-progress reservation wizard state, owned by the app shell. */
export interface ReserveDraft {
  date: string | null;
  slot: Slot;
  table: GbTable | null;
  guests: number;
  match: DraftMatch | null;
  reservation: GbReservation | null;
}

export const SLOT_LIST: Slot[] = ["LUNCH", "EVENING"];

export const SLOT_META: Record<
  Slot,
  { label: string; time: string; short: string; icsStartHour: number; icsStartMinute: number; icsHours: number }
> = {
  LUNCH: {
    label: "Lunch Session",
    time: "12:30 PM – 3:30 PM",
    short: "12:30 PM",
    icsStartHour: 9,
    icsStartMinute: 30,
    icsHours: 3,
  },
  EVENING: {
    label: "Evening Session",
    time: "7:00 PM – 11:00 PM",
    short: "7:00 PM",
    icsStartHour: 16,
    icsStartMinute: 0,
    icsHours: 4,
  },
};

/** Format a Date as a local YYYY-MM-DD string. */
export function toISODate(d: Date): string {
  const pad = (n: number) => String(n).padStart(2, "0");
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
}

/** Parse a YYYY-MM-DD string as a local calendar date (avoids UTC off-by-one). */
export function parseISODate(s: string): Date {
  const [y, m, d] = s.slice(0, 10).split("-").map(Number);
  return new Date(y, (m ?? 1) - 1, d ?? 1);
}

/** "Thursday, September 3" */
export function formatDayLong(iso: string): string {
  return parseISODate(iso).toLocaleDateString("en-US", {
    weekday: "long",
    month: "long",
    day: "numeric",
  });
}

/** { dow: "THU", day: "3" } */
export function formatDayShort(iso: string): { dow: string; day: string } {
  const d = parseISODate(iso);
  return {
    dow: d.toLocaleDateString("en-US", { weekday: "short" }).toUpperCase(),
    day: String(d.getDate()),
  };
}

/** Calendar-day key in the venue timezone (Africa/Addis_Ababa): YYYY-MM-DD. */
export function addisDayKey(d: Date): string {
  return new Intl.DateTimeFormat("en-CA", {
    timeZone: "Africa/Addis_Ababa",
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  }).format(d);
}

/** Kickoff label in venue timezone: { dayLabel: "TODAY" | "TOMORROW" | "SAT", time: "19:00" } */
export function formatKickoff(iso: string): { dayLabel: string; time: string } {
  const d = new Date(iso);
  const time = new Intl.DateTimeFormat("en-GB", {
    timeZone: "Africa/Addis_Ababa",
    hour: "2-digit",
    minute: "2-digit",
    hour12: false,
  }).format(d);
  const key = addisDayKey(d);
  const now = new Date();
  let dayLabel = d.toLocaleDateString("en-US", { weekday: "short" }).toUpperCase();
  if (key === addisDayKey(now)) dayLabel = "TODAY";
  else if (key === addisDayKey(new Date(now.getTime() + 86_400_000))) dayLabel = "TOMORROW";
  return { dayLabel, time };
}

/** Kickoff time only, in venue timezone: "19:00" */
export function formatKickoffTime(iso: string): string {
  return new Intl.DateTimeFormat("en-GB", {
    timeZone: "Africa/Addis_Ababa",
    hour: "2-digit",
    minute: "2-digit",
    hour12: false,
  }).format(new Date(iso));
}

/** TRUE when the Addis calendar day of `iso` is today. */
export function isTodayKey(key: string): boolean {
  return key === addisDayKey(new Date());
}

/** TRUE when the match has already kicked off. */
export function isMatchPast(iso: string): boolean {
  return new Date(iso).getTime() < Date.now();
}

/** TRUE when the match is within its live window (kicked off, less than 2.5h ago). */
export function isMatchLive(iso: string): boolean {
  const t = new Date(iso).getTime();
  const now = Date.now();
  return t <= now && now - t < 2.5 * 60 * 60 * 1000;
}

/** Short display label for a match: "Arsenal v Chelsea — Premier League · Matchweek 24" */
export function matchLabel(m: {
  homeTeam: string;
  awayTeam: string;
  competition: string;
  stage: string;
}): string {
  return `${m.homeTeam} vs ${m.awayTeam} · ${m.competition} — ${m.stage}`;
}

/** Build an .ics calendar file content for a confirmed reservation (venue is UTC+3). */
export function buildIcs(r: GbReservation): string {
  const meta = SLOT_META[r.slot];
  const [y, m, d] = r.date.slice(0, 10).split("-").map(Number);
  const pad = (n: number) => String(n).padStart(2, "0");
  const start = `${y}${pad(m)}${pad(d)}T${pad(meta.icsStartHour)}${pad(meta.icsStartMinute)}00Z`;
  const endHour = meta.icsStartHour + meta.icsHours;
  const end = `${y}${pad(m)}${pad(d)}T${pad(endHour)}${pad(meta.icsStartMinute)}00Z`;
  const stamp = new Date().toISOString().replace(/[-:]/g, "").split(".")[0] + "Z";
  const esc = (s: string) => s.replace(/\\/g, "\\\\").replace(/,/g, "\\,").replace(/;/g, "\\;");
  const title = r.matchLabel ? `Match Day at Gura Bicha (${r.code})` : `Game & Groove at Gura Bicha (${r.code})`;
  const matchLine = r.matchLabel ? `\\, ${r.matchLabel}` : "";
  return [
    "BEGIN:VCALENDAR",
    "VERSION:2.0",
    "PRODID:-//Gura Bicha Sports Bar//Reservation//EN",
    "BEGIN:VEVENT",
    `UID:${r.code}@gurabicha`,
    `DTSTAMP:${stamp}`,
    `DTSTART:${start}`,
    `DTEND:${end}`,
    `SUMMARY:${esc(title)}`,
    `DESCRIPTION:${esc(
      `Table ${r.tableCode} · ${r.guests} guests · ${meta.time}${matchLine}. Questions? Call +251 911 234 567.`
    )}`,
    "LOCATION:Gura Bicha Sports Bar\\, Bole Road\\, Addis Ababa",
    "END:VEVENT",
    "END:VCALENDAR",
  ].join("\r\n");
}

/** Google Calendar "add event" URL for a reservation (venue is UTC+3). */
export function googleCalendarUrl(r: GbReservation): string {
  const meta = SLOT_META[r.slot];
  const [y, m, d] = r.date.slice(0, 10).split("-").map(Number);
  const pad = (n: number) => String(n).padStart(2, "0");
  const start = `${y}${pad(m)}${pad(d)}T${pad(meta.icsStartHour)}${pad(meta.icsStartMinute)}00Z`;
  const endHour = meta.icsStartHour + meta.icsHours;
  const end = `${y}${pad(m)}${pad(d)}T${pad(endHour)}${pad(meta.icsStartMinute)}00Z`;
  const params = new URLSearchParams({
    action: "TEMPLATE",
    text: r.matchLabel
      ? `Match Day at Gura Bicha (${r.code})`
      : `Game & Groove at Gura Bicha (${r.code})`,
    dates: `${start}/${end}`,
    details: `Table ${r.tableCode} · ${r.guests} guests · ${meta.time}${
      r.matchLabel ? ` · ${r.matchLabel}` : ""
    }`,
    location: "Gura Bicha Sports Bar, Bole Road, Addis Ababa",
  });
  return `https://calendar.google.com/calendar/render?${params.toString()}`;
}

/** Team initials for logo circles: "Man City" -> "MC" */
export function teamInitials(team: string): string {
  return team
    .split(/\s+/)
    .map((w) => w[0])
    .join("")
    .slice(0, 3)
    .toUpperCase();
}
