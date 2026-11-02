# Gura Bicha Sports Bar & Lounge — Laravel 12 Application

A high-performance, mobile-first web application and Telegram Mini App for **Gura Bicha Sports Bar** ("Game & Groove") located on Bole Road, Addis Ababa, Ethiopia.

Converted from Next.js 16 to **Laravel 12** running on PHP 8.3+.

---

## 🌟 Features & Highlights

- **Venue Timezone & Scheduling**: Fully aligned with East Africa Time (`Africa/Addis_Ababa`, UTC+3).
- **Interactive 4-Step Table Reservation Wizard**:
  - Step 1: Date & Session selector (Lunch `12:30 PM – 3:30 PM` vs Evening `7:00 PM – 11:00 PM`). Quick-pick chips for upcoming Acoustic Thursdays.
  - Step 2: Live interactive 6-table floor plan (`T1`–`T6`) with zone labels, capacity caps, and real-time conflict-free availability.
  - Step 3: Capacity-aware party size counter (1 to 12 guests, clamped to table maximums).
  - Step 4: Contact details with automated phone digit normalization and local detail saving.
  - Step 5: Instant booking confirmation with unique code format `GB-XXXX`, 1-click iCalendar (`.ics`) file generation, and Google Calendar export links.
- **Complete 2026 Football Calendar (563 Matches)**:
  - All real Premier League (374 fixtures) and UEFA Champions League (189 fixtures) matches for calendar year 2026 loaded directly into Eloquent.
  - Month-by-month browser with day-dot counts, live scores for finished games, and 1-click table reservations pre-linked to specific match fixtures.
- **Traditional Ethiopian Kitchen & Lounge Menu**:
  - Categorized browsing across `FOOD`, `DRINKS`, and `SHISHA` with ETB prices (e.g. Kitfo Special, Shekla Tibs, Gored Gored, Meta Draft, Macchiato, Shisha Double Apple).
  - Instant live search and "Popular" highlight tags.
- **Booking Management & Soft-Cancellation**:
  - Phone number lookup (supports partial / normalized prefix matching).
  - 1-click cancellation (`DELETE /api/reservations/{id}`), immediately flipping status to `CANCELLED` and releasing table availability for other patrons.
- **Telegram Mini App Integration**:
  - Automatic detection of `window.Telegram.WebApp`.
  - Server-side cryptographic HMAC-SHA256 verification of `x-telegram-init-data` header against `TELEGRAM_BOT_TOKEN`.
  - Haptic feedback, theme styling, and automatic guest profile pre-fill.
- **Full-Featured Admin Management Console (`/admin`)**:
  - Protected session authentication.
  - Live venue floor visualizer (toggle between Lunch and Evening sessions for today).
  - Complete CRUD for Reservations, Tables, Events, 563 Football Fixtures, and Food & Bar Menu.
  - Quick action toggles for confirming/cancelling bookings and starring featured matches.

---

## 🏗️ Architecture

- **Backend**: Laravel 12.x (PHP 8.3+)
- **Database**: SQLite default (`database/database.sqlite`), MySQL ready
- **ORM & Models**: Eloquent (`VenueTable`, `Event`, `SportMatch`, `MenuItem`, `Reservation`)
- **Frontend**: Blade with Tailwind CSS and Alpine.js (zero node build daemon required at runtime, completely standalone in WAMP/Apache)
- **API**: Clean RESTful JSON endpoints with strict input validation, conflict checks, and error responses.

---

## 🚀 Quick Start Guide

### Prerequisites
- PHP 8.2 or 8.3+ with `pdo_sqlite` or `pdo_mysql`, `mbstring`, `openssl`, `curl`
- Composer 2.x

### 1. Install Dependencies
```bash
composer install
```

### 2. Environment Configuration
Copy `.env.example` to `.env` (if not already done) and generate your application key:
```bash
php artisan key:generate
```

### 3. Database Migration & Seeding
Migrate the database schema and populate the venue tables, acoustic events, 563 football matches, and 18 menu items:
```bash
php artisan migrate:fresh --seed
```

### 4. Run Development Server
```bash
php artisan serve
```
Visit `http://localhost:8000` or access directly through your local WAMP server at `http://localhost/Gurabicha/public/`.

---

## 🔐 Admin Panel Access

