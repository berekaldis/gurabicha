@extends('admin.layout')

@section('title', 'Manage Venue Tables')
@section('header_title', 'Venue Tables & Seating Zones')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: false, showEditModal: false, activeTable: null }">

    <!-- HEADER & ACTIONS -->
    <div class="p-5 rounded-2xl bg-admin-card border border-admin-border flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h3 class="font-display text-xl font-bold uppercase text-white tracking-wider">Venue Seating Layout</h3>
            <p class="text-xs text-zinc-400">Configure tables, seating capacities, and viewing zones</p>
        </div>
        <button @click="showCreateModal = true" class="px-4 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-semibold text-xs transition shadow-md shadow-gold/20 flex items-center space-x-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add New Table</span>
        </button>
    </div>

    <!-- TABLES GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($tables as $t)
            <div class="p-5 rounded-2xl bg-admin-card border border-admin-border hover:border-gold/40 transition space-y-3">
                <div class="flex items-center justify-between">
                    <span class="font-display text-2xl font-bold text-gold">{{ $t->code }}</span>
                    <span class="text-xs px-2.5 py-1 rounded-full bg-admin-panel border border-admin-border text-zinc-300 font-semibold">
                        Seats up to {{ $t->capacity }}
                    </span>
                </div>

                <div class="text-xs text-zinc-300 space-y-1">
                    <div>
                        <span class="text-zinc-500 font-medium">Zone:</span>
                        <span class="text-white font-semibold">{{ $t->zone }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500 font-medium">Description:</span>
                        <span class="text-zinc-400">{{ $t->seats }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500 font-medium">Confirmed Bookings:</span>
                        <span class="text-gold font-bold">{{ $t->reservations_count }}</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-admin-border/60 flex items-center justify-between text-xs">
                    <button @click="activeTable = {{ json_encode($t) }}; showEditModal = true" class="px-3 py-1.5 rounded-lg bg-admin-panel hover:bg-gold text-zinc-300 hover:text-black font-semibold transition">
                        Edit Table
                    </button>

                    <form method="POST" action="{{ route('admin.tables.destroy', $t) }}" onsubmit="return confirm('Delete Table {{ $t->code }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-zinc-500 hover:text-red-400 text-xs transition">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- CREATE TABLE MODAL -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-md w-full p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <h3 class="font-display text-lg font-bold uppercase text-white">Add New Table</h3>
                <button @click="showCreateModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.tables.store') }}" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Table Code (e.g. T7) *</label>
                    <input type="text" name="code" placeholder="T7" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>
                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Zone Area *</label>
                    <input type="text" name="zone" placeholder="Acoustic Stage Front, Mezzanine Lounge, Center Floor" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>
                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Maximum Capacity (Guests) *</label>
                    <input type="number" name="capacity" min="1" max="25" value="6" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>
                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Seating Description *</label>
                    <input type="text" name="seats" placeholder="4–6 people" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl bg-admin-panel text-zinc-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-semibold">Save Table</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT TABLE MODAL -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-md w-full p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <h3 class="font-display text-lg font-bold uppercase text-white">Edit Table</h3>
                <button @click="showEditModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <form :action="'{{ url('admin/tables') }}/' + (activeTable ? activeTable.id : '')" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Table Code *</label>
                    <input type="text" name="code" :value="activeTable ? activeTable.code : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>
                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Zone Area *</label>
                    <input type="text" name="zone" :value="activeTable ? activeTable.zone : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>
                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Maximum Capacity *</label>
                    <input type="number" name="capacity" min="1" max="25" :value="activeTable ? activeTable.capacity : 6" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>
                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Seating Description *</label>
                    <input type="text" name="seats" :value="activeTable ? activeTable.seats : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-admin-panel text-zinc-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-semibold">Update Table</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
