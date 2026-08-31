// Shared types, constants and helpers for the Gura Bicha Sports Bar app.

export type Slot = "LUNCH" | "EVENING";

export type Screen = "home" | "reserve" | "menu" | "reservations";

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
  kickoff: string;
  live: boolean;
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

export function isThursday(iso: string): boolean {
  return parseISODate(iso).getDay() === 4;
}

/** ISO date of the next upcoming Thursday (picks the following one if today is Thursday). */
export function nextThursdayISO(): string {
  const d = new Date();
  d.setHours(0, 0, 0, 0);
  const delta = (4 - d.getDay() + 7) % 7;
  d.setDate(d.getDate() + (delta === 0 ? 7 : delta));
  return toISODate(d);
}

const addisDayKey = (d: Date) =>
  new Intl.DateTimeFormat("en-CA", {
    timeZone: "Africa/Addis_Ababa",
    year: "numeric",
    month: "2-digit",
    day: "2-digit",
  }).format(d);

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
  return [
    "BEGIN:VCALENDAR",
    "VERSION:2.0",
    "PRODID:-//Gura Bicha Sports Bar//Reservation//EN",
    "BEGIN:VEVENT",
    `UID:${r.code}@gurabicha`,
    `DTSTAMP:${stamp}`,
    `DTSTART:${start}`,
    `DTEND:${end}`,
    `SUMMARY:${esc(`Game & Groove at Gura Bicha (${r.code})`)}`,
    `DESCRIPTION:${esc(
      `Table ${r.tableCode} · ${r.guests} guests · ${meta.time}. Questions? Call +251 911 234 567.`
    )}`,
    "LOCATION:Gura Bicha Sports Bar\\, Bole Road\\, Addis Ababa",
    "END:VEVENT",
    "END:VCALENDAR",
  ].join("\r\n");
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
