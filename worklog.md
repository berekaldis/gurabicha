# Gura Bicha Sports Bar — Worklog

Project: Fullstack Next.js 16 app for "Gura Bicha Sports Bar" (black & gold themed mobile-first venue app).

Source design: user-provided static HTML mockup (hero "Game & Groove", match card, events carousel, 6-table reservation flow, bottom nav).

API contract (agreed upfront):
- GET  /api/events                -> { events: Event[] }        (upcoming, asc)
- GET  /api/matches               -> { matches: Match[] }       (kickoff asc)
- GET  /api/menu                  -> { menu: MenuItem[] }
- GET  /api/tables?date=YYYY-MM-DD&slot=LUNCH|EVENING -> { tables: [{code,zone,capacity,seats,available}] }
- POST /api/reservations          -> 201 { reservation }        (validates Thursday, capacity, double-booking; code GB-XXXX)
- GET  /api/reservations?phone=X  -> { reservations: [] }
- DELETE /api/reservations/[id]   -> { reservation }            (soft-cancel, status=CANCELLED)

Image assets plan (public/images/): hero-gura.jpg, event-acoustic.jpg, event-sports.jpg

---
Task ID: 1
Agent: main (Z.ai Code)
Task: Explore project, define plan + API contract

Work Log:
- Read package.json: Next 16, Prisma 6 + SQLite, sonner, framer-motion, date-fns, zod available
- Confirmed shadcn/ui full set in src/components/ui, db client at src/lib/db.ts
- Confirmed dev server running on port 3000 (HTTP 200)
- No prior worklog existed; created this file
- prisma/schema.prisma still contains scaffold User/Post models (to be replaced)

Stage Summary:
- Plan: images -> backend (schema+seed+API) -> frontend (theme, shell, home, reserve flow, menu, my reservations) -> lint -> browser E2E -> worklog
- Design tokens: gold #f5bd19 / #ffd34d, black #050505 panels, Oswald display font + Inter body

---
Task ID: 3
Agent: full-stack-developer
Task: Backend — Prisma schema, db push, seed, API routes

Work Log:
- Replaced scaffold User/Post models in prisma/schema.prisma with Event, Match, VenueTable, MenuItem, Reservation (kept generator/datasource blocks); `bun run db:push` succeeded (db/custom.db)
- Wrote idempotent prisma/seed.ts (deleteMany all models -> recreate; dates computed relative to now). Ran twice: identical counts both runs (5 events, 3 matches, 6 tables, 18 menu items, 0 reservations)
- Seed computed next 4 Thursdays 2026-09-03 / 09-10 / 09-17 / 09-24 (first one featured:true) + Wednesday event 2026-09-02; matches at today 16:00 UTC (live), +1d 17:45 UTC, +2d 19:00 UTC
- Implemented 6 route handlers, all with `export const dynamic = "force-dynamic"` and try/catch -> 500 {error}: events, matches, menu, tables, reservations, reservations/[id]
- API contract verified via curl against port 3000 (see summary below); `bun run lint` clean; DB re-seeded after tests so reservations table is empty for the frontend agent

