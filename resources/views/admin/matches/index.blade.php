@extends('admin.layout')

@section('title', 'Manage Football Matches')
@section('header_title', '2026 Fixtures & Scoreboard')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: false, showEditModal: false, activeMatch: null }">

    <!-- HEADER & FILTERS -->
    <div class="p-5 rounded-2xl bg-admin-card border border-admin-border space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-display text-xl font-bold uppercase text-white tracking-wider">Football Fixtures</h3>
                <p class="text-xs text-zinc-400">Tracked automatically from real-time internet feeds (ESPN Live Scoreboards). No manual entry required.</p>
            </div>
            <div class="flex items-center space-x-2">
                <form method="POST" action="{{ route('admin.matches.sync') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs transition shadow-md flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>🌐 Sync Live from Internet</span>
                    </button>
                </form>
                <button @click="showCreateModal = true" class="px-4 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-semibold text-xs transition shadow-md shadow-gold/20 flex items-center space-x-1.5 self-start sm:self-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add Custom Match</span>
                </button>
            </div>
        </div>

        <!-- AUTO INTERNET SYNC NOTICE -->
        <div class="p-3 rounded-xl bg-purple-950/30 border border-purple-700/40 text-xs text-purple-200 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="text-base">⚡</span>
                <span><strong>Internet Fixture Tracking Active:</strong> Premier League and Champions League fixtures, kickoff times, and scores update automatically directly from live internet feeds.</span>
            </div>
            <span class="px-2 py-0.5 rounded bg-purple-900/60 border border-purple-500/40 text-[10px] font-bold uppercase tracking-wider text-purple-200 shrink-0 hidden sm:inline">LIVE FEED</span>
        </div>

        <form method="GET" action="{{ route('admin.matches.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3 pt-2">
            <!-- Search -->
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search team (Arsenal, Real Madrid)..." class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2 text-white text-xs focus:outline-none focus:border-gold">
            </div>

            <!-- Competition -->
            <div>
                <select name="competition" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-gold">
                    <option value="">All Competitions</option>
                    <option value="Premier League" {{ request('competition') === 'Premier League' ? 'selected' : '' }}>Premier League</option>
                    <option value="Champions League" {{ request('competition') === 'Champions League' ? 'selected' : '' }}>Champions League</option>
                </select>
            </div>

            <!-- Month -->
            <div>
                <select name="month" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white text-xs focus:outline-none focus:border-gold">
                    <option value="">All Months</option>
                    @foreach(['8'=>'Aug (MW 1)', '9'=>'Sep', '10'=>'Oct', '11'=>'Nov', '12'=>'Dec', '1'=>'Jan', '2'=>'Feb', '3'=>'Mar', '4'=>'Apr', '5'=>'May'] as $mNum => $mName)
                        <option value="{{ $mNum }}" {{ request('month') == $mNum ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Submit -->
            <div class="flex space-x-2">
                <select name="status" class="w-1/2 bg-admin-panel border border-admin-border rounded-xl px-2 py-2 text-white text-xs focus:outline-none focus:border-gold">
                    <option value="">Status</option>
                    <option value="SCHEDULED" {{ request('status') === 'SCHEDULED' ? 'selected' : '' }}>Scheduled</option>
                    <option value="LIVE" {{ request('status') === 'LIVE' ? 'selected' : '' }}>Live</option>
                    <option value="FINISHED" {{ request('status') === 'FINISHED' ? 'selected' : '' }}>Finished</option>
                </select>
                <button type="submit" class="w-1/2 rounded-xl bg-admin-panel hover:bg-gold text-zinc-300 hover:text-black font-semibold text-xs border border-admin-border transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- MATCHES TABLE -->
    <div class="rounded-2xl bg-admin-card border border-admin-border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-admin-panel text-zinc-400 border-b border-admin-border">
                    <tr>
                        <th class="py-3 px-4">Kickoff (EAT)</th>
                        <th class="py-3 px-4">Competition</th>
                        <th class="py-3 px-4">Home vs Away</th>
                        <th class="py-3 px-4 text-center">Score</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Featured</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-admin-border/60">
                    @forelse($matches as $m)
                        <tr class="hover:bg-admin-panel/30 transition">
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-medium text-white block">
                                    {{ \Carbon\Carbon::parse($m->kickoff)->setTimezone('Africa/Addis_Ababa')->format('M d, Y') }}
                                </span>
                                <span class="text-gold font-mono text-[11px]">
                                    {{ \Carbon\Carbon::parse($m->kickoff)->setTimezone('Africa/Addis_Ababa')->format('H:i') }} EAT
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded font-bold text-[10px] {{ $m->competition === 'Premier League' ? 'bg-purple-900/60 text-purple-300' : 'bg-cyan-900/60 text-cyan-300' }}">
                                    {{ $m->competition }}
                                </span>
                                <span class="text-zinc-500 text-[10px] block mt-0.5 truncate max-w-[120px]">{{ $m->stage }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-white text-sm">
                                    {{ $m->homeTeam }} <span class="text-zinc-500 text-xs font-normal">vs</span> {{ $m->awayTeam }}
                                </span>
                                @if($m->venue)
                                    <span class="text-zinc-500 text-[11px] block truncate max-w-[180px]">{{ $m->venue }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($m->homeScore !== null && $m->awayScore !== null)
                                    <span class="font-display font-bold text-sm text-gold px-2 py-0.5 bg-admin-panel rounded border border-admin-border">
                                        {{ $m->homeScore }} : {{ $m->awayScore }}
                                    </span>
                                @else
                                    <span class="text-zinc-600">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $m->status === 'LIVE' ? 'bg-red-900/60 text-red-300 animate-pulse' : ($m->status === 'FINISHED' ? 'bg-zinc-800 text-zinc-300' : 'bg-blue-900/60 text-blue-300') }}">
                                    {{ $m->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <form method="POST" action="{{ route('admin.matches.toggle-featured', $m) }}">
                                    @csrf
                                    <button type="submit" class="text-xs transition {{ $m->featured ? 'text-gold font-bold' : 'text-zinc-500 hover:text-zinc-300' }}">
                                        {{ $m->featured ? '★ Featured' : '☆ Feature' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2 whitespace-nowrap">
                                <button @click="activeMatch = {{ json_encode($m) }}; showEditModal = true" class="text-zinc-400 hover:text-gold transition">
                                    Edit / Scores
                                </button>
                                <form method="POST" action="{{ route('admin.matches.destroy', $m) }}" class="inline" onsubmit="return confirm('Delete this match?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-zinc-500 hover:text-red-400">✕</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-zinc-500">
                                No fixtures found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($matches->hasPages())
            <div class="p-4 border-t border-admin-border bg-admin-panel/40">
                {{ $matches->links() }}
            </div>
        @endif
    </div>

    <!-- CREATE MATCH MODAL -->
    <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-lg w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <h3 class="font-display text-lg font-bold uppercase text-white">Add Football Match</h3>
                <button @click="showCreateModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.matches.store') }}" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Home Team *</label>
                        <input type="text" name="homeTeam" placeholder="Arsenal" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Away Team *</label>
                        <input type="text" name="awayTeam" placeholder="Chelsea" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Competition *</label>
                        <select name="competition" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                            <option value="Premier League">Premier League</option>
                            <option value="Champions League">Champions League</option>
                            <option value="FA Cup">FA Cup</option>
                            <option value="World Cup">World Cup</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Stage / Matchweek</label>
                        <input type="text" name="stage" placeholder="Matchweek 28, Semi-final" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Kickoff (UTC Date & Time) *</label>
                        <input type="datetime-local" name="kickoff" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Venue Stadium</label>
                        <input type="text" name="venue" placeholder="Emirates Stadium" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Status *</label>
                        <select name="status" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                            <option value="SCHEDULED">SCHEDULED</option>
                            <option value="LIVE">LIVE</option>
                            <option value="FINISHED">FINISHED</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Home Score</label>
                        <input type="number" name="homeScore" min="0" placeholder="0" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Away Score</label>
                        <input type="number" name="awayScore" min="0" placeholder="0" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" id="match_featured" name="featured" value="1" class="rounded border-admin-border text-gold focus:ring-gold bg-admin-panel">
                    <label for="match_featured" class="text-zinc-300 font-medium">Feature on homepage match spotlight</label>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl bg-admin-panel text-zinc-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-semibold">Save Match</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MATCH MODAL -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div class="bg-admin-card border border-admin-border rounded-2xl max-w-lg w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-admin-border pb-3">
                <h3 class="font-display text-lg font-bold uppercase text-white">Edit Match & Scores</h3>
                <button @click="showEditModal = false" class="text-zinc-400 hover:text-white">✕</button>
            </div>

            <form :action="'{{ url('admin/matches') }}/' + (activeMatch ? activeMatch.id : '')" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Home Team *</label>
                        <input type="text" name="homeTeam" :value="activeMatch ? activeMatch.homeTeam : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Away Team *</label>
                        <input type="text" name="awayTeam" :value="activeMatch ? activeMatch.awayTeam : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Competition *</label>
                        <input type="text" name="competition" :value="activeMatch ? activeMatch.competition : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Stage</label>
                        <input type="text" name="stage" :value="activeMatch ? activeMatch.stage : ''" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Kickoff (Date & Time) *</label>
                        <input type="datetime-local" name="kickoff" :value="activeMatch ? activeMatch.kickoff.slice(0, 16) : ''" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Venue Stadium</label>
                        <input type="text" name="venue" :value="activeMatch ? activeMatch.venue : ''" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Status *</label>
                        <select name="status" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                            <option value="SCHEDULED" :selected="activeMatch && activeMatch.status === 'SCHEDULED'">SCHEDULED</option>
                            <option value="LIVE" :selected="activeMatch && activeMatch.status === 'LIVE'">LIVE</option>
                            <option value="FINISHED" :selected="activeMatch && activeMatch.status === 'FINISHED'">FINISHED</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Home Score</label>
                        <input type="number" name="homeScore" min="0" :value="activeMatch ? activeMatch.homeScore : ''" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                    <div>
                        <label class="block text-zinc-300 font-semibold mb-1">Away Score</label>
                        <input type="number" name="awayScore" min="0" :value="activeMatch ? activeMatch.awayScore : ''" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-white focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <input type="checkbox" id="edit_m_featured" name="featured" value="1" :checked="activeMatch && activeMatch.featured" class="rounded border-admin-border text-gold focus:ring-gold bg-admin-panel">
                    <label for="edit_m_featured" class="text-zinc-300 font-medium">Feature on homepage match spotlight</label>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-admin-panel text-zinc-300 hover:text-white">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-semibold">Update Match</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
