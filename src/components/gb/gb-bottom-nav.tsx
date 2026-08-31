"use client";

import { CalendarCheck, Home, Trophy, UtensilsCrossed } from "lucide-react";
import type { Screen } from "@/lib/gb/types";

function NavItem({
  icon: Icon,
  label,
  active,
  onClick,
}: {
  icon: typeof Home;
  label: string;
  active?: boolean;
  onClick: () => void;
}) {
  return (
    <button
      type="button"
      onClick={onClick}
      aria-current={active ? "page" : undefined}
      className={`flex flex-col items-center justify-center gap-1.5 py-2.5 text-[9px] font-bold tracking-[0.12em] transition ${
        active ? "text-gold" : "text-white/40 hover:text-white/70"
      }`}
    >
      <Icon className="h-5 w-5" strokeWidth={2.2} aria-hidden />
      {label}
    </button>
  );
}

export function GbBottomNav({
  screen,
  onHome,
  onMatches,
  onReserve,
  onMenu,
}: {
  screen: Screen;
  onHome: () => void;
  onMatches: () => void;
  onReserve: () => void;
  onMenu: () => void;
}) {
  return (
    <nav
      aria-label="Bottom navigation"
      className="sticky bottom-0 z-40 shrink-0 border-t border-white/[0.08] bg-[rgba(8,8,8,0.96)] backdrop-blur-md"
    >
      <div className="grid grid-cols-4 items-end">
        <NavItem icon={Home} label="HOME" active={screen === "home"} onClick={onHome} />
        <NavItem icon={Trophy} label="MATCHES" onClick={onMatches} />
        <button
          type="button"
          onClick={onReserve}
          aria-label="Reserve a table"
          className="flex flex-col items-center gap-1 pb-2.5"
        >
          <span className="-mt-6 grid h-[52px] w-[52px] place-items-center rounded-full border-4 border-[#080808] bg-gold text-[#050505] shadow-lg shadow-gold/25 transition hover:brightness-110 active:scale-95">
            <CalendarCheck className="h-6 w-6" strokeWidth={2.4} aria-hidden />
          </span>
          <span className={`text-[9px] font-bold tracking-[0.12em] ${screen === "reserve" ? "text-gold" : "text-white/50"}`}>
            RESERVE
          </span>
        </button>
        <NavItem icon={UtensilsCrossed} label="MENU" active={screen === "menu"} onClick={onMenu} />
      </div>
    </nav>
  );
}
