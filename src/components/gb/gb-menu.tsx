"use client";

import { Beer, Search, SearchX, Soup, Sparkles, Star, UtensilsCrossed } from "lucide-react";
import { useCallback, useEffect, useMemo, useState } from "react";
import { gbApi } from "@/lib/gb/api";
import type { GbMenuItem } from "@/lib/gb/types";
import { GbErrorBox, GbSkeleton } from "./gb-ui";

const CATEGORIES = ["ALL", "FOOD", "DRINKS", "SHISHA"] as const;
type Category = (typeof CATEGORIES)[number];

const CATEGORY_ICONS: Record<string, typeof UtensilsCrossed> = {
  FOOD: Soup,
  DRINKS: Beer,
  SHISHA: Sparkles,
};

function MenuRow({ item }: { item: GbMenuItem }) {
  const Icon = CATEGORY_ICONS[item.category] ?? UtensilsCrossed;
  return (
    <li className="flex items-start gap-3 rounded-xl border border-white/[0.06] bg-panel p-3.5 transition hover:border-gold/25">
      <span className="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-gold/25 bg-black/40 text-gold">
        <Icon className="h-4 w-4" aria-hidden />
      </span>
      <span className="min-w-0 flex-1">
        <span className="flex flex-wrap items-center gap-2">
          <span className="text-[13px] font-bold text-white">{item.name}</span>
          {item.popular && (
            <span className="inline-flex items-center gap-1 rounded-full border border-gold/40 px-1.5 py-0.5 text-[8px] font-extrabold tracking-wider text-gold">
              <Star className="h-2.5 w-2.5 fill-gold" aria-hidden />
              POPULAR
            </span>
          )}
        </span>
        <span className="mt-0.5 block text-[11px] leading-relaxed text-white/45">{item.description}</span>
      </span>
      <span className="shrink-0 text-right">
        <span className="block font-display text-[16px] leading-tight text-gold">{item.price}</span>
        <span className="block text-[8px] tracking-[0.2em] text-white/35">ETB</span>
      </span>
    </li>
  );
}

export function GbMenu() {
  const [menu, setMenu] = useState<GbMenuItem[] | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [category, setCategory] = useState<Category>("ALL");
  const [query, setQuery] = useState("");
  const [reloadKey, setReloadKey] = useState(0);

  useEffect(() => {
    let alive = true;
    gbApi.menu().then(
      (res) => {
        if (!alive) return;
        setMenu(res.menu);
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
    setMenu(null);
    setError(null);
    setReloadKey((k) => k + 1);
  }, []);

  const filtered = useMemo(() => {
    if (!menu) return [];
    const q = query.trim().toLowerCase();
    return menu.filter(
      (m) =>
        (category === "ALL" || m.category === category) &&
        (q === "" || m.name.toLowerCase().includes(q) || m.description.toLowerCase().includes(q))
    );
  }, [menu, category, query]);

  return (
    <div className="px-5 pb-9 pt-6">
      <h1 className="font-display text-2xl uppercase text-white">Food &amp; Drinks</h1>
      <p className="mt-1 text-[12px] text-white/45">Kitchen till late · prices in Ethiopian Birr (ETB)</p>

      <div className="relative mt-4">
        <Search className="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-white/30" aria-hidden />
        <input
          type="search"
          value={query}
          onChange={(e) => setQuery(e.target.value)}
          placeholder="Search the menu…"
          aria-label="Search the menu"
          className="w-full rounded-[10px] border border-gold/25 bg-panel py-3 pl-10 pr-3.5 text-[13px] text-white outline-none transition placeholder:text-white/25 focus:border-gold"
        />
      </div>

      <div className="no-scrollbar -mx-5 mt-4 flex gap-2 overflow-x-auto px-5" role="tablist" aria-label="Menu categories">
        {CATEGORIES.map((c) => (
          <button
            key={c}
            type="button"
            role="tab"
            aria-selected={category === c}
            onClick={() => setCategory(c)}
            className={`shrink-0 rounded-full border px-4 py-2 text-[11px] font-bold tracking-wider transition ${
              category === c
                ? "border-gold bg-gold text-[#080808]"
                : "border-white/10 text-white/55 hover:border-gold/40 hover:text-gold-light"
            }`}
          >
            {c}
          </button>
        ))}
      </div>

      <div className="mt-5">
        {error && <GbErrorBox message={error} onRetry={retry} />}
        {!error && menu === null && (
          <div className="space-y-2.5">
            <GbSkeleton className="h-[76px] w-full" />
            <GbSkeleton className="h-[76px] w-full" />
            <GbSkeleton className="h-[76px] w-full" />
            <GbSkeleton className="h-[76px] w-full" />
          </div>
        )}
        {menu !== null && filtered.length === 0 && !error && (
          <div className="rounded-xl border border-white/[0.08] bg-panel p-8 text-center">
            <SearchX className="mx-auto mb-2 h-6 w-6 text-white/30" aria-hidden />
            <p className="text-[12px] text-white/50">Nothing matches your search.</p>
          </div>
        )}
        {menu !== null && filtered.length > 0 && !error && (
          <ul className="space-y-2.5">
            {filtered.map((item) => (
              <MenuRow key={item.id} item={item} />
            ))}
          </ul>
        )}
      </div>
    </div>
  );
}
