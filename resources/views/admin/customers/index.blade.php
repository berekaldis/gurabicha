@extends('admin.layout', [
    'title' => 'Customer Info & Fan Club Alerts',
    'header' => 'Customer Registry & Matchday Alerts',
    'subHeader' => 'Manage registered patrons, supported football clubs, matchday alert preferences, and score notification broadcasts.'
])

@section('content')
<div class="space-y-6" x-data="{
    showCreateModal: false,
    showEditModal: false,
    showBroadcastModal: false,
    activeCustomer: null
}">

    <!-- HEADER & ACTION BUTTONS -->
    <div class="p-6 rounded-2xl bg-admin-card border border-admin-border flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-lg">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-gold animate-pulse"></span>
                <h3 class="font-display text-xl font-bold uppercase text-white tracking-wider">Fan Club & Patron Database</h3>
            </div>
            <p class="text-xs text-zinc-300 mt-1">Send matchday reminders, live score alerts, and acoustic night invitations to club supporters.</p>
        </div>
        <div class="flex items-center space-x-3 shrink-0">
            <button @click="showBroadcastModal = true" class="px-4 py-2.5 rounded-xl bg-admin-panel hover:bg-zinc-800 border border-gold/40 text-gold hover:text-white font-bold text-xs transition flex items-center space-x-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                <span>Broadcast Match Alert</span>
            </button>
            <button @click="showCreateModal = true" class="px-4 py-2.5 rounded-xl bg-gold hover:bg-gold-light text-black font-bold text-xs transition shadow-md shadow-gold/20 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Register Patron</span>
            </button>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="p-4 rounded-2xl bg-admin-card border border-admin-border shadow-md">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patron name, mobile (+251...) or email..." class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2 text-white focus:outline-none focus:border-gold">
            </div>

            <div>
                <select name="club" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    <option value="">All Supported Clubs</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club }}" {{ request('club') === $club ? 'selected' : '' }}>{{ $club }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="w-full py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-bold text-xs transition">
                    Search Fans
                </button>
                @if(request()->hasAny(['search', 'club']))
                    <a href="{{ route('admin.customers.index') }}" class="px-3 py-2 rounded-xl bg-admin-panel hover:bg-zinc-800 text-zinc-300 border border-admin-border text-xs flex items-center justify-center">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- CUSTOMERS TABLE -->
    <div class="rounded-2xl bg-admin-card border border-admin-border overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-admin-panel text-zinc-300 border-b border-admin-border font-semibold">
                    <tr>
                        <th class="py-3.5 px-4">Patron Name</th>
                        <th class="py-3.5 px-4">Phone / Mobile</th>
                        <th class="py-3.5 px-4">Supported Club</th>
                        <th class="py-3.5 px-4">Alert Preferences</th>
                        <th class="py-3.5 px-4">Notes</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border/60">
                    @forelse($customers as $c)
                        <tr class="hover:bg-admin-panel/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-gold/20 border border-gold/40 text-gold flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($c->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-semibold text-white block">{{ $c->name }}</span>
                                        <span class="text-[10px] text-zinc-400 font-mono">{{ $c->email ?: 'No email' }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-zinc-300 font-mono font-medium">
                                {{ $c->phone }}
                            </td>

                            <td class="py-3.5 px-4">
                                @if($c->favorite_club)
                                    <span class="px-2.5 py-1 rounded-md bg-zinc-800 border border-gold/40 text-gold font-bold text-[11px] tracking-wide inline-flex items-center space-x-1">
                                        <span>⚽</span>
                                        <span>{{ $c->favorite_club }}</span>
                                    </span>
                                @else
                                    <span class="text-zinc-500 text-[11px]">General Supporter</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="flex flex-wrap gap-1">
                                    @if($c->notify_matchday)
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 text-[9px] font-bold">Matchday</span>
                                    @endif
                                    @if($c->notify_scores)
                                        <span class="px-1.5 py-0.5 rounded bg-blue-950/60 border border-blue-500/40 text-blue-300 text-[9px] font-bold">Scores</span>
                                    @endif
                                    @if($c->notify_acoustic)
                                        <span class="px-1.5 py-0.5 rounded bg-amber-950/60 border border-amber-500/40 text-amber-300 text-[9px] font-bold">Acoustic</span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-zinc-400 text-[11px] max-w-xs truncate">
                                {{ $c->notes ?: '—' }}
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button @click="activeCustomer = {{ json_encode($c) }}; showEditModal = true" class="px-2.5 py-1 rounded bg-admin-panel hover:bg-gold text-zinc-300 hover:text-black font-semibold text-xs transition">
                                        Edit
                                    </button>

                                    <form method="POST" action="{{ route('admin.customers.destroy', $c) }}" onsubmit="return confirm('Remove patron {{ $c->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-zinc-500 hover:text-red-400 transition" title="Delete">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-zinc-500">No patron records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="p-4 border-t border-admin-border">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <!-- BROADCAST MATCH ALERT MODAL -->
    <div x-show="showBroadcastModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gold animate-ping"></span>
                    <h3 class="font-display text-lg font-bold uppercase text-white">Broadcast Match Alert</h3>
                </div>
                <button @click="showBroadcastModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.customers.alert') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Alert Target Event *</label>
                    <select name="type" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                        <option value="MATCHDAY">⚽ Matchday Reminder (Kickoff countdown & screening info)</option>
                        <option value="SCORE">🏆 Final Score Alert (Live match score update)</option>
                        <option value="ACOUSTIC">🎸 Saturday Acoustic Night Invitation</option>
                    </select>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Target Fan Club (Optional)</label>
                    <select name="club" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                        <option value="">All Supported Clubs (Broadcast to all subscribers)</option>
                        <option value="Arsenal">Arsenal Supporters</option>
                        <option value="Chelsea">Chelsea Supporters</option>
                        <option value="Liverpool">Liverpool Supporters</option>
                        <option value="Manchester United">Manchester United Supporters</option>
                        <option value="Manchester City">Manchester City Supporters</option>
                        <option value="Real Madrid">Real Madrid Supporters</option>
                        <option value="Barcelona">Barcelona Supporters</option>
                    </select>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Message Text *</label>
                    <textarea name="message" rows="3" required placeholder="Big Match Alert: Arsenal vs Chelsea kicks off today at 19:30 EAT! Catch it live on our 4K screens at Mexico Chamber of Commerce Bldg." class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showBroadcastModal = false" class="px-4 py-2 rounded-xl bg-admin-panel text-zinc-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-bold">Dispatch Notification</button>
                </div>
            </form>
        </div>
    </div>

    <!-- REGISTER PATRON MODAL -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <h3 class="font-display text-lg font-bold uppercase text-white">Register Patron Fan</h3>
                <button @click="showCreateModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.customers.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Full Name *</label>
                    <input type="text" name="name" placeholder="Abebe Kebede" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Phone Number (Mobile) *</label>
                    <input type="text" name="phone" placeholder="+251911234567" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Email Address (Optional)</label>
                    <input type="email" name="email" placeholder="abebe@example.com" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Favorite Football Club</label>
                    <input type="text" name="favorite_club" placeholder="Arsenal, Chelsea, Real Madrid..." class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div class="space-y-2 pt-1 border-t border-admin-border/50">
                    <span class="block text-zinc-300 font-semibold">Notification Preferences</span>
                    <label class="flex items-center space-x-2 text-zinc-300">
                        <input type="checkbox" name="notify_matchday" value="1" checked class="rounded border-admin-border text-gold bg-admin-panel">
                        <span>Send matchday kickoff reminders</span>
                    </label>
                    <label class="flex items-center space-x-2 text-zinc-300">
                        <input type="checkbox" name="notify_scores" value="1" checked class="rounded border-admin-border text-gold bg-admin-panel">
                        <span>Send live final score alerts</span>
                    </label>
                    <label class="flex items-center space-x-2 text-zinc-300">
                        <input type="checkbox" name="notify_acoustic" value="1" checked class="rounded border-admin-border text-gold bg-admin-panel">
                        <span>Send Saturday Acoustic music announcements</span>
                    </label>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Notes</label>
                    <textarea name="notes" rows="2" placeholder="VIP seating preference, regular guest..." class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl bg-admin-panel text-zinc-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-bold">Save Patron</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT PATRON MODAL -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <h3 class="font-display text-lg font-bold uppercase text-white">Edit Patron Info</h3>
                <button @click="showEditModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <form :action="'{{ url('admin/customers') }}/' + (activeCustomer ? activeCustomer.id : '')" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Full Name *</label>
                    <input type="text" name="name" :value="activeCustomer ? activeCustomer.name : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Phone Number (Mobile) *</label>
                    <input type="text" name="phone" :value="activeCustomer ? activeCustomer.phone : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Email Address</label>
                    <input type="email" name="email" :value="activeCustomer ? activeCustomer.email : ''" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Favorite Football Club</label>
                    <input type="text" name="favorite_club" :value="activeCustomer ? activeCustomer.favorite_club : ''" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                </div>

                <div class="space-y-2 pt-1 border-t border-admin-border/50">
                    <span class="block text-zinc-300 font-semibold">Notification Preferences</span>
                    <label class="flex items-center space-x-2 text-zinc-300">
                        <input type="checkbox" name="notify_matchday" value="1" :checked="activeCustomer && activeCustomer.notify_matchday" class="rounded border-admin-border text-gold bg-admin-panel">
                        <span>Send matchday kickoff reminders</span>
                    </label>
                    <label class="flex items-center space-x-2 text-zinc-300">
                        <input type="checkbox" name="notify_scores" value="1" :checked="activeCustomer && activeCustomer.notify_scores" class="rounded border-admin-border text-gold bg-admin-panel">
                        <span>Send live final score alerts</span>
                    </label>
                    <label class="flex items-center space-x-2 text-zinc-300">
                        <input type="checkbox" name="notify_acoustic" value="1" :checked="activeCustomer && activeCustomer.notify_acoustic" class="rounded border-admin-border text-gold bg-admin-panel">
                        <span>Send Saturday Acoustic music announcements</span>
                    </label>
                </div>

                <div>
                    <label class="block text-zinc-300 font-semibold mb-1">Notes</label>
                    <textarea name="notes" rows="2" :value="activeCustomer ? activeCustomer.notes : ''" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-admin-panel text-zinc-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-bold">Update Patron</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
