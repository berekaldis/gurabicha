"use client";

import { AnimatePresence, motion } from "framer-motion";
import { useCallback, useEffect, useState } from "react";
import { Toaster } from "sonner";
import { GbBottomNav } from "./gb-bottom-nav";
import { GbHeader } from "./gb-header";
import { GbHome } from "./gb-home";
import { GbMenu } from "./gb-menu";
import { GbReserve } from "./gb-reserve";
import { GbReservations } from "./gb-reservations";
import {
  nextThursdayISO,
  type ReserveDraft,
  type SavedDetails,
  type Screen,
} from "@/lib/gb/types";

const EMPTY_DRAFT: ReserveDraft = {
  date: null,
  slot: "LUNCH",
  table: null,
  guests: 4,
  reservation: null,
};

const fade = {
  initial: { opacity: 0, y: 10 },
  animate: { opacity: 1, y: 0 },
  exit: { opacity: 0, y: -8 },
  transition: { duration: 0.18 },
};

export default function GbApp() {
  const [screen, setScreen] = useState<Screen>("home");
  const [draft, setDraft] = useState<ReserveDraft>(EMPTY_DRAFT);
  const [savedDetails, setSavedDetails] = useState<SavedDetails | null>(null);

  // Restore saved guest details from localStorage (async so no cascading render).
  useEffect(() => {
    let alive = true;
    void (async () => {
      try {
        const raw = localStorage.getItem("gb:details");
        if (!raw) return;
        const parsed = JSON.parse(raw) as SavedDetails;
        await Promise.resolve();
        if (alive) setSavedDetails(parsed);
      } catch {
        // storage unavailable or corrupt — ignore
      }
    })();
    return () => {
      alive = false;
    };
  }, []);

  const goTo = useCallback((next: Screen) => {
    setScreen(next);
    window.scrollTo({ top: 0, behavior: "smooth" });
  }, []);

  const startReserve = useCallback(() => {
    setDraft({ ...EMPTY_DRAFT, date: nextThursdayISO() });
    setScreen("reserve");
    window.scrollTo({ top: 0, behavior: "smooth" });
  }, []);

  const scrollToMatches = useCallback(() => {
    setScreen("home");
    window.setTimeout(() => {
      document.getElementById("gb-matches")?.scrollIntoView({ behavior: "smooth", block: "start" });
    }, 80);
  }, []);

  return (
    <div className="relative mx-auto flex min-h-screen w-full max-w-[480px] flex-col bg-ink text-[#f7f7f7] shadow-[0_30px_100px_rgba(0,0,0,0.55)] md:my-6 md:min-h-[calc(100vh-3rem)] md:rounded-3xl md:border md:border-gold/15">
      <GbHeader onNavigate={goTo} onReserve={startReserve} />

      <main className="flex-1">
        <AnimatePresence mode="wait" initial={false}>
          <motion.div key={screen} {...fade}>
            {screen === "home" && <GbHome onReserve={startReserve} onMenu={() => goTo("menu")} />}
            {screen === "reserve" && (
              <GbReserve
                draft={draft}
                onChange={setDraft}
                savedDetails={savedDetails}
                onSaveDetails={setSavedDetails}
                onHome={() => goTo("home")}
                onViewReservations={() => goTo("reservations")}
              />
            )}
            {screen === "menu" && <GbMenu />}
            {screen === "reservations" && (
              <GbReservations savedDetails={savedDetails} onReserve={startReserve} />
            )}
          </motion.div>
        </AnimatePresence>
      </main>

      <GbBottomNav
        screen={screen}
        onHome={() => goTo("home")}
        onMatches={scrollToMatches}
        onReserve={startReserve}
        onMenu={() => goTo("menu")}
      />

      <Toaster theme="dark" position="top-center" richColors closeButton />
    </div>
  );
}