Stage Summary:
- Files created: prisma/seed.ts; src/app/api/events/route.ts; src/app/api/matches/route.ts; src/app/api/menu/route.ts; src/app/api/tables/route.ts; src/app/api/reservations/route.ts; src/app/api/reservations/[id]/route.ts
- Re-run seed anytime with: `cd /home/z/my-project && bun prisma/seed.ts` (wipes + recreates all data, always-fresh relative dates)
- curl results: GET events/matches/menu/tables 200; POST valid reservation 201 {reservation, code GB-XXXX}; T4 flipped to available:false + remaining 5; duplicate booking 409 exact message; GET ?phone=0911223344 -> 1 record; DELETE -> status CANCELLED, T4 back to available:true; DELETE bogus id 404; guests 20 / non-Thursday / past date / unknown table / over-capacity / missing params all 400 with clear messages
- Deviations: (1) phone stored digits-only normalized at creation; GET lookup normalizes query the same way and matches with startsWith per spec (400 also when phone param present but normalizes to empty, to avoid returning all rows); (2) menu category ordering done in JS (SQLite can't order by custom enum list); (3) tables route additionally validates calendar-date validity via UTC round-trip (catches e.g. 2026-02-30); (4) 409 message returns table code dynamically (`Table ${tableCode} ...`)

---
Task ID: 4-11
Agent: main (Z.ai Code)
Task: Frontend build, integration, lint fixes, browser E2E verification

Work Log:
- Verified backend contract via curl (events/matches/tables shapes match spec)
- Generated 3 brand images via image-generation skill -> public/images/{hero-gura,event-acoustic,event-sports}.jpg
- Theme: gold/ink/panel tokens in globals.css @theme, dark shadcn vars, Oswald+Inter via Google Fonts <link> in layout, font-script + no-scrollbar utilities
- Types + API client: src/lib/gb/{types,api}.ts (ReserveDraft, SLOT_META, formatKickoff with Africa/Addis_Ababa TZ, buildIcs)
- Components (src/components/gb/): gb-app (shell, screen state, draft state, sonner toasts), gb-header (sticky + Sheet nav + venue info), gb-bottom-nav (sticky bottom, raised gold RESERVE button), gb-home (hero, features strip, live match card with TODAY/TOMORROW TZ labels, events carousel + expand-all), gb-reserve (4-step wizard: Thursday-only calendar with month nav, LUNCH/EVENING sessions, live table map with booked states + refresh + conflict banner, capacity-aware guest counter with quick chips, details form with inline validation + localStorage save, confirmation with ICS download / share / code), gb-menu (category tabs + search + ETB prices), gb-reservations (phone lookup, auto-lookup from saved details, cancel via AlertDialog, status badges)
- page.tsx renders GbApp only (single visible route)
- Fixed React hooks lint errors: removed mounted gate, async-callback data loading patterns, derived table loading state, removed stale eslint-disable directives
- bun run lint: 0 errors (1 acceptable no-page-custom-font warning, false positive in App Router)
- Browser E2E (agent-browser, 430x932 + 1280x800): home renders with live data; full booking flow GB-9674 created (validation errors verified first); my-reservations auto-lookup worked; cancel flow released T4 (API verified available:true); seeded T4 LUNCH booking shows BOOKED state in table map + "5 of 6 tables free"; second booking GB-9681 via UI; menu filters + search verified; sheet menu verified; mobile + desktop framed layout verified; zero console/page errors

Stage Summary:
- App fully fullstack: SQLite via Prisma (Event, Match, VenueTable, MenuItem, Reservation), 7 API routes, 4 screens + 5-step reservation wizard
- Demo data in DB: GB-2804 (T4 LUNCH confirmed, via API), GB-9681 (T5 LUNCH confirmed), GB-9674 (cancelled)
- To reseed demo content: bun prisma/seed.ts (idempotent, relative dates)

---
Task ID: 12
Agent: main (Z.ai Code)
Task: Telegram Mini App polish + load ALL 2026 Premier League & Champions League fixtures into the calendar

Work Log:
- Discovered data/matches-2026.json (563 real fixtures fetched from ESPN scoreboard API: 374 PL + 189 UCL, Jan-Dec 2026 incl. FT scores for 2025/26 half and SCHEDULED 2026/27 half) existed but prisma/seed.ts only seeded 3 dummy matches
- Rewrote seed section 4: loadFixtures() reads data/matches-2026.json via import.meta.url + node:fs, createMany with full fields (stage/season/venue/status/scores/featured); re-run seed -> 563 matches in DB
- Fixed stale dev-server Prisma client (year route served rows missing new schema fields): restarted `bun run dev` (was started before schema push)
- Verified APIs: /api/matches?year=2026 -> 563 (85 Jan, 58 Feb, 48 Mar, 40 Apr, 44 May, 20 Aug, 48 Sep, 73 Oct, 69 Nov, 78 Dec); /api/matches upcoming -> real fixtures (Aston Villa v Arsenal MW2 tonight); tables T5 BOOKED after test booking; events 5
- Telegram BackButton interceptor system in src/lib/gb/telegram.tsx: BackInterceptorsContext + useTelegramBackInterceptor(handler|null), LIFO dispatch; wired: gb-app (default = go home), gb-reserve (steps back through wizard Date->home / Table->Date / Guests->Table / Details->Guests), gb-header (closes nav sheet first)
- Telegram CloudStorage for saved guest details: cloudStorageGet/Set/Remove helpers with versionAtLeast("6.4") guard + 1s timeout race (mock client v6.0 logged "CloudStorage is not supported" and never called back -> promise would hang); restore reads CloudStorage then localStorage; DetailsStep writes both, removes from both on opt-out; GB_DETAILS_KEY const
- Lint fixes (react-hooks v6 rules): ref-sync moved into per-render effects (3 hooks), loadYearMatches + TelegramProvider setCtx deferred a microtask/tick; final lint 0 errors (1 known font false-positive)
- Browser E2E (agent-browser, 430x932 + 1280x800): home shows real MW2 fixture; fixtures calendar Aug 2026 with PL dots + "20 THIS MONTH", Jan 2026 UCL MD7/8 silver dots + FT scores (Palace 1-1 Fulham etc.), Sep 8 shows UCL MD1 (AEK Athens v LASK, Club Brugge v Aston Villa, Dortmund v Villarreal) with BOOK buttons; booked Dortmund v Villarreal -> GB-3283 (T5, Sep 8 EVENING, 4 guests) via full wizard; My Reservations phone lookup +1 911 234 567 finds GB-3283 CONFIRMED; menu renders ETB categories; T5 shows BOOKED + "5 of 6 tables free" via API; zero console/page errors; desktop framed layout verified

Stage Summary:
- Calendar now shows EVERY Premier League & Champions League match of 2026 (both halves of both seasons) with per-day PL/UCL dots, EAT kickoffs, venues, live/FT states
- App is a complete Telegram Mini App: SDK script, ready/expand/theme colors, haptics, MainButton confirm, context-aware BackButton (wizard steps + sheet close), initData sent on every request + server-side HMAC verification, Telegram user prefill + identity-bound bookings (mine=1), Telegram share, CloudStorage profile persistence
- Refresh fixtures anytime: bun data/fetch-fixtures.ts && bun prisma/seed.ts (wipes DB incl. reservations)
- Demo data in DB: reservation GB-3283 (Dortmund v Villarreal, Sep 8 2026, T5)
