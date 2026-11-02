@extends('admin.layout')

@section('title', 'New Reservation')
@section('header_title', 'Create Table Reservation')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- BREADCRUMB & BACK LINK -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2 text-xs">
            <a href="{{ route('admin.dashboard') }}" class="text-zinc-400 hover:text-gold transition">Dashboard</a>
            <span class="text-zinc-600">/</span>
            <a href="{{ route('admin.reservations.index') }}" class="text-zinc-400 hover:text-gold transition">Reservations</a>
            <span class="text-zinc-600">/</span>
            <span class="text-gold font-semibold">New Booking</span>
        </div>
        <a href="{{ route('admin.reservations.index') }}" class="text-xs text-zinc-400 hover:text-white transition flex items-center space-x-1">
            <span>← Back to List</span>
        </a>
    </div>

    <!-- BOOKING FORM CARD -->
    <div class="p-6 rounded-2xl bg-admin-card border border-admin-border shadow-xl space-y-6">
        <div class="flex items-center space-x-3 pb-4 border-b border-admin-border">
            <div class="w-10 h-10 rounded-xl bg-gold/15 border border-gold/40 text-gold flex items-center justify-center font-bold text-lg">
                📝
            </div>
            <div>
                <h3 class="font-display text-lg font-bold uppercase text-white tracking-wider">Book a Patron Table</h3>
                <p class="text-xs text-zinc-400">Manual table allocation for Saturday Acoustic or Lunch session</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.reservations.store') }}" class="space-y-5 text-xs">
            @csrf

            <!-- Date & Session Slot -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-zinc-300 font-semibold mb-1.5 uppercase tracking-wider text-[11px]">Reservation Date *</label>
                    <input type="date" name="date" required value="{{ old('date', $selectedDate) }}"
                           class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-gold">
                    <p class="text-[10px] text-zinc-500 mt-1">Saturday Acoustic nights or weekday lunch</p>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1.5 uppercase tracking-wider text-[11px]">Session Slot *</label>
                    <select name="slot" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-gold">
                        <option value="EVENING" {{ old('slot', $selectedSlot) === 'EVENING' ? 'selected' : '' }}>🎸 Evening Acoustic (7:00–11:30 PM)</option>
                        <option value="LUNCH" {{ old('slot', $selectedSlot) === 'LUNCH' ? 'selected' : '' }}>🍽️ Lunch Session (12:30–3:30 PM)</option>
                    </select>
                </div>
            </div>

            <!-- Table & Guests -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-zinc-300 font-semibold mb-1.5 uppercase tracking-wider text-[11px]">Table Allocation *</label>
                    <select name="tableCode" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-gold">
                        @foreach($tables as $t)
                            <option value="{{ $t->code }}" {{ old('tableCode', $selectedTable) === $t->code ? 'selected' : '' }}>
                                Table {{ $t->code }} — {{ $t->zone }} (Seats up to {{ $t->capacity }}p)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1.5 uppercase tracking-wider text-[11px]">Number of Guests *</label>
                    <input type="number" name="guests" min="1" max="25" value="{{ old('guests', 4) }}" required
                           class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-gold">
                </div>
            </div>

            <!-- Guest Name & Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-zinc-300 font-semibold mb-1.5 uppercase tracking-wider text-[11px]">Guest Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Dawit Bekele" required
                           class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-gold">
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1.5 uppercase tracking-wider text-[11px]">Phone / Mobile Contact *</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="e.g. 0911234567 or +251..." required
                           class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-gold">
                </div>
            </div>

            <!-- Email & Football Match Link -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-zinc-300 font-semibold mb-1.5 uppercase tracking-wider text-[11px]">Email Address (Optional)</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="guest@example.com"
                           class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-gold">
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1.5 uppercase tracking-wider text-[11px]">Link to Match Broadcast (Optional)</label>
                    <select name="matchId" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-gold">
                        <option value="">No Match Linked</option>
                        @foreach($matches as $m)
                            <option value="{{ $m->id }}" {{ old('matchId') == $m->id ? 'selected' : '' }}>
                                {{ $m->homeTeam }} vs {{ $m->awayTeam }} ({{ \Carbon\Carbon::parse($m->kickoff)->setTimezone('Africa/Addis_Ababa')->format('M d · H:i') }} EAT)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Special Requests -->
            <div>
                <label class="block text-zinc-300 font-semibold mb-1.5 uppercase tracking-wider text-[11px]">Special Requests / Notes (Optional)</label>
                <textarea name="request" rows="3" placeholder="Birthday celebration, high top preference, VIP arrival..."
                          class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2.5 text-white focus:outline-none focus:border-gold">{{ old('request') }}</textarea>
            </div>

            <!-- Initial Status -->
            <div>
                <label class="block text-zinc-300 font-semibold mb-1.5 uppercase tracking-wider text-[11px]">Booking Status *</label>
                <div class="flex items-center space-x-4">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="status" value="CONFIRMED" {{ old('status', 'CONFIRMED') === 'CONFIRMED' ? 'checked' : '' }} class="text-gold focus:ring-gold">
                        <span class="text-emerald-400 font-semibold">CONFIRMED (Lock Table)</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="status" value="CANCELLED" {{ old('status') === 'CANCELLED' ? 'checked' : '' }} class="text-gold focus:ring-gold">
                        <span class="text-red-400 font-semibold">CANCELLED</span>
                    </label>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-admin-border">
                <a href="{{ route('admin.reservations.index') }}" class="px-5 py-2.5 rounded-xl bg-admin-panel text-zinc-300 hover:text-white font-medium transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gold hover:bg-gold-light text-black font-bold shadow-md shadow-gold/20 transition flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Save Reservation</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
