"use client";

import { CalendarCheck, Clock, Home, MapPin, Menu, Phone, Share2, Ticket, Trophy, UtensilsCrossed } from "lucide-react";
import { useState } from "react";
import { toast } from "sonner";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet";
import { shareViaTelegram, useTelegram, useTelegramBackInterceptor } from "@/lib/gb/telegram";
import type { Screen } from "@/lib/gb/types";

const NAV_LINKS: { screen: Screen; label: string; icon: typeof Home }[] = [
  { screen: "home", label: "Home", icon: Home },
  { screen: "fixtures", label: "Fixtures & Events", icon: Trophy },
  { screen: "reserve", label: "Reserve a Table", icon: CalendarCheck },
  { screen: "menu", label: "Food & Drinks", icon: UtensilsCrossed },
  { screen: "reservations", label: "My Reservations", icon: Ticket },
];

export function GbHeader({
  onNavigate,
  onReserve,
}: {
  onNavigate: (screen: Screen) => void;
  onReserve: () => void;
}) {
  const [open, setOpen] = useState(false);
  const { user } = useTelegram();

  // Telegram hardware back closes the sheet before navigating screens.
  useTelegramBackInterceptor(
    open
      ? () => {
          setOpen(false);
          return true;
        }
      : null
  );

  const go = (screen: Screen) => {
    setOpen(false);
    onNavigate(screen);
  };

  const shareApp = () => {
    const url = typeof window !== "undefined" ? window.location.href : "";
    const sent = shareViaTelegram(
      "Watch every PL & Champions League game at Gura Bicha Sports Bar 🏆",
      url
    );
    if (!sent) {
      void navigator.clipboard
        ?.writeText(url)
        .then(() => toast.success("App link copied to clipboard"))
        .catch(() => toast.error("Could not copy the link"));
    }
  };

  return (
    <>
      <header className="sticky top-0 z-40 flex h-[68px] shrink-0 items-center justify-between border-b border-white/[0.06] bg-[rgba(5,5,5,0.92)] px-5 backdrop-blur-md">
        <button
          type="button"
          aria-label="Open navigation menu"
          onClick={() => setOpen(true)}
          className="grid h-10 w-10 place-items-center rounded-xl border border-gold/30 text-gold transition hover:bg-gold/10"
        >
          <Menu className="h-5 w-5" />
        </button>

        <div className="select-none text-center">
          <div className="font-display text-xl leading-none tracking-wide text-gold">GURA BICHA</div>
          <div className="mt-1 text-[8px] tracking-[0.35em] text-white/90">SPORTS BAR</div>
        </div>

        {/* Telegram user chip (or reservations shortcut outside Telegram). */}
        <button
          type="button"
          aria-label={user ? `My reservations (${user.firstName})` : "My reservations"}
          onClick={() => go("reservations")}
          className="grid h-10 w-10 place-items-center overflow-hidden rounded-xl border border-gold/30 text-gold transition hover:bg-gold/10"
        >
          {user ? (
            <span
              className="grid h-full w-full place-items-center bg-gold/15 font-display text-[13px] font-bold text-gold"
              title={user.username ? `@${user.username}` : user.firstName}
            >
              {user.firstName.charAt(0).toUpperCase()}
            </span>
          ) : (
            <Ticket className="h-5 w-5" />
          )}
        </button>
      </header>

      <Sheet open={open} onOpenChange={setOpen}>
        <SheetContent
          side="left"
          className="w-[300px] border-gold/20 bg-[#0b0b0b] p-0 text-white"
        >
          <SheetHeader className="border-b border-white/[0.06] p-5 text-left">
            <SheetTitle className="font-display text-2xl tracking-wide text-gold">GURA BICHA</SheetTitle>
            <SheetDescription className="text-[9px] tracking-[0.35em] text-white/70">
              SPORTS BAR · ADDIS ABABA
            </SheetDescription>
            {user && (
              <SheetDescription className="pt-1 text-[11px] tracking-normal text-gold-light">
                Hi, {user.firstName} {user.lastName ?? ""} — your table awaits.
              </SheetDescription>
            )}
          </SheetHeader>

          <nav aria-label="Main navigation" className="flex flex-col gap-1 p-3">
            {NAV_LINKS.map(({ screen, label, icon: Icon }) => (
              <button
                key={screen}
                type="button"
                onClick={() => go(screen)}
                className="flex items-center gap-3 rounded-xl px-3 py-3 text-left text-[13px] font-semibold text-white/80 transition hover:bg-gold/10 hover:text-gold"
              >
                <Icon className="h-4 w-4 text-gold" aria-hidden />
                {label}
              </button>
            ))}
            <button
              type="button"
              onClick={() => {
                setOpen(false);
                shareApp();
              }}
              className="flex items-center gap-3 rounded-xl px-3 py-3 text-left text-[13px] font-semibold text-white/80 transition hover:bg-gold/10 hover:text-gold"
            >
              <Share2 className="h-4 w-4 text-gold" aria-hidden />
              Share this app
            </button>
          </nav>

          <div className="border-t border-white/[0.06] p-5">
            <p className="mb-3 text-[10px] font-bold tracking-[0.2em] text-gold">VISIT US</p>
            <ul className="space-y-2.5 text-[12px] text-white/65">
              <li className="flex items-start gap-2.5">
                <MapPin className="mt-0.5 h-3.5 w-3.5 shrink-0 text-gold/80" aria-hidden />
                Bole Road, behind Getu Commercial, Addis Ababa
              </li>
              <li className="flex items-start gap-2.5">
                <Clock className="mt-0.5 h-3.5 w-3.5 shrink-0 text-gold/80" aria-hidden />
                Mon – Thu 11 AM – 11 PM · Fri – Sun 11 AM – 12 AM
              </li>
              <li className="flex items-start gap-2.5">
                <Phone className="mt-0.5 h-3.5 w-3.5 shrink-0 text-gold/80" aria-hidden />
                +251 911 234 567
              </li>
            </ul>
          </div>

          <div className="px-5 pb-6">
            <button
              type="button"
              onClick={() => {
                setOpen(false);
                onReserve();
              }}
              className="w-full rounded-[10px] bg-gradient-to-br from-gold-light to-gold py-3.5 text-[12px] font-extrabold tracking-[0.05em] text-[#080808] transition hover:brightness-110"
            >
              RESERVE YOUR TABLE
            </button>
          </div>
        </SheetContent>
      </Sheet>
    </>
  );
}
