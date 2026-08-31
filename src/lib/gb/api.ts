// Typed API client for the Gura Bicha Sports Bar backend.
// When running inside Telegram, every request carries the Mini App's
// initData in the x-telegram-init-data header so the backend can verify
// the user server-side.

import type { GbEvent, GbMatch, GbMenuItem, GbReservation, GbTable, Slot } from "./types";

/** Raw Telegram initData ("" outside Telegram). */
function telegramInitData(): string {
  if (typeof window === "undefined") return "";
  const tg = (window as unknown as { Telegram?: { WebApp?: { initData?: string } } }).Telegram;
  return tg?.WebApp?.initData ?? "";
}

async function request<T>(url: string, init?: RequestInit): Promise<T> {
  const initData = telegramInitData();
  let res: Response;
  try {
    res = await fetch(url, {
      headers: {
        "Content-Type": "application/json",
        ...(initData ? { "x-telegram-init-data": initData } : {}),
      },
      cache: "no-store",
      ...init,
    });
  } catch {
    throw new Error("Network error — please check your connection and try again.");
  }
  const data: unknown = await res.json().catch(() => null);
  if (!res.ok) {
    const message =
      data && typeof data === "object" && "error" in data && typeof (data as { error: unknown }).error === "string"
        ? (data as { error: string }).error
        : `Request failed (${res.status})`;
    throw new Error(message);
  }
  return data as T;
}

export interface ReservationInput {
  date: string;
  slot: Slot;
  tableCode: string;
  guests: number;
  name: string;
  phone: string;
  email?: string;
  request?: string;
  matchId?: string;
}

export const gbApi = {
  events: () => request<{ events: GbEvent[] }>("/api/events"),

  matches: () => request<{ matches: GbMatch[] }>("/api/matches"),

  matchesByYear: (year: number) =>
    request<{ matches: GbMatch[] }>(`/api/matches?year=${year}`),

  menu: () => request<{ menu: GbMenuItem[] }>("/api/menu"),

  tables: (date: string, slot: Slot) =>
    request<{ tables: GbTable[]; remaining: number }>(
      `/api/tables?date=${encodeURIComponent(date)}&slot=${encodeURIComponent(slot)}`
    ),

  createReservation: (body: ReservationInput) =>
    request<{ reservation: GbReservation }>("/api/reservations", {
      method: "POST",
      body: JSON.stringify(body),
    }),

  reservationsByPhone: (phone: string) =>
    request<{ reservations: GbReservation[] }>(`/api/reservations?phone=${encodeURIComponent(phone)}`),

  reservationsMine: () => request<{ reservations: GbReservation[] }>(`/api/reservations?mine=1`),

  cancelReservation: (id: string) =>
    request<{ reservation: GbReservation }>(`/api/reservations/${id}`, { method: "DELETE" }),
};
