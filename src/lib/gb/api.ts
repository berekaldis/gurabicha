// Typed API client for the Gura Bicha Sports Bar backend.

import type { GbEvent, GbMatch, GbMenuItem, GbReservation, GbTable, Slot } from "./types";

async function request<T>(url: string, init?: RequestInit): Promise<T> {
  let res: Response;
  try {
    res = await fetch(url, {
      headers: { "Content-Type": "application/json" },
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
}

export const gbApi = {
  events: () => request<{ events: GbEvent[] }>("/api/events"),

  matches: () => request<{ matches: GbMatch[] }>("/api/matches"),

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

  cancelReservation: (id: string) =>
    request<{ reservation: GbReservation }>(`/api/reservations/${id}`, { method: "DELETE" }),
};
