@extends('admin.layout')

@section('title', 'Manage Food & Bar Menu')
@section('header_title', 'Menu & Pricing Management')

@section('content')
<div class="space-y-6" x-data="{
    activeCategory: 'FOOD',
    searchQuery: '',
    showCreateModal: false,
    showEditModal: false,
    activeItem: null,
    newImageUrl: '',
    editImageUrl: ''
}">

    <!-- HEADER & ACTIONS -->
    <div class="p-6 rounded-2xl bg-admin-card border border-admin-border flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-lg">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-gold animate-pulse"></span>
                <h3 class="font-display text-xl font-bold uppercase text-white tracking-wider">Kitchen & Bar Menu</h3>
            </div>
            <p class="text-xs text-zinc-400 mt-1">Manage authentic Ethiopian specialties, craft beverages, ETB prices, and photo presentation</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.settings.index') }}" class="px-3.5 py-2 rounded-xl bg-admin-panel hover:bg-zinc-800 border border-admin-border text-xs text-zinc-300 hover:text-white transition flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                <span>Print Table Tent QR</span>
            </a>
            <button @click="showCreateModal = true; newImageUrl = ''" class="px-4 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-bold text-xs transition shadow-lg shadow-gold/20 flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Menu Item</span>
            </button>
        </div>
    </div>

    <!-- CATEGORY TABS & SEARCH BAR -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-admin-border pb-3">
        <div class="flex items-center space-x-2">
            <button @click="activeCategory = 'FOOD'" :class="activeCategory === 'FOOD' ? 'bg-gold text-black font-bold shadow-md shadow-gold/20' : 'bg-admin-panel text-zinc-400 hover:text-white border border-admin-border'" class="px-4 py-2 rounded-xl text-xs transition flex items-center space-x-2">
                <span>🍽️ Food</span>
                <span class="px-2 py-0.5 rounded-full text-[10px]" :class="activeCategory === 'FOOD' ? 'bg-black text-gold font-bold' : 'bg-zinc-800 text-zinc-400'">{{ $food->count() }}</span>
            </button>
            <button @click="activeCategory = 'PIZZA'" :class="activeCategory === 'PIZZA' ? 'bg-gold text-black font-bold shadow-md shadow-gold/20' : 'bg-admin-panel text-zinc-400 hover:text-white border border-admin-border'" class="px-4 py-2 rounded-xl text-xs transition flex items-center space-x-2">
                <span>🍕 Pizza</span>
                <span class="px-2 py-0.5 rounded-full text-[10px]" :class="activeCategory === 'PIZZA' ? 'bg-black text-gold font-bold' : 'bg-zinc-800 text-zinc-400'">{{ $pizza->count() }}</span>
            </button>
            <button @click="activeCategory = 'DRINKS'" :class="activeCategory === 'DRINKS' ? 'bg-gold text-black font-bold shadow-md shadow-gold/20' : 'bg-admin-panel text-zinc-400 hover:text-white border border-admin-border'" class="px-4 py-2 rounded-xl text-xs transition flex items-center space-x-2">
                <span>🍺 Beers & Drinks</span>
                <span class="px-2 py-0.5 rounded-full text-[10px]" :class="activeCategory === 'DRINKS' ? 'bg-black text-gold font-bold' : 'bg-zinc-800 text-zinc-400'">{{ $drinks->count() }}</span>
            </button>
        </div>

        <div class="relative w-full sm:w-64">
            <input type="text" x-model="searchQuery" placeholder="Filter dishes..." class="w-full bg-admin-panel border border-admin-border rounded-xl pl-8 pr-3 py-1.5 text-xs text-white focus:outline-none focus:border-gold">
            <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </div>

    <!-- FOOD GRID -->
    <div x-show="activeCategory === 'FOOD'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($food as $item)
            <div x-show="!searchQuery || '{{ strtolower(addslashes($item->name . ' ' . $item->description)) }}'.includes(searchQuery.toLowerCase())"
                 class="group rounded-2xl bg-admin-card border border-admin-border hover:border-gold/50 transition-all duration-200 overflow-hidden shadow-md hover:shadow-gold/10 flex flex-col justify-between">
                <div>
                    <!-- Photo Header -->
                    <div class="relative h-44 w-full bg-zinc-900 overflow-hidden">
                        @if($item->imageUrl)
                            <img src="{{ $item->imageUrl }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1544025162-d76694265947?w=600&auto=format&fit=crop&q=80';">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-zinc-800 to-zinc-950 text-zinc-600">
                                <svg class="w-10 h-10 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/20"></div>

                        <div class="absolute top-3 left-3 flex items-center space-x-1.5">
                            <span class="px-2 py-0.5 rounded-md bg-black/70 backdrop-blur-md text-gold text-[10px] font-bold uppercase tracking-wider border border-gold/30">
                                {{ $item->category }}
                            </span>
                            @if($item->popular)
                                <span class="px-2 py-0.5 rounded-md bg-amber-500 text-black text-[10px] font-extrabold shadow-md">
                                    ★ POPULAR
                                </span>
                            @endif
                        </div>

                        <div class="absolute bottom-3 right-3">
                            <span class="font-display font-extrabold text-base text-gold bg-black/80 backdrop-blur-md px-2.5 py-1 rounded-lg border border-gold/40">
                                {{ number_format($item->price, 0) }} ETB
                            </span>
                        </div>
                    </div>

                    <!-- Details Body -->
                    <div class="p-4 space-y-1.5">
                        <h4 class="font-semibold text-sm text-white group-hover:text-gold transition">{{ $item->name }}</h4>
                        <p class="text-xs text-zinc-400 line-clamp-2 leading-relaxed">{{ $item->description }}</p>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-4 pt-2 border-t border-admin-border/60 flex items-center justify-between text-xs">
                    <button @click="activeItem = {{ json_encode($item) }}; editImageUrl = '{{ $item->imageUrl }}'; showEditModal = true" class="px-3 py-1.5 rounded-lg bg-admin-panel hover:bg-gold text-zinc-300 hover:text-black font-semibold transition flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Edit</span>
                    </button>
                    <form method="POST" action="{{ route('admin.menu.destroy', $item) }}" onsubmit="return confirm('Delete this menu item?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-zinc-500 hover:text-red-400 transition rounded hover:bg-red-500/10" title="Delete Item">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- PIZZA GRID -->
    <div x-show="activeCategory === 'PIZZA'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($pizza as $item)
            <div x-show="!searchQuery || '{{ strtolower(addslashes($item->name . ' ' . $item->description)) }}'.includes(searchQuery.toLowerCase())"
                 class="group rounded-2xl bg-admin-card border border-admin-border hover:border-gold/50 transition-all duration-200 overflow-hidden shadow-md hover:shadow-gold/10 flex flex-col justify-between">
                <div>
                    <!-- Photo Header -->
                    <div class="relative h-44 w-full bg-zinc-900 overflow-hidden">
                        @if($item->imageUrl)
                            <img src="{{ $item->imageUrl }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&auto=format&fit=crop&q=80';">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-zinc-800 to-zinc-950 text-zinc-600">
                                <svg class="w-10 h-10 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/20"></div>

                        <div class="absolute top-3 left-3 flex items-center space-x-1.5">
                            <span class="px-2 py-0.5 rounded-md bg-black/70 backdrop-blur-md text-gold text-[10px] font-bold uppercase tracking-wider border border-gold/30">
                                {{ $item->category }}
                            </span>
                            @if($item->popular)
                                <span class="px-2 py-0.5 rounded-md bg-amber-500 text-black text-[10px] font-extrabold shadow-md">
                                    ★ POPULAR
                                </span>
                            @endif
                        </div>

                        <div class="absolute bottom-3 right-3">
                            <span class="font-display font-extrabold text-base text-gold bg-black/80 backdrop-blur-md px-2.5 py-1 rounded-lg border border-gold/40">
                                {{ number_format($item->price, 0) }} ETB
                            </span>
                        </div>
                    </div>

                    <!-- Details Body -->
                    <div class="p-4 space-y-1.5">
                        <h4 class="font-semibold text-sm text-white group-hover:text-gold transition">{{ $item->name }}</h4>
                        <p class="text-xs text-zinc-400 line-clamp-2 leading-relaxed">{{ $item->description }}</p>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-4 pt-2 border-t border-admin-border/60 flex items-center justify-between text-xs">
                    <span class="font-mono text-gold font-bold">{{ number_format($item->price, 0) }} ETB</span>
                    <div class="flex items-center space-x-1">
                        <button type="button" @click="activeItem = {{ json_encode($item) }}; editImageUrl = '{{ $item->imageUrl }}'; showEditModal = true" class="px-2.5 py-1 rounded-lg bg-admin-panel hover:bg-zinc-800 text-zinc-300 hover:text-white transition flex items-center space-x-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Edit</span>
                        </button>
                        <form method="POST" action="{{ route('admin.menu.destroy', $item) }}" onsubmit="return confirm('Delete {{ $item->name }} from menu?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-zinc-500 hover:text-red-400 transition rounded hover:bg-red-500/10" title="Delete Item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- DRINKS GRID -->
    <div x-show="activeCategory === 'DRINKS'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($drinks as $item)
            <div x-show="!searchQuery || '{{ strtolower(addslashes($item->name . ' ' . $item->description)) }}'.includes(searchQuery.toLowerCase())"
                 class="group rounded-2xl bg-admin-card border border-admin-border hover:border-gold/50 transition-all duration-200 overflow-hidden shadow-md hover:shadow-gold/10 flex flex-col justify-between">
                <div>
                    <!-- Photo Header -->
                    <div class="relative h-44 w-full bg-zinc-900 overflow-hidden">
                        @if($item->imageUrl)
                            <img src="{{ $item->imageUrl }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=600&auto=format&fit=crop&q=80';">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-zinc-800 to-zinc-950 text-zinc-600">
                                <svg class="w-10 h-10 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/20"></div>

                        <div class="absolute top-3 left-3 flex items-center space-x-1.5">
                            <span class="px-2 py-0.5 rounded-md bg-black/70 backdrop-blur-md text-gold text-[10px] font-bold uppercase tracking-wider border border-gold/30">
                                {{ $item->category }}
                            </span>
                            @if($item->popular)
                                <span class="px-2 py-0.5 rounded-md bg-amber-500 text-black text-[10px] font-extrabold shadow-md">
                                    ★ POPULAR
                                </span>
                            @endif
                        </div>

                        <div class="absolute bottom-3 right-3">
                            <span class="font-display font-extrabold text-base text-gold bg-black/80 backdrop-blur-md px-2.5 py-1 rounded-lg border border-gold/40">
                                {{ number_format($item->price, 0) }} ETB
                            </span>
                        </div>
                    </div>

                    <!-- Details Body -->
                    <div class="p-4 space-y-1.5">
                        <h4 class="font-semibold text-sm text-white group-hover:text-gold transition">{{ $item->name }}</h4>
                        <p class="text-xs text-zinc-400 line-clamp-2 leading-relaxed">{{ $item->description }}</p>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-4 pt-2 border-t border-admin-border/60 flex items-center justify-between text-xs">
                    <button @click="activeItem = {{ json_encode($item) }}; editImageUrl = '{{ $item->imageUrl }}'; showEditModal = true" class="px-3 py-1.5 rounded-lg bg-admin-panel hover:bg-gold text-zinc-300 hover:text-black font-semibold transition flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span>Edit</span>
                    </button>
                    <form method="POST" action="{{ route('admin.menu.destroy', $item) }}" onsubmit="return confirm('Delete this menu item?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-zinc-500 hover:text-red-400 transition rounded hover:bg-red-500/10" title="Delete Item">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- CREATE ITEM MODAL (WITH LIVE PHOTO PREVIEW) -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <h3 class="font-display text-lg font-bold uppercase text-white">Add Menu Item</h3>
                <button @click="showCreateModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.menu.store') }}" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Item Name *</label>
                    <input type="text" name="name" placeholder="Special Kitfo, St. George Draft..." required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Category *</label>
                        <select name="category" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                            <option value="FOOD">FOOD (Burgers, Sandwiches, Wings, Buffet)</option>
                            <option value="PIZZA">PIZZA (Margherita, Tuna, Stone-Baked)</option>
                            <option value="DRINKS">DRINKS (Beer, Draft, Mojito)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Price (ETB) *</label>
                        <input type="number" step="1" name="price" placeholder="450" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Photo Image URL</label>
                    <input type="url" name="imageUrl" x-model="newImageUrl" placeholder="https://images.unsplash.com/..." class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    <p class="text-[10px] text-zinc-500 mt-0.5">Direct image URL (e.g. Unsplash, hosted image)</p>
                </div>

                <template x-if="newImageUrl">
                    <div class="w-full h-28 rounded-xl overflow-hidden border border-gold/40 bg-zinc-900 shadow-inner">
                        <img :src="newImageUrl" alt="Preview" class="w-full h-full object-cover">
                    </div>
                </template>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Description *</label>
                    <textarea name="description" rows="3" placeholder="Ingredients, flavor notes, preparation..." required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold"></textarea>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" id="item_popular" name="popular" value="1" class="rounded border-admin-border text-gold focus:ring-gold bg-admin-panel">
                    <label for="item_popular" class="text-zinc-300 font-medium">Highlight with "★ POPULAR" badge</label>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl bg-admin-panel text-zinc-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-semibold">Save Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT ITEM MODAL (WITH LIVE PHOTO PREVIEW) -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <h3 class="font-display text-lg font-bold uppercase text-white">Edit Menu Item</h3>
                <button @click="showEditModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <form :action="'{{ url('admin/menu') }}/' + (activeItem ? activeItem.id : '')" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Item Name *</label>
                    <input type="text" name="name" :value="activeItem ? activeItem.name : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Category *</label>
                        <select name="category" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                            <option value="FOOD" :selected="activeItem && activeItem.category === 'FOOD'">FOOD</option>
                            <option value="PIZZA" :selected="activeItem && activeItem.category === 'PIZZA'">PIZZA</option>
                            <option value="DRINKS" :selected="activeItem && activeItem.category === 'DRINKS'">DRINKS</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Price (ETB) *</label>
                        <input type="number" step="1" name="price" :value="activeItem ? activeItem.price : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Photo Image URL</label>
                    <input type="url" name="imageUrl" x-model="editImageUrl" placeholder="https://images.unsplash.com/..." class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <template x-if="editImageUrl">
                    <div class="w-full h-28 rounded-xl overflow-hidden border border-gold/40 bg-zinc-900 shadow-inner">
                        <img :src="editImageUrl" alt="Preview" class="w-full h-full object-cover">
                    </div>
                </template>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Description *</label>
                    <textarea name="description" rows="3" :value="activeItem ? activeItem.description : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold"></textarea>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" id="edit_popular" name="popular" value="1" :checked="activeItem && activeItem.popular" class="rounded border-admin-border text-gold focus:ring-gold bg-admin-panel">
                    <label for="edit_popular" class="text-zinc-300 font-medium">Highlight with "★ POPULAR" badge</label>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-admin-panel text-zinc-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-semibold">Update Item</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