Open the Admin Console at:
- **WAMP**: [http://localhost/Gurabicha/public/admin](http://localhost/Gurabicha/public/admin)
- **Artisan**: [http://localhost:8000/admin](http://localhost:8000/admin)

### Default Credentials:
- **Email**: `admin@gurabicha.com`
- **Password**: `password`
*(Includes a 1-click "Auto-fill Password" button on the login screen for rapid testing)*

### Admin Capabilities:
1. **Live Floor Map & Dashboard**: High-level KPIs, real-time table status for both Lunch and Evening sessions.
2. **Reservations Manager**: Filter by date/session/status/table, search by customer phone/name/code, add manual phone/walk-in reservations, edit party details, and 1-click confirm/cancel.
3. **Venue Tables**: Add, edit, or delete tables (`T1`–`T6`), adjust seat capacities and viewing zones.
4. **Events Manager**: Add or edit venue events, update timings, descriptions, and feature events.
5. **Football Fixtures**: Search and manage all 563 matches, edit live/final scores, update schedules, and toggle the homepage match spotlight.
6. **Menu Manager**: Add, edit, or remove Food, Drinks, and Shisha items, adjust ETB prices, and toggle popular badges.

---

## 🛠️ Helpful Artisan Commands

- **Check Real-Time Venue Status & Table Bookings**:
  ```bash
  php artisan venue:status
  # Check for a specific calendar date:
  php artisan venue:status --date=2026-11-05
  ```
  Renders a table breakdown of tables `T1`–`T6`, zone, capacity, and current booking codes/patrons for both `LUNCH` and `EVENING` sessions.

- **Re-import or Refresh Football Fixtures**:
  ```bash
  php artisan fixtures:refresh --wipe
  ```
  Parses `data/matches-2026.json` and loads all 563 real Premier League & Champions League matches with an interactive progress bar.

---

## 📡 API Reference

All API routes are served under `/api` with JSON responses:

### 1. Events
- **`GET /api/events`**: Returns upcoming events (`date >= start of today UTC`), sorted ascending.

### 2. Matches
- **`GET /api/matches`**: Returns upcoming matches (kickoff >= now - 4h, max 40).
- **`GET /api/matches?year=2026`**: Returns every match scheduled in calendar year 2026.
- **`GET /api/matches?date=YYYY-MM-DD`**: Returns matches kicking off on that specific calendar day.

### 3. Menu
- **`GET /api/menu`**: Returns all menu items sorted by category (`FOOD` -> `DRINKS` -> `SHISHA`) then by name.

### 4. Tables & Availability
- **`GET /api/tables?date=YYYY-MM-DD&slot=LUNCH|EVENING`**:
  Returns all 6 tables with dynamic boolean `available` status and `remaining` count of unbooked tables.

### 5. Reservations
- **`POST /api/reservations`**:
  Creates a new booking.
  - Body:
    ```json
    {
      "date": "2026-11-05",
      "slot": "EVENING",
      "tableCode": "T1",
      "guests": 3,
      "name": "Abebe Kebede",
      "phone": "+251 911 223 344",
      "email": "abebe@example.com",
      "request": "Table with direct screen sightline",
      "matchId": "optional-uuid"
    }
    ```
  - Response: `201 Created` with `{ "reservation": { ... } }` and code `GB-XXXX`.
  - Conflict: `409 Conflict` if table was already booked for that date + slot.
- **`GET /api/reservations?phone=0911`**: Lookup reservations by phone prefix.
- **`GET /api/reservations?mine=1`**: Lookup reservations for the Telegram authenticated user via `x-telegram-init-data`.
- **`DELETE /api/reservations/{id}`**: Soft-cancels the reservation (sets status to `CANCELLED`), releasing the table immediately.

### 6. Calendar Downloads
- **`GET /ics/{code}`**: Downloads standard `.ics` iCalendar event file for Apple Calendar, Outlook, and Google Calendar.

---

## 🧪 Running Automated Tests

Run the complete test suite:
```bash
php artisan test
```

Tests cover:
- Web homepage rendering
- Event retrieval & sorting
- 2026 Match calendar filtering
- Menu item ordering
- Full reservation lifecycle: availability check -> validation -> 409 collision prevention -> phone search -> ICS export -> soft-delete cancel -> table availability release.
