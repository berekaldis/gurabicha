@extends('admin.layout')

@section('title', 'Manage Reservations')
@section('header_title', 'Reservations Management')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: false, showEditModal: false, activeReservation: null }">

    <!-- TOP CONTROLS & FILTER BAR -->
    <div class="p-5 rounded-2xl bg-admin-card border border-admin-border space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-display text-xl font-bold uppercase text-white tracking-wider">Patron Reservations</h3>
                <p class="text-xs text-zinc-400">Search, filter, edit, or manually create table bookings</p>
            </div>
            <button @click="showCreateModal = true" class="px-4 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-semibold text-xs transition shadow-md shadow-gold/20 flex items-center space-x-1.5 self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Manual Booking</span>
            </button>
        </div>

        <!-- FILTERS FORM -->
        <form method="GET" action="{{ route('admin.reservations.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3 pt-2">
            <!-- Search -->
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone or GB code..." class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2 text-white text-xs focus:outline-none focus:border-gold">
            </div>

            <!-- Date -->
            <div>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2 text-white text-xs focus:outline-none focus:border-gold">
            </div>

            <!-- Slot -->
            <div>
                <select name="slot" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-gold">
                    <option value="">All Sessions</option>
                    <option value="LUNCH" {{ request('slot') === 'LUNCH' ? 'selected' : '' }}>Lunch Session</option>
                    <option value="EVENING" {{ request('slot') === 'EVENING' ? 'selected' : '' }}>Evening Session</option>
                </select>
            </div>

            <!-- Status & Submit -->
            <div class="flex space-x-2">
                <select name="status" class="w-1/2 bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-gold">
                    <option value="">Status</option>
                    <option value="CONFIRMED" {{ request('status') === 'CONFIRMED' ? 'selected' : '' }}>Confirmed</option>
                    <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="w-1/2 rounded-xl bg-admin-panel hover:bg-gold text-zinc-300 hover:text-black font-semibold text-xs border border-admin-border transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- RESERVATIONS TABLE -->
    <div class="rounded-2xl bg-admin-card border border-admin-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-admin-panel text-zinc-400 border-b border-admin-border">
                    <tr>
                        <th class="py-3 px-4">Code</th>
                        <th class="py-3 px-4">Date & Time</th>
                        <th class="py-3 px-4">Table</th>
                        <th class="py-3 px-4">Guest Info</th>
                        <th class="py-3 px-4">Guests</th>
                        <th class="py-3 px-4">Match / Notes</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border/60">
                    @forelse($reservations as $res)
                        <tr class="hover:bg-admin-panel/30 transition">
                            <td class="py-3 px-4 font-mono font-bold text-gold">
                                {{ $res->code }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-medium text-white block">{{ \Carbon\Carbon::parse($res->date)->format('M d, Y') }}</span>
                                <span class="text-[10px] text-zinc-400 uppercase tracking-wider">{{ $res->slot }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded bg-admin-panel border border-admin-border font-bold text-white text-xs">
                                    {{ $res->tableCode }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-white block">{{ $res->name }}</span>
                                <span class="text-zinc-400 text-[11px] block">{{ $res->phone }}</span>
                                @if($res->email)
                                    <span class="text-zinc-500 text-[10px] block truncate max-w-[150px]">{{ $res->email }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-bold text-white">
                                {{ $res->guests }}
                            </td>
                            <td class="py-3 px-4 max-w-[200px]">
                                @if($res->matchLabel)
                                    <span class="text-gold font-medium block truncate text-[11px]">⚽ {{ $res->matchLabel }}</span>
                                @endif
                                @if($res->request)
                                    <span class="text-zinc-400 text-[10px] block truncate italic">"{{ $res->request }}"</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $res->status === 'CONFIRMED' ? 'bg-emerald-900/60 text-emerald-300' : 'bg-red-900/60 text-red-300' }}">
                                    {{ $res->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2 whitespace-nowrap">
                                <!-- Edit Button -->
                                <button @click="activeReservation = {{ json_encode($res) }}; showEditModal = true" class="text-zinc-400 hover:text-gold transition">
                                    Edit
                                </button>

                                <!-- Toggle Status -->
                                <form method="POST" action="{{ route('admin.reservations.status', $res) }}" class="inline">
                                    @csrf
                                    @if($res->status === 'CONFIRMED')
                                        <input type="hidden" name="status" value="CANCELLED">
                                        <button type="submit" class="text-red-400 hover:underline">Cancel</button>
                                    @else
                                        <input type="hidden" name="status" value="CONFIRMED">
                                        <button type="submit" class="text-emerald-400 hover:underline">Confirm</button>
                                    @endif
                                </form>

                                <!-- Delete -->
                                <form method="POST" action="{{ route('admin.reservations.destroy', $res) }}" class="inline" onsubmit="return confirm('Permanently delete this reservation?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-zinc-500 hover:text-red-400">✕</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-zinc-500">
                                No reservations found matching the criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($reservations->hasPages())
            <div class="p-4 border-t border-admin-border bg-admin-panel/40">
                {{ $reservations->links() }}
            </div>
        @endif
    </div>

    <!-- CREATE MODAL -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-lg w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <h3 class="font-display text-lg font-bold uppercase text-white">Create New Reservation</h3>
                <button @click="showCreateModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.reservations.store') }}" class="space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Date *</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Session *</label>
                        <select name="slot" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                            <option value="EVENING">Evening (7:00–11:00 PM)</option>
                            <option value="LUNCH">Lunch (12:30–3:30 PM)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Table *</label>
                        <select name="tableCode" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                            @foreach($tables as $t)
                                <option value="{{ $t->code }}">{{ $t->code }} ({{ $t->zone }}, max {{ $t->capacity }}p)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Guests *</label>
                        <input type="number" name="guests" min="1" max="12" value="4" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Guest Name *</label>
                        <input type="text" name="name" placeholder="Full name" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Phone Number *</label>
                        <input type="tel" name="phone" placeholder="0911234567" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Email (Optional)</label>
                    <input type="email" name="email" placeholder="guest@example.com" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Link to Football Match (Optional)</label>
                    <select name="matchId" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                        <option value="">No Match Linked</option>
                        @foreach($matches as $m)
                            <option value="{{ $m->id }}">
                                {{ $m->homeTeam }} vs {{ $m->awayTeam }} ({{ \Carbon\Carbon::parse($m->kickoff)->format('M d') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Special Notes / Requests</label>
                    <textarea name="request" rows="2" placeholder="VIP greeting, birthday cake, screen table..." class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold"></textarea>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Initial Status</label>
                    <select name="status" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                        <option value="CONFIRMED">CONFIRMED</option>
                        <option value="CANCELLED">CANCELLED</option>
                    </select>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl bg-admin-panel text-zinc-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-semibold">Save Reservation</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-lg w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <h3 class="font-display text-lg font-bold uppercase text-white">
                    Edit Reservation <span class="text-gold" x-text="activeReservation ? activeReservation.code : ''"></span>
                </h3>
                <button @click="showEditModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <form :action="'{{ url('admin/reservations') }}/' + (activeReservation ? activeReservation.id : '')" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Date *</label>
                        <input type="date" name="date" required :value="activeReservation ? activeReservation.date.slice(0, 10) : ''" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Session *</label>
                        <select name="slot" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                            <option value="EVENING" :selected="activeReservation && activeReservation.slot === 'EVENING'">Evening (7:00–11:00 PM)</option>
                            <option value="LUNCH" :selected="activeReservation && activeReservation.slot === 'LUNCH'">Lunch (12:30–3:30 PM)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Table *</label>
                        <select name="tableCode" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                            @foreach($tables as $t)
                                <option value="{{ $t->code }}" :selected="activeReservation && activeReservation.tableCode === '{{ $t->code }}'">
                                    {{ $t->code }} ({{ $t->zone }}, max {{ $t->capacity }}p)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Guests *</label>
                        <input type="number" name="guests" min="1" max="12" :value="activeReservation ? activeReservation.guests : 4" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Guest Name *</label>
                        <input type="text" name="name" :value="activeReservation ? activeReservation.name : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Phone Number *</label>
                        <input type="tel" name="phone" :value="activeReservation ? activeReservation.phone : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Email</label>
                    <input type="email" name="email" :value="activeReservation ? activeReservation.email : ''" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Special Notes / Requests</label>
                    <textarea name="request" rows="2" :value="activeReservation ? activeReservation.request : ''" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold"></textarea>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Status</label>
                    <select name="status" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                        <option value="CONFIRMED" :selected="activeReservation && activeReservation.status === 'CONFIRMED'">CONFIRMED</option>
                        <option value="CANCELLED" :selected="activeReservation && activeReservation.status === 'CANCELLED'">CANCELLED</option>
                    </select>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-admin-panel text-zinc-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-semibold">Update Reservation</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
