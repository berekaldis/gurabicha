@extends('admin.layout')

@section('title', 'Manage Events')
@section('header_title', 'Events & Live Music Management')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: false, showEditModal: false, activeEvent: null }">

    <!-- HEADER & ACTIONS -->
    <div class="p-5 rounded-2xl bg-admin-card border border-admin-border flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h3 class="font-display text-xl font-bold uppercase text-white tracking-wider">Venue Events</h3>
            <p class="text-xs text-zinc-400">Manage acoustic Saturdays, featured entertainment, and live Addis bands</p>
        </div>
        <button @click="showCreateModal = true" class="px-4 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-semibold text-xs transition shadow-md shadow-gold/20 flex items-center space-x-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add New Event</span>
        </button>
    </div>

    <!-- EVENTS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($events as $e)
            <div class="rounded-2xl bg-admin-card border {{ $e->featured ? 'border-gold shadow-lg shadow-gold/10' : 'border-admin-border' }} overflow-hidden space-y-3">
                <div class="h-36 relative overflow-hidden bg-admin-panel">
                    <img src="{{ $e->imageUrl ?: asset('images/event-acoustic.jpg') }}" alt="{{ $e->title }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-admin-card via-admin-card/40 to-transparent"></div>
                    <span class="absolute top-3 left-3 px-2 py-0.5 rounded bg-gold text-black text-[10px] font-bold uppercase tracking-wider">
                        {{ $e->category }}
                    </span>
                    @if($e->featured)
                        <span class="absolute top-3 right-3 px-2 py-0.5 rounded bg-amber-500 text-black text-[9px] font-bold uppercase">
                            ★ Featured
                        </span>
                    @endif
                </div>

                <div class="p-4 pt-1 space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="font-display text-lg font-bold text-white uppercase">{{ $e->title }}</h4>
                        <span class="text-xs text-zinc-400">{{ \Carbon\Carbon::parse($e->date)->format('M d, Y') }}</span>
                    </div>

                    <div class="text-xs text-gold font-semibold">
                        {{ $e->startTime }} – {{ $e->endTime }}
                    </div>

                    <p class="text-xs text-zinc-400 line-clamp-2">
                        {{ $e->description }}
                    </p>

                    <div class="pt-3 border-t border-admin-border/60 flex items-center justify-between text-xs">
                        <button @click="activeEvent = {{ json_encode($e) }}; showEditModal = true" class="px-3 py-1.5 rounded-lg bg-admin-panel hover:bg-gold text-zinc-300 hover:text-black font-semibold transition">
                            Edit Event
                        </button>

                        <form method="POST" action="{{ route('admin.events.destroy', $e) }}" onsubmit="return confirm('Delete this event?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-zinc-500 hover:text-red-400 transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- CREATE EVENT MODAL -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-lg w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <h3 class="font-display text-lg font-bold uppercase text-white">Create New Event</h3>
                <button @click="showCreateModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.events.store') }}" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Title *</label>
                        <input type="text" name="title" placeholder="Game & Groove Acoustic" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Category *</label>
                        <input type="text" name="category" placeholder="Acoustic Saturdays, Live Band" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Date *</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Start Time *</label>
                        <input type="text" name="startTime" placeholder="7:00 PM" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">End Time *</label>
                        <input type="text" name="endTime" placeholder="11:00 PM" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Banner Image URL</label>
                    <input type="text" name="imageUrl" placeholder="/images/event-acoustic.jpg" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Description *</label>
                    <textarea name="description" rows="3" placeholder="Live acoustic sessions, match viewing, food specials..." required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold"></textarea>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" id="featured" name="featured" value="1" class="rounded border-admin-border text-gold focus:ring-gold bg-admin-panel">
                    <label for="featured" class="text-zinc-300 font-medium">Feature this event on the homepage hero banner</label>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl bg-admin-panel text-zinc-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-semibold">Save Event</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT EVENT MODAL -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-lg w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <h3 class="font-display text-lg font-bold uppercase text-white">Edit Event</h3>
                <button @click="showEditModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <form :action="'{{ url('admin/events') }}/' + (activeEvent ? activeEvent.id : '')" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Title *</label>
                        <input type="text" name="title" :value="activeEvent ? activeEvent.title : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Category *</label>
                        <input type="text" name="category" :value="activeEvent ? activeEvent.category : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Date *</label>
                        <input type="date" name="date" :value="activeEvent ? activeEvent.date.slice(0, 10) : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Start Time *</label>
                        <input type="text" name="startTime" :value="activeEvent ? activeEvent.startTime : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">End Time *</label>
                        <input type="text" name="endTime" :value="activeEvent ? activeEvent.endTime : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Banner Image URL</label>
                    <input type="text" name="imageUrl" :value="activeEvent ? activeEvent.imageUrl : ''" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Description *</label>
                    <textarea name="description" rows="3" :value="activeEvent ? activeEvent.description : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold"></textarea>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" id="edit_featured" name="featured" value="1" :checked="activeEvent && activeEvent.featured" class="rounded border-admin-border text-gold focus:ring-gold bg-admin-panel">
                    <label for="edit_featured" class="text-zinc-300 font-medium">Feature this event on the homepage</label>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-admin-panel text-zinc-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-semibold">Update Event</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
