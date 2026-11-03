@extends('admin.layout')

@section('title', 'Feedback & Suggestions')
@section('header_title', 'Patron Feedback & Suggestions')

@section('content')
<div class="space-y-6" x-data="{ activeFeedback: null, showModal: false }">

    <!-- KPI CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 sm:p-5 rounded-2xl bg-admin-card border border-admin-border flex items-center justify-between">
            <div>
                <span class="text-xs text-zinc-400 font-semibold block uppercase">Total Submissions</span>
                <span class="font-display text-2xl sm:text-3xl font-bold text-white mt-1 block">{{ $counts['total'] }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-gold/15 text-gold border border-gold/30 flex items-center justify-center text-lg font-bold">
                💬
            </div>
        </div>

        <div class="p-4 sm:p-5 rounded-2xl bg-admin-card border border-admin-border flex items-center justify-between">
            <div>
                <span class="text-xs text-amber-500 font-semibold block uppercase">New / Pending</span>
                <span class="font-display text-2xl sm:text-3xl font-bold text-amber-400 mt-1 block">{{ $counts['new'] }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/15 text-amber-400 border border-amber-500/30 flex items-center justify-center text-lg font-bold">
                ⏳
            </div>
        </div>

        <div class="p-4 sm:p-5 rounded-2xl bg-admin-card border border-admin-border flex items-center justify-between">
            <div>
                <span class="text-xs text-blue-400 font-semibold block uppercase">Reviewed</span>
                <span class="font-display text-2xl sm:text-3xl font-bold text-blue-400 mt-1 block">{{ $counts['reviewed'] }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-500/15 text-blue-400 border border-blue-500/30 flex items-center justify-center text-lg font-bold">
                👀
            </div>
        </div>

        <div class="p-4 sm:p-5 rounded-2xl bg-admin-card border border-admin-border flex items-center justify-between">
            <div>
                <span class="text-xs text-emerald-400 font-semibold block uppercase">Resolved / Actioned</span>
                <span class="font-display text-2xl sm:text-3xl font-bold text-emerald-400 mt-1 block">{{ $counts['resolved'] }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-lg font-bold">
                ✅
            </div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="p-5 rounded-2xl bg-admin-card border border-admin-border space-y-3">
        <form method="GET" action="{{ route('admin.feedbacks.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, telegram, or message..." class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2 text-white text-xs focus:outline-none focus:border-gold">
            </div>

            <div>
                <select name="status" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-gold">
                    <option value="">All Statuses</option>
                    <option value="NEW" {{ request('status') === 'NEW' ? 'selected' : '' }}>New / Pending</option>
                    <option value="REVIEWED" {{ request('status') === 'REVIEWED' ? 'selected' : '' }}>Reviewed</option>
                    <option value="RESOLVED" {{ request('status') === 'RESOLVED' ? 'selected' : '' }}>Resolved</option>
                </select>
            </div>

            <div>
                <select name="category" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-gold">
                    <option value="">All Categories</option>
                    <option value="FOOD" {{ request('category') === 'FOOD' ? 'selected' : '' }}>Food & Kitchen</option>
                    <option value="DRINKS" {{ request('category') === 'DRINKS' ? 'selected' : '' }}>Drinks & Bar</option>
                    <option value="ACOUSTIC" {{ request('category') === 'ACOUSTIC' ? 'selected' : '' }}>Live Acoustic</option>
                    <option value="SPORTS" {{ request('category') === 'SPORTS' ? 'selected' : '' }}>Sports Screen</option>
                    <option value="SERVICE" {{ request('category') === 'SERVICE' ? 'selected' : '' }}>Staff & Service</option>
                    <option value="GENERAL" {{ request('category') === 'GENERAL' ? 'selected' : '' }}>General</option>
                    <option value="SUGGESTION" {{ request('category') === 'SUGGESTION' ? 'selected' : '' }}>Suggestion</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-bold text-xs transition">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'status', 'category']))
                    <a href="{{ route('admin.feedbacks.index') }}" class="px-3 py-2 rounded-xl bg-admin-panel border border-admin-border text-zinc-300 hover:text-white text-xs font-semibold flex items-center justify-center">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- FEEDBACKS LIST -->
    <div class="rounded-2xl bg-admin-card border border-admin-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-admin-panel text-zinc-400 border-b border-admin-border">
                    <tr>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Patron / Channel</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Rating</th>
                        <th class="py-3 px-4">Message</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border/60">
                    @forelse($feedbacks as $fb)
                        <tr class="hover:bg-admin-panel/40 transition">
                            <td class="py-3.5 px-4 text-zinc-400 whitespace-nowrap">
                                <span class="font-medium text-white block">{{ $fb->created_at->format('M d, Y') }}</span>
                                <span class="text-[10px]">{{ $fb->created_at->format('g:i A') }}</span>
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-bold text-white">{{ $fb->name ?: 'Anonymous Patron' }}</div>
                                <div class="flex items-center space-x-2 text-[11px] text-zinc-400 mt-0.5">
                                    @if($fb->telegram_username)
                                        <span class="text-sky-400 font-medium">@ {{ $fb->telegram_username }}</span>
                                    @endif
                                    @if($fb->phone)
                                        <span>{{ $fb->phone }}</span>
                                    @endif
                                </div>
                                <span class="inline-block mt-1 px-1.5 py-0.5 text-[9px] rounded font-bold uppercase {{ $fb->source === 'BOT' ? 'bg-sky-950/60 text-sky-300 border border-sky-500/30' : 'bg-gold/15 text-gold border border-gold/30' }}">
                                    {{ $fb->source }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-lg bg-admin-panel border border-admin-border text-zinc-300 font-semibold text-[11px]">
                                    @switch($fb->category)
                                        @case('FOOD') 🍔 Food @break
                                        @case('DRINKS') 🍸 Drinks @break
                                        @case('ACOUSTIC') 🎸 Acoustic @break
                                        @case('SPORTS') ⚽ Sports @break
                                        @case('SERVICE') 🤵 Service @break
                                        @case('SUGGESTION') 💡 Suggestion @break
                                        @default 💬 General
                                    @endswitch
                                </span>
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap text-amber-400 font-bold">
                                @if($fb->rating)
                                    <span>{{ str_repeat('★', $fb->rating) }}</span>
                                    <span class="text-zinc-500 font-normal text-[11px]">({{ $fb->rating }}/5)</span>
                                @else
                                    <span class="text-zinc-500 font-normal">—</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 max-w-sm">
                                <p class="text-zinc-200 leading-relaxed text-xs line-clamp-3">
                                    {{ $fb->message }}
                                </p>
                                @if($fb->admin_notes)
                                    <div class="mt-1.5 p-1.5 rounded-lg bg-admin-panel border border-admin-border text-[11px] text-zinc-400">
                                        <span class="font-bold text-gold">Admin Note:</span> {{ $fb->admin_notes }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($fb->status === 'NEW')
                                    <span class="px-2.5 py-1 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-400 font-bold text-[10px] uppercase">
                                        ⏳ New
                                    </span>
                                @elseif($fb->status === 'REVIEWED')
                                    <span class="px-2.5 py-1 rounded-full bg-blue-500/20 border border-blue-500/40 text-blue-400 font-bold text-[10px] uppercase">
                                        👀 Reviewed
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 font-bold text-[10px] uppercase">
                                        ✅ Resolved
                                    </span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right whitespace-nowrap space-x-1.5">
                                <button type="button" @click="activeFeedback = {{ $fb->toJson() }}; showModal = true" class="px-2.5 py-1 rounded-lg bg-admin-panel hover:bg-gold hover:text-black border border-admin-border text-zinc-300 font-medium text-xs transition">
                                    Update
                                </button>
                                <form method="POST" action="{{ route('admin.feedbacks.destroy', $fb) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this feedback?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-zinc-500 hover:text-red-400 transition" title="Delete Feedback">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-zinc-400">
                                <span class="text-3xl block mb-2">💬</span>
                                <p class="font-bold text-white text-sm">No feedback found</p>
                                <p class="text-xs">Feedback received from the Mini App or Telegram Bot will appear here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($feedbacks->hasPages())
            <div class="p-4 border-t border-admin-border bg-admin-panel">
                {{ $feedbacks->links() }}
            </div>
        @endif
    </div>

    <!-- UPDATE FEEDBACK MODAL -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl" @click.away="showModal = false">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <h3 class="font-display font-bold text-lg text-white uppercase">Update Feedback Status</h3>
                <button type="button" @click="showModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <template x-if="activeFeedback">
                <form :action="'{{ url('admin/feedbacks') }}/' + activeFeedback.id + '/status'" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <span class="text-xs text-zinc-400 block mb-1">Patron Message</span>
                        <div class="p-3 rounded-xl bg-admin-panel border border-admin-border text-xs text-zinc-200" x-text="activeFeedback.message"></div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Status *</label>
                        <select name="status" x-model="activeFeedback.status" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-gold">
                            <option value="NEW">⏳ New / Pending</option>
                            <option value="REVIEWED">👀 Reviewed by Management</option>
                            <option value="RESOLVED">✅ Resolved / Action Taken</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 mb-1">Admin Internal Notes (Optional)</label>
                        <textarea name="admin_notes" x-model="activeFeedback.admin_notes" rows="3" placeholder="Add follow-up notes, kitchen action, or resolution details..." class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-gold"></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-2 pt-2 border-t border-admin-border">
                        <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl bg-admin-panel border border-admin-border text-zinc-300 text-xs font-semibold hover:text-white transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-bold text-xs transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
