"use client";

import { AnimatePresence, motion } from "framer-motion";
import { useCallback, useEffect, useState } from "react";
import { Toaster } from "sonner";
import { gbApi } from "@/lib/gb/api";
import {
  cloudStorageGet,
  GB_DETAILS_KEY,
  haptics,
  TelegramProvider,
  useTelegram,
  useTelegramBackButton,
  useTelegramBackInterceptors,
} from "@/lib/gb/telegram";
import {
  type DraftMatch,
  type GbMatch,
  type ReserveDraft,
  type SavedDetails,
  type Screen,
  type Slot,
  toISODate,
} from "@/lib/gb/types";
import { GbBottomNav } from "./gb-bottom-nav";
import { GbFixturesScreen } from "./gb-fixtures";
import { GbHeader } from "./gb-header";
import { GbHome } from "./gb-home";
import { GbMenu } from "./gb-menu";
import { GbReserve } from "./gb-reserve";
import { GbReservations } from "./gb-reservations";

const EMPTY_DRAFT: ReserveDraft = {
  date: null,
  slot: "EVENING",
  table: null,
  guests: 4,
  match: null,
  reservation: null,
};

const fade = {
  initial: { opacity: 0, y: 10 },
  animate: { opacity: 1, y: 0 },
  exit: { opacity: 0, y: -8 },
  transition: { duration: 0.18 },
};

function GbAppInner() {
  const [screen, setScreen] = useState<Screen>("home");
  const [draft, setDraft] = useState<ReserveDraft>(EMPTY_DRAFT);
  const [savedDetails, setSavedDetails] = useState<SavedDetails | null>(null);
  const [yearMatches, setYearMatches] = useState<GbMatch[] | null>(null);
  const [yearMatchesError, setYearMatchesError] = useState<string | null>(null);
  const [yearMatchesLoading, setYearMatchesLoading] = useState(true);
  const year = new Date().getFullYear();

  // Restore saved guest details: Telegram CloudStorage first (persists across
  // devices), then localStorage as fallback. Async so no cascading render.
  useEffect(() => {
    let alive = true;
    void (async () => {
      try {
        const [cloud, local] = await Promise.all([
          cloudStorageGet(GB_DETAILS_KEY),
          Promise.resolve(localStorage.getItem(GB_DETAILS_KEY)),
        ]);
        const raw = cloud ?? local;
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

  const loadYearMatches = useCallback(() => {
    setYearMatchesLoading(true);
    setYearMatchesError(null);
    gbApi
      .matchesByYear(year)
      .then(
        (res) => {
          setYearMatches(res.matches);
          setYearMatchesLoading(false);
        },
        (err: unknown) => {
          setYearMatchesError((err as Error).message);
          setYearMatchesLoading(false);
        }
      );
  }, [year]);

  useEffect(() => {
    // Deferred one microtask: the fetch flips loading state synchronously,
    // which cascades if run directly inside the effect body.
    void Promise.resolve().then(loadYearMatches);
  }, [loadYearMatches]);

  const goTo = useCallback((next: Screen) => {
    haptics.tap();
    setScreen(next);
    window.scrollTo({ top: 0, behavior: "smooth" });
  }, []);

  // Telegram hardware-style back: give the deepest open UI (reserve wizard
  // steps, nav sheet) the first chance to handle it, then fall back to home.
  const { runInterceptors } = useTelegramBackInterceptors();
  useTelegramBackButton(screen !== "home", () => {
    if (!runInterceptors()) goTo("home");
  });

  const startReserve = useCallback(() => {
    setDraft({ ...EMPTY_DRAFT, date: toISODate(new Date()) });
    setScreen("reserve");
    window.scrollTo({ top: 0, behavior: "smooth" });
  }, []);

  /** Jump into the wizard from the fixtures screen (or a match card) with
   *  date + optional match + slot pre-filled; land on the table step. */
  const reserveFor = useCallback((date: string, match: DraftMatch | null, slot: Slot) => {
    haptics.tap();
    setDraft({ ...EMPTY_DRAFT, date, match, slot });
    setScreen("reserve");
    window.scrollTo({ top: 0, behavior: "smooth" });
  }, []);

  return (
    <div className="relative mx-auto flex min-h-screen w-full max-w-[480px] flex-col bg-ink text-[#f7f7f7] shadow-[0_30px_100px_rgba(0,0,0,0.55)] md:my-6 md:min-h-[calc(100vh-3rem)] md:rounded-3xl md:border md:border-gold/15">
      <GbHeader onNavigate={goTo} onReserve={startReserve} />

      <main className="flex-1">
        <AnimatePresence mode="wait" initial={false}>
          <motion.div key={screen} {...fade}>
            {screen === "home" && (
              <GbHome
                onReserve={startReserve}
                onFixtures={() => goTo("fixtures")}
                onMenu={() => goTo("menu")}
                onReserveMatch={reserveFor}
              />
            )}
            {screen === "fixtures" && (
              <GbFixturesScreen
                matches={yearMatches}
                loading={yearMatchesLoading}
                error={yearMatchesError}
                onRetry={loadYearMatches}
                onReserveMatch={reserveFor}
              />
            )}
            {screen === "reserve" && (
              <GbReserve
                draft={draft}
                onChange={setDraft}
                savedDetails={savedDetails}
                onSaveDetails={setSavedDetails}
                yearMatches={yearMatches}
                yearMatchesLoading={yearMatchesLoading}
                startStep={draft.match ? "table" : "date"}
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
        onFixtures={() => goTo("fixtures")}
        onReserve={startReserve}
        onMenu={() => goTo("menu")}
      />

      <Toaster theme="dark" position="top-center" richColors closeButton />
    </div>
  );
}

export default function GbApp() {
  return (
    <TelegramProvider>
      <GbAppInner />
    </TelegramProvider>
  );
}
