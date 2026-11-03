@extends('admin.layout')

@section('title', 'Admin Dashboard')
@section('header_title', 'Venue Dashboard & Operations Floor')

@section('content')
<div class="space-y-6" x-data="{ currentSlot: 'EVENING' }">

    <!-- SMART OPERATIONS DESK (FANCY & SIMPLE CONTROL BAR) -->
    <div class="p-5 rounded-3xl bg-admin-card border border-gold/30 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4 relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-48 h-48 bg-gold/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex items-center space-x-3.5 z-10">
            <div class="w-12 h-12 rounded-2xl bg-gold/15 border border-gold/40 text-gold flex items-center justify-center font-bold text-2xl shadow-inner shrink-0">
                ⚡
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h3 class="font-display font-extrabold text-base sm:text-lg text-slate-900 dark:text-white uppercase tracking-wider">
                        Gura Bicha Operations Desk
                    </h3>
                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-500/40 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Live Floor</span>
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                    {{ \Carbon\Carbon::parse($today)->format('l, F j, Y') }} · Mexico, Gura Bicha Addis Ababa
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 z-10 text-xs">
            <a href="{{ route('admin.reservations.create') }}" class="px-4 py-2.5 rounded-xl bg-gold hover:bg-gold-light text-black font-bold transition shadow-lg shadow-gold/25 flex items-center space-x-1.5 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ New Booking</span>
            </a>
            <a href="{{ route('admin.customers.index') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-admin-panel dark:hover:bg-zinc-800 border border-slate-200 dark:border-admin-border text-slate-800 dark:text-zinc-200 hover:text-emerald-600 dark:hover:text-emerald-400 transition flex items-center space-x-1.5 font-semibold">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                <span>Broadcast Alert</span>
            </a>
            <form method="POST" action="{{ route('admin.matches.sync') }}" class="inline">
                @csrf
                <button type="submit" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-admin-panel dark:hover:bg-zinc-800 border border-slate-200 dark:border-admin-border text-slate-800 dark:text-zinc-200 hover:text-purple-600 dark:hover:text-purple-400 transition flex items-center space-x-1.5 font-semibold">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Sync Scores</span>
                </button>
            </form>
            <a href="{{ route('admin.menu.index') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-admin-panel dark:hover:bg-zinc-800 border border-slate-200 dark:border-admin-border text-slate-800 dark:text-zinc-200 hover:text-amber-600 dark:hover:text-amber-400 transition flex items-center space-x-1.5 font-semibold">
                <span>📜 Menu Board</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-admin-panel dark:hover:bg-zinc-800 border border-slate-200 dark:border-admin-border text-slate-800 dark:text-zinc-200 hover:text-gold transition flex items-center space-x-1.5 font-semibold">
                <span>🖨️ Table QRs</span>
            </a>
        </div>
    </div>

    <!-- TOP KPI STATS (4 HIGH-IMPACT FANCY CARDS) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Today's Bookings -->
        <div class="p-5 rounded-2xl bg-admin-card border border-admin-border hover:border-emerald-500/50 transition shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500 dark:text-zinc-400 font-bold uppercase tracking-wider">Today's Bookings</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-sm">📅</span>
            </div>
            <div class="mt-3 flex items-baseline space-x-2">
                <span class="font-display text-3xl sm:text-4xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['today_reservations'] }}</span>
                <span class="text-xs text-slate-500 dark:text-zinc-400 font-medium">tables active</span>
            </div>
            <div class="mt-2 text-[11px] text-emerald-700 dark:text-emerald-400 font-semibold">
                Saturday Acoustic & Dining
            </div>
        </div>

        <!-- Confirmed / Total -->
        <div class="p-5 rounded-2xl bg-admin-card border border-admin-border hover:border-gold/50 transition shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500 dark:text-zinc-400 font-bold uppercase tracking-wider">Total Bookings</span>
                <span class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 dark:text-gold flex items-center justify-center font-bold text-sm">🧾</span>
            </div>
            <div class="mt-3 flex items-baseline space-x-2">
                <span class="font-display text-3xl sm:text-4xl font-black text-amber-600 dark:text-gold">{{ $stats['confirmed_reservations'] }}</span>
                <span class="text-xs text-slate-500 dark:text-zinc-400 font-medium">/ {{ $stats['total_reservations'] }} total</span>
            </div>
            <div class="mt-2 text-[11px] text-amber-700 dark:text-gold font-semibold">
                Confirmed Reservations
            </div>
        </div>

        <!-- Registered Fans -->
        <div class="p-5 rounded-2xl bg-admin-card border border-admin-border hover:border-blue-500/50 transition shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500 dark:text-zinc-400 font-bold uppercase tracking-wider">Fan Club Patrons</span>
                <span class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-sm">⚽</span>
            </div>
            <div class="mt-3 flex items-baseline space-x-2">
                <span class="font-display text-3xl sm:text-4xl font-black text-blue-600 dark:text-blue-400">{{ $stats['total_customers'] ?? 0 }}</span>
                <span class="text-xs text-slate-500 dark:text-zinc-400 font-medium">registered</span>
            </div>
            <div class="mt-2 text-[11px] text-blue-700 dark:text-blue-400 font-semibold">
                Direct Telegram Alert Subscriptions
            </div>
        </div>

        <!-- Venue Tables & Fixtures -->
        <div class="p-5 rounded-2xl bg-admin-card border border-admin-border hover:border-purple-500/50 transition shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-500 dark:text-zinc-400 font-bold uppercase tracking-wider">Matches Tracked</span>
                <span class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-sm">🏆</span>
            </div>
            <div class="mt-3 flex items-baseline space-x-2">
                <span class="font-display text-3xl sm:text-4xl font-black text-purple-600 dark:text-purple-400">{{ $stats['total_matches'] }}</span>
                <span class="text-xs text-slate-500 dark:text-zinc-400 font-medium">in database</span>
            </div>
            <div class="mt-2 text-[11px] text-purple-700 dark:text-purple-400 font-semibold">
                Premier League & UCL Screenings
            </div>
        </div>
    </div>

    <!-- TODAY'S SMART FLOOR OCCUPANCY -->
    <div class="p-5 sm:p-6 rounded-3xl bg-admin-card border border-admin-border space-y-4 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-admin-border pb-3">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gold animate-pulse"></span>
                    <h3 class="font-display text-base sm:text-lg font-bold uppercase tracking-wider text-slate-900 dark:text-white">
                        Today's Floor Map & Table Availability
                    </h3>
                </div>
                <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                    Click any available table to book directly for walk-in or telephone guests
                </p>
            </div>

            <!-- Session Switcher -->
            <div class="flex items-center space-x-1.5 bg-slate-100 dark:bg-admin-panel p-1.5 rounded-2xl border border-slate-200 dark:border-admin-border self-start sm:self-auto">
                <button @click="currentSlot = 'LUNCH'" :class="currentSlot === 'LUNCH' ? 'bg-gold text-black font-bold shadow-md shadow-gold/20' : 'text-slate-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs transition">
                    🍽️ Lunch (12:30–3:30 PM)
                </button>
                <button @click="currentSlot = 'EVENING'" :class="currentSlot === 'EVENING' ? 'bg-gold text-black font-bold shadow-md shadow-gold/20' : 'text-slate-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs transition">
                    🎸 Evening Acoustic (7:00–11:30 PM)
                </button>
            </div>
        </div>

        <!-- LUNCH TABLES -->
        <div x-show="currentSlot === 'LUNCH'" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach($tables as $t)
                @php
                    $isBooked = isset($todayLunchBookings[$t->code]);
                    $booking = $isBooked ? $todayLunchBookings[$t->code] : null;
                @endphp
                <div class="p-4 rounded-2xl border transition {{ $isBooked ? 'border-red-400/50 bg-red-500/10' : 'border-emerald-500/40 bg-emerald-500/10' }} flex flex-col justify-between space-y-2">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-display text-xl font-black text-slate-900 dark:text-white">{{ $t->code }}</span>
                            <span class="text-[9px] px-2 py-0.5 rounded-full font-black uppercase {{ $isBooked ? 'bg-red-500 text-white' : 'bg-emerald-600 text-white' }}">
                                {{ $isBooked ? 'BOOKED' : 'FREE' }}
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-700 dark:text-zinc-200 font-bold block truncate">{{ $t->zone }}</span>
                        <span class="text-[10px] text-slate-500 dark:text-zinc-400 block">{{ $t->seats }}</span>
                    </div>

                    @if($booking)
                        <div class="pt-2 border-t border-red-300 dark:border-red-800 text-[11px]">
                            <div class="font-bold text-amber-700 dark:text-gold truncate">{{ $booking->name }}</div>
                            <div class="text-slate-500 dark:text-zinc-400 text-[10px]">{{ $booking->phone }} ({{ $booking->guests }} guests)</div>
                        </div>
                    @else
                        <div class="pt-2 border-t border-emerald-300 dark:border-emerald-800 flex items-center justify-between text-[11px]">
                            <span class="text-emerald-700 dark:text-emerald-400 font-bold">Open</span>
                            <a href="{{ route('admin.reservations.create') }}?table={{ $t->code }}&slot=LUNCH&date={{ $today }}" class="px-2 py-0.5 rounded-md bg-gold text-black font-bold text-[10px] hover:bg-gold-light transition shadow-sm">
                                + Book
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- EVENING TABLES -->
        <div x-show="currentSlot === 'EVENING'" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach($tables as $t)
                @php
                    $isBooked = isset($todayEveningBookings[$t->code]);
                    $booking = $isBooked ? $todayEveningBookings[$t->code] : null;
                @endphp
                <div class="p-4 rounded-2xl border transition {{ $isBooked ? 'border-red-400/50 bg-red-500/10' : 'border-emerald-500/40 bg-emerald-500/10' }} flex flex-col justify-between space-y-2">
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-display text-xl font-black text-slate-900 dark:text-white">{{ $t->code }}</span>
                            <span class="text-[9px] px-2 py-0.5 rounded-full font-black uppercase {{ $isBooked ? 'bg-red-500 text-white' : 'bg-emerald-600 text-white' }}">
                                {{ $isBooked ? 'BOOKED' : 'FREE' }}
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-700 dark:text-zinc-200 font-bold block truncate">{{ $t->zone }}</span>
                        <span class="text-[10px] text-slate-500 dark:text-zinc-400 block">{{ $t->seats }}</span>
                    </div>

                    @if($booking)
                        <div class="pt-2 border-t border-red-300 dark:border-red-800 text-[11px]">
                            <div class="font-bold text-amber-700 dark:text-gold truncate">{{ $booking->name }}</div>
                            <div class="text-slate-500 dark:text-zinc-400 text-[10px]">{{ $booking->phone }} ({{ $booking->guests }} guests)</div>
                        </div>
                    @else
                        <div class="pt-2 border-t border-emerald-300 dark:border-emerald-800 flex items-center justify-between text-[11px]">
                            <span class="text-emerald-700 dark:text-emerald-400 font-bold">Open</span>
                            <a href="{{ route('admin.reservations.create') }}?table={{ $t->code }}&slot=EVENING&date={{ $today }}" class="px-2 py-0.5 rounded-md bg-gold text-black font-bold text-[10px] hover:bg-gold-light transition shadow-sm">
                                + Book
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- RECENT BOOKINGS & FAN RADAR GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Recent Reservations (2 cols) -->
        <div class="lg:col-span-2 p-5 sm:p-6 rounded-3xl bg-admin-card border border-admin-border space-y-4 shadow-xl">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <div>
                    <h3 class="font-display text-base font-bold uppercase tracking-wider text-slate-900 dark:text-white">Recent Table Bookings</h3>
                    <p class="text-xs text-slate-500 dark:text-zinc-400">Incoming reservations for Saturday Acoustic & matchdays</p>
                </div>
                <a href="{{ route('admin.reservations.index') }}" class="text-xs text-amber-700 dark:text-gold hover:underline font-bold">
                    View All ({{ $stats['total_reservations'] }}) →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-admin-border text-slate-600 dark:text-zinc-400 uppercase text-[10px] tracking-wider">
                            <th class="pb-2.5">Code</th>
                            <th class="pb-2.5">Guest & Contact</th>
                            <th class="pb-2.5">Date & Slot</th>
                            <th class="pb-2.5">Table</th>
                            <th class="pb-2.5">Status</th>
                            <th class="pb-2.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-admin-border/50">
                        @forelse($recentReservations as $r)
                            <tr class="hover:bg-slate-50 dark:hover:bg-admin-panel/40 transition">
                                <td class="py-3 font-mono font-bold text-amber-700 dark:text-gold">
                                    <a href="{{ route('admin.reservations.edit', $r) }}" class="hover:underline">{{ $r->code }}</a>
                                </td>
                                <td class="py-3">
                                    <span class="font-semibold text-slate-900 dark:text-white block">{{ $r->name }}</span>
                                    <span class="text-slate-500 dark:text-zinc-400 text-[11px]">{{ $r->phone }} ({{ $r->guests }}p)</span>
                                </td>
                                <td class="py-3">
                                    <span class="text-slate-800 dark:text-zinc-200 font-medium block">{{ \Carbon\Carbon::parse($r->date)->format('M d, Y') }}</span>
                                    <span class="text-amber-700 dark:text-gold text-[10px] uppercase font-bold">{{ $r->slot === 'EVENING' ? 'Evening Groove' : 'Lunch' }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 dark:bg-admin-panel text-slate-900 dark:text-white font-bold border border-slate-200 dark:border-admin-border">{{ $r->tableCode }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $r->status === 'CONFIRMED' ? 'bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-500/40' : 'bg-red-500/20 text-red-700 dark:text-red-400 border border-red-500/40' }}">
                                        {{ $r->status }}
                                    </span>
                                </td>
                                <td class="py-3 text-right space-x-1.5">
                                    <a href="{{ route('admin.reservations.edit', $r) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-admin-panel text-slate-700 dark:text-zinc-300 hover:text-gold font-bold text-[11px] border border-slate-200 dark:border-admin-border transition">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 dark:text-zinc-500">No reservations created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIGHT SIDEBAR: TOP CLUBS & UPCOMING SCREENINGS -->
        <div class="space-y-6">
            <!-- FAN CLUB STATS -->
            <div class="p-5 rounded-3xl bg-admin-card border border-admin-border space-y-3 shadow-xl">
                <div class="flex items-center justify-between border-b border-admin-border pb-2.5">
                    <div>
                        <h3 class="font-display text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white">Top Supported Clubs</h3>
                        <p class="text-[11px] text-slate-500 dark:text-zinc-400">Telegram Fan Club Community</p>
                    </div>
                    <a href="{{ route('admin.customers.index') }}" class="text-xs text-amber-700 dark:text-gold hover:underline font-bold">All Fans →</a>
                </div>

                <div class="space-y-3 pt-1">
                    @forelse($topClubs ?? [] as $club)
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-bold text-slate-800 dark:text-white">⚽ {{ $club->favorite_club }}</span>
                                <span class="text-amber-700 dark:text-gold font-black font-mono">{{ $club->count }} supporters</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-admin-panel overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-amber-500 to-amber-600 dark:from-gold dark:to-amber-500 rounded-full" style="width: {{ min(100, $club->count * 25) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 dark:text-zinc-500">No registered fan clubs yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- UPCOMING BIG MATCHES -->
            <div class="p-5 rounded-3xl bg-admin-card border border-admin-border space-y-3 shadow-xl">
                <div class="flex items-center justify-between border-b border-admin-border pb-2.5">
                    <div>
                        <h3 class="font-display text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white">Upcoming Screenings</h3>
                        <p class="text-[11px] text-slate-500 dark:text-zinc-400">Big Screen Match Feeds</p>
                    </div>
                    <a href="{{ route('admin.matches.index') }}" class="text-xs text-amber-700 dark:text-gold hover:underline font-bold">All Matches →</a>
                </div>

                <div class="space-y-2.5">
                    @forelse($upcomingMatches as $m)
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-admin-panel border {{ $m->featured ? 'border-amber-400 dark:border-gold shadow-sm' : 'border-slate-200 dark:border-admin-border' }} space-y-1">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-slate-500 dark:text-zinc-400 font-mono">{{ \Carbon\Carbon::parse($m->kickoff)->setTimezone('Africa/Addis_Ababa')->format('D, M d · H:i') }} EAT</span>
                                @if($m->featured)
                                    <span class="px-2 py-0.5 rounded-full bg-amber-500 text-black text-[9px] font-black uppercase">FEATURED</span>
                                @endif
                            </div>
                            <div class="font-bold text-xs text-slate-900 dark:text-white">
                                {{ $m->homeTeam }} <span class="text-slate-400 dark:text-zinc-500 font-normal">vs</span> {{ $m->awayTeam }}
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-zinc-400 pt-0.5">
                                <span class="font-semibold text-slate-600 dark:text-zinc-300">{{ $m->competition }}</span>
                                <form method="POST" action="{{ route('admin.matches.toggle-featured', $m) }}">
                                    @csrf
                                    <button type="submit" class="text-amber-700 dark:text-gold hover:underline font-bold">
                                        {{ $m->featured ? 'Unfeature' : '★ Feature' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 dark:text-zinc-500">No upcoming matches currently scheduled.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
