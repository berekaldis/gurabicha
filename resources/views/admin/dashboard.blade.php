@extends('admin.layout')

@section('title', 'Admin Dashboard')
@section('header_title', 'Venue Dashboard & Operations Floor')

@section('content')
<div class="space-y-6" x-data="{ currentSlot: 'EVENING' }">

    <!-- QUICK OPERATIONS DESK -->
    <div class="p-4 rounded-2xl bg-admin-card border border-admin-border shadow-md flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gold/15 border border-gold/40 text-gold flex items-center justify-center font-bold text-lg shrink-0">
                ⚡
            </div>
            <div>
                <h4 class="font-display font-bold text-sm text-slate-900 dark:text-white uppercase tracking-wider">Quick Operations Desk</h4>
                <p class="text-[11px] text-slate-500 dark:text-zinc-400">Direct shortcuts to allocate tables, broadcast fan alerts, and manage services</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 text-xs">
            <a href="{{ route('admin.reservations.create') }}" class="px-3.5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-bold transition shadow-md shadow-gold/20 flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ New Booking</span>
            </a>
            <a href="{{ route('admin.customers.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-admin-panel dark:hover:bg-zinc-800 border border-slate-200 dark:border-admin-border text-slate-700 dark:text-zinc-200 hover:text-emerald-700 dark:hover:text-white transition flex items-center space-x-1.5 font-medium">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                <span>Broadcast Alert</span>
            </a>
            <form method="POST" action="{{ route('admin.matches.sync') }}" class="inline">
                @csrf
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-admin-panel dark:hover:bg-zinc-800 border border-slate-200 dark:border-admin-border text-slate-700 dark:text-zinc-200 hover:text-purple-700 dark:hover:text-white transition flex items-center space-x-1.5 font-medium">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Sync Live Scores</span>
                </button>
            </form>
            <a href="{{ route('admin.menu.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-admin-panel dark:hover:bg-zinc-800 border border-slate-200 dark:border-admin-border text-slate-700 dark:text-zinc-200 hover:text-amber-700 dark:hover:text-white transition flex items-center space-x-1.5 font-medium">
                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Add Dish / Drink</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-admin-panel dark:hover:bg-zinc-800 border border-slate-200 dark:border-admin-border text-slate-700 dark:text-zinc-200 hover:text-cyan-700 dark:hover:text-white transition flex items-center space-x-1.5 font-medium">
                <svg class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                <span>Table QRs & Bot</span>
            </a>
        </div>
    </div>

    <!-- TOP KPI STATS (5-COLUMN EXECUTIVE METRICS) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
        <!-- Today's Bookings -->
        <div class="p-4 rounded-2xl bg-admin-card border border-admin-border relative overflow-hidden group hover:border-emerald-500/50 transition shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-[11px] text-slate-500 dark:text-zinc-400 font-semibold uppercase tracking-wider">Today's Bookings</span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>
            <div class="mt-2.5 flex items-baseline space-x-1.5">
                <span class="font-display text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['today_reservations'] }}</span>
                <span class="text-xs text-slate-400 dark:text-zinc-500">patron tables</span>
            </div>
            <div class="mt-2 text-[10px] text-emerald-700 dark:text-emerald-400 font-medium">
                Saturday Acoustic Live Floor
            </div>
        </div>

        <!-- Confirmed / Total -->
        <div class="p-4 rounded-2xl bg-admin-card border border-admin-border group hover:border-gold/50 transition shadow-sm">
            <span class="text-[11px] text-slate-500 dark:text-zinc-400 font-semibold uppercase tracking-wider">Confirmed Bookings</span>
            <div class="mt-2.5 flex items-baseline space-x-1.5">
                <span class="font-display text-3xl font-bold text-amber-600 dark:text-gold">{{ $stats['confirmed_reservations'] }}</span>
                <span class="text-xs text-slate-400 dark:text-zinc-500">/ {{ $stats['total_reservations'] }} total</span>
            </div>
            <div class="mt-2 text-[10px] text-amber-700 dark:text-gold font-medium">
                Active Patron Reservations
            </div>
        </div>

        <!-- Registered Fans -->
        <div class="p-4 rounded-2xl bg-admin-card border border-admin-border group hover:border-blue-500/50 transition shadow-sm">
            <span class="text-[11px] text-slate-500 dark:text-zinc-400 font-semibold uppercase tracking-wider">Fan Club Patrons</span>
            <div class="mt-2.5 flex items-baseline space-x-1.5">
                <span class="font-display text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total_customers'] ?? 0 }}</span>
                <span class="text-xs text-slate-400 dark:text-zinc-500">registered fans</span>
            </div>
            <div class="mt-2 text-[10px] text-blue-700 dark:text-blue-400 font-medium">
                Alerts & Score Notifications
            </div>
        </div>

        <!-- Venue Tables -->
        <div class="p-4 rounded-2xl bg-admin-card border border-admin-border group hover:border-amber-500/50 transition shadow-sm">
            <span class="text-[11px] text-slate-500 dark:text-zinc-400 font-semibold uppercase tracking-wider">Venue Tables</span>
            <div class="mt-2.5 flex items-baseline space-x-1.5">
                <span class="font-display text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['total_tables'] }}</span>
                <span class="text-xs text-slate-400 dark:text-zinc-500">zones (T1–T6)</span>
            </div>
            <div class="mt-2 text-[10px] text-slate-500 dark:text-zinc-400 font-medium">
                Acoustic Sightline Seating
            </div>
        </div>

        <!-- Football Fixtures -->
        <div class="p-4 rounded-2xl bg-admin-card border border-admin-border group hover:border-purple-500/50 transition col-span-2 sm:col-span-1 shadow-sm">
            <span class="text-[11px] text-slate-500 dark:text-zinc-400 font-semibold uppercase tracking-wider">Tracked Fixtures</span>
            <div class="mt-2.5 flex items-baseline space-x-1.5">
                <span class="font-display text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['total_matches'] }}</span>
                <span class="text-xs text-slate-400 dark:text-zinc-500">matches</span>
            </div>
            <div class="mt-2 text-[10px] text-purple-700 dark:text-purple-400 font-medium">
                Premier League & UCL Feeds
            </div>
        </div>
    </div>

    <!-- TODAY'S INTERACTIVE FLOOR STATUS -->
    <div class="p-5 rounded-2xl bg-admin-card border border-admin-border space-y-4 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gold animate-pulse"></span>
                    <h3 class="font-display text-lg font-bold uppercase tracking-wider text-slate-900 dark:text-white">
                        Today's Floor Map & Table Occupancy
                    </h3>
                </div>
                <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                    {{ \Carbon\Carbon::parse($today)->format('l, F j, Y') }} · Real-time table allocations for Saturday Acoustic & dining
                </p>
            </div>

            <!-- Session Switcher -->
            <div class="flex items-center space-x-1.5 bg-slate-100 dark:bg-admin-panel p-1 rounded-xl border border-slate-200 dark:border-admin-border self-start sm:self-auto">
                <button @click="currentSlot = 'LUNCH'" :class="currentSlot === 'LUNCH' ? 'bg-gold text-black font-bold shadow-sm' : 'text-slate-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'" class="px-3 py-1.5 rounded-lg text-xs transition">
                    🍽️ Lunch (12:30–3:30 PM)
                </button>
                <button @click="currentSlot = 'EVENING'" :class="currentSlot === 'EVENING' ? 'bg-gold text-black font-bold shadow-sm' : 'text-slate-600 dark:text-zinc-400 hover:text-black dark:hover:text-white'" class="px-3 py-1.5 rounded-lg text-xs transition">
                    🎸 Evening Acoustic (7:00–11:30 PM)
                </button>
            </div>
        </div>

        <!-- INTEGRATED STAGE & SIGHTLINE OVERVIEW (Replacing old static banner) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-admin-panel border border-slate-200 dark:border-admin-border text-xs">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                <span class="font-semibold text-slate-700 dark:text-zinc-200">Main 4K Projection Screens & Saturday Live Acoustic Stage</span>
                <span class="text-slate-400 dark:text-zinc-500 hidden md:inline">· Direct stage sightlines for Tables T1–T6</span>
            </div>
            <div class="font-semibold text-amber-700 dark:text-gold" x-text="currentSlot === 'LUNCH' ? 'Occupancy: {{ count($todayLunchBookings) }} of {{ count($tables) }} Tables Booked' : 'Occupancy: {{ count($todayEveningBookings) }} of {{ count($tables) }} Tables Booked'">
            </div>
        </div>

        <!-- LUNCH TABLES -->
        <div x-show="currentSlot === 'LUNCH'" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach($tables as $t)
                @php
                    $isBooked = isset($todayLunchBookings[$t->code]);
                    $booking = $isBooked ? $todayLunchBookings[$t->code] : null;
                @endphp
                <div class="p-3.5 rounded-xl border transition {{ $isBooked ? 'border-red-300 dark:border-red-900/60 bg-red-50 dark:bg-red-950/20' : 'border-emerald-300 dark:border-emerald-900/60 bg-emerald-50 dark:bg-emerald-950/20' }} flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="font-display text-lg font-bold text-slate-900 dark:text-white">{{ $t->code }}</span>
                            <span class="text-[9px] px-1.5 py-0.5 rounded font-bold uppercase {{ $isBooked ? 'bg-red-200 text-red-800 dark:bg-red-900/80 dark:text-red-200' : 'bg-emerald-200 text-emerald-800 dark:bg-emerald-900/80 dark:text-emerald-200' }}">
                                {{ $isBooked ? 'BOOKED' : 'FREE' }}
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-700 dark:text-zinc-300 font-medium block truncate">{{ $t->zone }}</span>
                        <span class="text-[10px] text-slate-500 dark:text-zinc-400 block mb-2">{{ $t->seats }}</span>
                    </div>

                    @if($booking)
                        <div class="pt-2 border-t border-red-200 dark:border-red-900/40 text-[11px] space-y-0.5">
                            <div class="font-semibold text-amber-700 dark:text-gold truncate">{{ $booking->name }}</div>
                            <div class="text-slate-500 dark:text-zinc-400 text-[10px]">{{ $booking->phone }} ({{ $booking->guests }}p)</div>
                        </div>
                    @else
                        <div class="pt-2 border-t border-emerald-200 dark:border-emerald-900/40 text-[10px] text-emerald-700 dark:text-emerald-400 font-medium flex items-center justify-between">
                            <span>Available</span>
                            <a href="{{ route('admin.reservations.create') }}?table={{ $t->code }}&slot=LUNCH&date={{ $today }}" class="text-amber-700 dark:text-gold hover:underline font-bold">+ Book</a>
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
                <div class="p-3.5 rounded-xl border transition {{ $isBooked ? 'border-red-300 dark:border-red-900/60 bg-red-50 dark:bg-red-950/20' : 'border-emerald-300 dark:border-emerald-900/60 bg-emerald-50 dark:bg-emerald-950/20' }} flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="font-display text-lg font-bold text-slate-900 dark:text-white">{{ $t->code }}</span>
                            <span class="text-[9px] px-1.5 py-0.5 rounded font-bold uppercase {{ $isBooked ? 'bg-red-200 text-red-800 dark:bg-red-900/80 dark:text-red-200' : 'bg-emerald-200 text-emerald-800 dark:bg-emerald-900/80 dark:text-emerald-200' }}">
                                {{ $isBooked ? 'BOOKED' : 'FREE' }}
                            </span>
                        </div>
                        <span class="text-[11px] text-slate-700 dark:text-zinc-300 font-medium block truncate">{{ $t->zone }}</span>
                        <span class="text-[10px] text-slate-500 dark:text-zinc-400 block mb-2">{{ $t->seats }}</span>
                    </div>

                    @if($booking)
                        <div class="pt-2 border-t border-red-200 dark:border-red-900/40 text-[11px] space-y-0.5">
                            <div class="font-semibold text-amber-700 dark:text-gold truncate">{{ $booking->name }}</div>
                            <div class="text-slate-500 dark:text-zinc-400 text-[10px]">{{ $booking->phone }} ({{ $booking->guests }}p)</div>
                        </div>
                    @else
                        <div class="pt-2 border-t border-emerald-200 dark:border-emerald-900/40 text-[10px] text-emerald-700 dark:text-emerald-400 font-medium flex items-center justify-between">
                            <span>Available</span>
                            <a href="{{ route('admin.reservations.create') }}?table={{ $t->code }}&slot=EVENING&date={{ $today }}" class="text-amber-700 dark:text-gold hover:underline font-bold">+ Book</a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- RECENT RESERVATIONS & FAN CLUB INSIGHTS GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Recent Reservations (2 cols) -->
        <div class="lg:col-span-2 p-5 rounded-2xl bg-admin-card border border-admin-border space-y-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-display text-lg font-bold uppercase tracking-wider text-slate-900 dark:text-white">Recent Table Bookings</h3>
                    <p class="text-xs text-slate-500 dark:text-zinc-400">Incoming Saturday acoustic & lunch reservations</p>
                </div>
                <a href="{{ route('admin.reservations.index') }}" class="text-xs text-amber-700 dark:text-gold hover:underline font-semibold">
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
                                    <span class="font-medium text-slate-900 dark:text-white block">{{ $r->name }}</span>
                                    <span class="text-slate-500 dark:text-zinc-400 text-[11px]">{{ $r->phone }} ({{ $r->guests }} guests)</span>
                                </td>
                                <td class="py-3">
                                    <span class="text-slate-700 dark:text-zinc-200 block">{{ \Carbon\Carbon::parse($r->date)->format('M d, Y') }}</span>
                                    <span class="text-amber-700 dark:text-gold text-[10px] uppercase font-semibold">{{ $r->slot === 'EVENING' ? 'Acoustic Jam' : 'Lunch' }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-admin-panel text-slate-800 dark:text-white font-bold border border-slate-200 dark:border-admin-border">{{ $r->tableCode }}</span>
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $r->status === 'CONFIRMED' ? 'bg-emerald-50 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/50' : 'bg-red-50 dark:bg-red-900/60 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-700/50' }}">
                                        {{ $r->status }}
                                    </span>
                                </td>
                                <td class="py-3 text-right space-x-1.5">
                                    <a href="{{ route('admin.reservations.edit', $r) }}" class="text-slate-600 dark:text-zinc-400 hover:text-amber-700 dark:hover:text-gold font-medium text-[11px]">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.reservations.status', $r) }}" class="inline">
                                        @csrf
                                        @if($r->status === 'CONFIRMED')
                                            <input type="hidden" name="status" value="CANCELLED">
                                            <button type="submit" class="px-2 py-0.5 rounded bg-red-100 hover:bg-red-200 dark:bg-red-950/60 dark:hover:bg-red-900 text-red-700 dark:text-red-300 text-[10px] transition font-semibold">Cancel</button>
                                        @else
                                            <input type="hidden" name="status" value="CONFIRMED">
                                            <button type="submit" class="px-2 py-0.5 rounded bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-950/60 dark:hover:bg-emerald-900 text-emerald-700 dark:text-emerald-300 text-[10px] transition font-semibold">Confirm</button>
                                        @endif
                                    </form>
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

        <!-- RIGHT SIDEBAR: FAN CLUB & NEXT MATCHES -->
        <div class="space-y-6">
            <!-- FAN CLUB STATS -->
            <div class="p-5 rounded-2xl bg-admin-card border border-admin-border space-y-3 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-display text-base font-bold uppercase tracking-wider text-slate-900 dark:text-white">Top Supported Clubs</h3>
                        <p class="text-[11px] text-slate-500 dark:text-zinc-400">Registered Fan Club community</p>
                    </div>
                    <a href="{{ route('admin.customers.index') }}" class="text-xs text-amber-700 dark:text-gold hover:underline font-semibold">Manage Fans →</a>
                </div>

                <div class="space-y-2.5 pt-1">
                    @forelse($topClubs ?? [] as $club)
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-semibold text-slate-800 dark:text-white">⚽ {{ $club->favorite_club }}</span>
                                <span class="text-amber-700 dark:text-gold font-bold font-mono">{{ $club->count }} fans</span>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-slate-100 dark:bg-admin-panel overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-amber-500 to-amber-600 dark:from-gold dark:to-amber-500 rounded-full" style="width: {{ min(100, $club->count * 20) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 dark:text-zinc-500">No registered fan clubs yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- UPCOMING BIG MATCHES -->
            <div class="p-5 rounded-2xl bg-admin-card border border-admin-border space-y-3 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-display text-base font-bold uppercase tracking-wider text-slate-900 dark:text-white">Upcoming Screenings</h3>
                        <p class="text-[11px] text-slate-500 dark:text-zinc-400">Live 4K stadium broadcasts</p>
                    </div>
                    <a href="{{ route('admin.matches.index') }}" class="text-xs text-amber-700 dark:text-gold hover:underline font-semibold">All Matches →</a>
                </div>

                <div class="space-y-2.5">
                    @forelse($upcomingMatches as $m)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-admin-panel border {{ $m->featured ? 'border-amber-400 dark:border-gold shadow-sm' : 'border-slate-200 dark:border-admin-border' }} space-y-1">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-slate-500 dark:text-zinc-400 font-mono">{{ \Carbon\Carbon::parse($m->kickoff)->setTimezone('Africa/Addis_Ababa')->format('D, M d · H:i') }} EAT</span>
                                @if($m->featured)
                                    <span class="px-1.5 py-0.2 rounded bg-amber-500 text-black text-[9px] font-bold uppercase">FEATURED</span>
                                @endif
                            </div>
                            <div class="font-semibold text-xs text-slate-900 dark:text-white">
                                {{ $m->homeTeam }} <span class="text-slate-400 dark:text-zinc-500 font-normal">vs</span> {{ $m->awayTeam }}
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-zinc-400 pt-0.5">
                                <span class="font-medium text-slate-600 dark:text-zinc-300">{{ $m->competition }}</span>
                                <form method="POST" action="{{ route('admin.matches.toggle-featured', $m) }}">
                                    @csrf
                                    <button type="submit" class="text-amber-700 dark:text-gold hover:underline font-semibold">
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
