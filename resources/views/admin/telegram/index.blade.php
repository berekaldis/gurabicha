@extends('admin.layout', [
    'title' => 'Telegram Bot Configuration & Broadcast Announcements',
    'header' => 'Telegram Config & Announcements',
    'subHeader' => 'Manage Telegram Bot credentials, Mini App integration, and broadcast real-time announcements to Telegram users.'
])

@section('content')
<div class="space-y-8" x-data="{
    activeTab: 'broadcast',
    copiedAppUrl: false,
    copiedWebhookUrl: false,
    copiedToken: false,
    showToken: false,
    
    // Broadcast Form Data
    broadcastCategory: 'FIXTURE',
    broadcastAudience: 'ALL',
    broadcastTitle: '⚽ Premier League Live Tonight at Gura Bicha!',
    broadcastMessage: 'Big match alert! Join us tonight for live 4K screening with stadium sound, cold draft beers and fresh Ethiopian platters.\n\nKickoff: 8:00 PM (Addis Ababa EAT)\nWalk-ins welcome in the sports arena!',
    actionButtonType: 'FIXTURES',
    customButtonText: '',
    customButtonUrl: '',

    // Quick Presets
    applyPreset(type) {
        this.broadcastCategory = type;
        if (type === 'FIXTURE') {
            @if($upcomingMatches->isNotEmpty())
                @php $nextM = $upcomingMatches->first(); @endphp
                this.broadcastTitle = '⚽ LIVE MATCH: {{ $nextM->homeTeam }} vs {{ $nextM->awayTeam }}';
                this.broadcastMessage = '⚡ Catch the biggest clash on our 4K arena screens!\n\n🏆 Competition: {{ $nextM->competition }}\n⏰ Kickoff: {{ $nextM->kickoff_eat }}\n📍 Venue: Gura Bicha, Chamber of Commerce Building, Mexico\n\nWalk-in seating is open. Gather your crew!';
            @else
                this.broadcastTitle = '⚽ Big Matchday Screening at Gura Bicha!';
                this.broadcastMessage = 'Watch live football tonight on massive 4K projection screens with stadium audio. Draft beer discounts & special kitfo platters all night!';
            @endif
            this.actionButtonType = 'FIXTURES';
        } else if (type === 'EVENT') {
            this.broadcastCategory = 'EVENT';
            this.broadcastTitle = '🎸 Saturday Acoustic Groove Night (7:00 PM – 11:30 PM)';
            this.broadcastMessage = 'Unwind this Saturday with intimate acoustic soul, vocal jazz & Ethio-groove by our resident musicians.\n\n✨ Candlelit VIP lounge tables\n🍸 Signature cocktails & prime Ethiopian steaks\n\nReserve your front-row table early before seats sell out!';
            this.actionButtonType = 'RESERVE';
        } else if (type === 'MENU') {
            this.broadcastCategory = 'MENU';
            this.broadcastTitle = '🍽️ Matchday Chef Special: Kitfo & Draft Combo';
            this.broadcastMessage = 'Feast like a champion during the matches!\n\n• Special Kitfo with spiced butter & ayib\n• Chilled St. George / Habesha Draft Pitchers\n• Crispy Ethiopian Sambusas with Awaze\n\nBrowse full menu and prices right inside Telegram!';
            this.actionButtonType = 'MENU';
        } else {
            this.broadcastCategory = 'GENERAL';
            this.broadcastTitle = '📢 Gura Bicha Lounge Announcement';
            this.broadcastMessage = 'Greetings from Gura Bicha Sports Bar & Acoustic Lounge! Check out our latest schedule, upcoming weekend events, and venue highlights in the Mini App.';
            this.actionButtonType = 'MINIAPP';
        }
    }
}">

    <!-- TOP KPI STATS SUMMARY -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Total Subscribers -->
        <div class="bg-admin-card border border-admin-border rounded-2xl p-4 shadow-lg flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Telegram Users</span>
            <div class="flex items-baseline space-x-1.5 mt-2">
                <span class="text-2xl font-black text-white">{{ $stats['total_subscribers'] }}</span>
                <span class="text-xs text-sky-400 font-semibold">patrons</span>
            </div>
            <span class="text-[10px] text-zinc-500 mt-1">Active bot subscribers</span>
        </div>

        <!-- Arsenal Fans -->
        <div class="bg-admin-card border border-admin-border rounded-2xl p-4 shadow-lg flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-red-400">🔴 Arsenal Fans</span>
            <div class="flex items-baseline space-x-1.5 mt-2">
                <span class="text-2xl font-black text-white">{{ $stats['arsenal_subscribers'] }}</span>
                <span class="text-xs text-zinc-400">fans</span>
            </div>
            <span class="text-[10px] text-zinc-500 mt-1">Targeted alerts ready</span>
        </div>

        <!-- Man Utd Fans -->
        <div class="bg-admin-card border border-admin-border rounded-2xl p-4 shadow-lg flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-500">🟡 Man United</span>
            <div class="flex items-baseline space-x-1.5 mt-2">
                <span class="text-2xl font-black text-white">{{ $stats['manutd_subscribers'] }}</span>
                <span class="text-xs text-zinc-400">fans</span>
            </div>
            <span class="text-[10px] text-zinc-500 mt-1">Targeted alerts ready</span>
        </div>

        <!-- Chelsea Fans -->
        <div class="bg-admin-card border border-admin-border rounded-2xl p-4 shadow-lg flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-400">🔵 Chelsea Fans</span>
            <div class="flex items-baseline space-x-1.5 mt-2">
                <span class="text-2xl font-black text-white">{{ $stats['chelsea_subscribers'] }}</span>
                <span class="text-xs text-zinc-400">fans</span>
            </div>
            <span class="text-[10px] text-zinc-500 mt-1">Targeted alerts ready</span>
        </div>

        <!-- Acoustic Lovers -->
        <div class="bg-admin-card border border-admin-border rounded-2xl p-4 shadow-lg flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">🎸 Acoustic Fans</span>
            <div class="flex items-baseline space-x-1.5 mt-2">
                <span class="text-2xl font-black text-white">{{ $stats['acoustic_subscribers'] }}</span>
                <span class="text-xs text-zinc-400">lovers</span>
            </div>
            <span class="text-[10px] text-zinc-500 mt-1">Saturday VIP patrons</span>
        </div>

        <!-- Bot Status -->
        <div class="bg-admin-card border border-admin-border rounded-2xl p-4 shadow-lg flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Bot Connection</span>
            <div class="mt-2">
                <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-full text-[11px] font-bold {{ $botInfo['is_configured'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-amber-500/20 text-amber-400 border border-amber-500/40' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $botInfo['is_configured'] ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400' }}"></span>
                    <span>{{ $botInfo['is_configured'] ? 'LIVE TOKEN' : 'DEV MOCK' }}</span>
                </span>
            </div>
            <span class="text-[10px] font-mono text-zinc-500 mt-1 truncate">@{{ $botInfo['bot_username'] }}</span>
        </div>
    </div>

    <!-- MAIN TWO-COLUMN WORKSPACE: BROADCAST CENTER & BOT CONFIGURATION -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">

        <!-- ======================================================== -->
        <!-- LEFT COLUMN: BROADCAST ANNOUNCEMENT DISPATCHER (7 COLS)  -->
        <!-- ======================================================== -->
        <div class="xl:col-span-7 space-y-6">
            
            <!-- BROADCAST DISPATCH CARD -->
            <div class="bg-admin-card border border-admin-border rounded-2xl p-6 shadow-xl relative overflow-hidden">
                <div class="flex items-center justify-between pb-4 border-b border-admin-border mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/30 flex items-center justify-center text-sky-400 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white tracking-wide">Telegram Broadcast Announcements</h2>
                            <p class="text-xs text-zinc-400">Push real-time alerts for live match kickoffs, acoustic groove nights & specials</p>
                        </div>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-sky-500/20 text-sky-300 border border-sky-500/30 hidden sm:inline">
                        Instant Delivery
                    </span>
                </div>

                <!-- 1-CLICK QUICK PRESETS TOOLBAR -->
                <div class="mb-5">
                    <label class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-2">
                        1-Click Announcement Presets:
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button type="button" @click="applyPreset('FIXTURE')"
                                :class="broadcastCategory === 'FIXTURE' ? 'bg-sky-500/20 text-sky-300 border-sky-500/60 font-bold' : 'bg-admin-panel text-zinc-300 border-admin-border hover:bg-zinc-800'"
                                class="px-3 py-2 rounded-xl border text-xs text-left transition flex items-center space-x-1.5">
                            <span>⚽</span>
                            <span class="truncate">Live Match</span>
                        </button>

                        <button type="button" @click="applyPreset('EVENT')"
                                :class="broadcastCategory === 'EVENT' ? 'bg-amber-500/20 text-amber-300 border-amber-500/60 font-bold' : 'bg-admin-panel text-zinc-300 border-admin-border hover:bg-zinc-800'"
                                class="px-3 py-2 rounded-xl border text-xs text-left transition flex items-center space-x-1.5">
                            <span>🎸</span>
                            <span class="truncate">Saturday Acoustic</span>
                        </button>

                        <button type="button" @click="applyPreset('MENU')"
                                :class="broadcastCategory === 'MENU' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/60 font-bold' : 'bg-admin-panel text-zinc-300 border-admin-border hover:bg-zinc-800'"
                                class="px-3 py-2 rounded-xl border text-xs text-left transition flex items-center space-x-1.5">
                            <span>🍽️</span>
                            <span class="truncate">Food & Drink</span>
                        </button>

                        <button type="button" @click="applyPreset('GENERAL')"
                                :class="broadcastCategory === 'GENERAL' ? 'bg-purple-500/20 text-purple-300 border-purple-500/60 font-bold' : 'bg-admin-panel text-zinc-300 border-admin-border hover:bg-zinc-800'"
                                class="px-3 py-2 rounded-xl border text-xs text-left transition flex items-center space-x-1.5">
                            <span>📢</span>
                            <span class="truncate">Custom News</span>
                        </button>
                    </div>
                </div>

                <!-- BROADCAST COMPOSE FORM -->
                <form method="POST" action="{{ route('admin.telegram.broadcast') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="category" :value="broadcastCategory">

                    <!-- Audience & Category Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Target Audience</label>
                            <select name="target_audience" x-model="broadcastAudience"
                                    class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-gold">
                                <option value="ALL">📢 All Telegram Subscribers ({{ $stats['total_subscribers'] }})</option>
                                <option value="Arsenal">🔴 Arsenal Fans Only ({{ $stats['arsenal_subscribers'] }})</option>
                                <option value="Manchester United">🟡 Manchester United Fans ({{ $stats['manutd_subscribers'] }})</option>
                                <option value="Chelsea">🔵 Chelsea Fans ({{ $stats['chelsea_subscribers'] }})</option>
                                <option value="Liverpool">🔴 Liverpool Fans ({{ $stats['liverpool_subscribers'] }})</option>
                                <option value="ACOUSTIC">🎸 Saturday Acoustic Lovers ({{ $stats['acoustic_subscribers'] }})</option>
                            </select>
                            <p class="text-[10px] text-zinc-500 mt-1">Filtered by fan club profile registered in Telegram or website.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Interactive Button</label>
                            <select name="action_button_type" x-model="actionButtonType"
                                    class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-gold">
                                <option value="FIXTURES">⚽ Open Live Match Center (Mini App)</option>
                                <option value="RESERVE">🎸 Book Saturday Acoustic Table</option>
                                <option value="MENU">🍽️ Open Live Food & Drink Menu</option>
                                <option value="MINIAPP">🚀 Launch General Mini App</option>
                                <option value="CUSTOM">🔗 Custom External Link...</option>
                                <option value="NONE">❌ No Button (Text Only)</option>
                            </select>
                            <p class="text-[10px] text-zinc-500 mt-1">Appends interactive buttons below message in Telegram chat.</p>
                        </div>
                    </div>

                    <!-- Custom Button Fields (if CUSTOM chosen) -->
                    <div x-show="actionButtonType === 'CUSTOM'" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-4 p-3 rounded-xl bg-admin-panel border border-admin-border">
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-300 uppercase mb-1">Button Label</label>
                            <input type="text" name="custom_button_text" x-model="customButtonText" placeholder="e.g. Visit Our Website"
                                   class="w-full bg-admin-card border border-admin-border rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-gold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-zinc-300 uppercase mb-1">Destination URL</label>
                            <input type="url" name="custom_button_url" x-model="customButtonUrl" placeholder="https://example.com"
                                   class="w-full bg-admin-card border border-admin-border rounded-lg px-3 py-1.5 text-xs text-white focus:outline-none focus:border-gold">
                        </div>
                    </div>

                    <!-- Announcement Title -->
                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Announcement Title</label>
                        <input type="text" name="title" x-model="broadcastTitle" required
                               class="w-full bg-admin-panel border border-admin-border rounded-xl px-4 py-2.5 text-sm text-white font-medium focus:outline-none focus:border-gold">
                    </div>

                    <!-- Message Body -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider">Message Content</label>
                            <span class="text-[10px] text-zinc-500">Supports emojis & Telegram line breaks</span>
                        </div>
                        <textarea name="message" x-model="broadcastMessage" rows="5" required
                                  class="w-full bg-admin-panel border border-admin-border rounded-xl p-4 text-xs text-zinc-200 leading-relaxed font-sans focus:outline-none focus:border-gold"></textarea>
                    </div>

                    <!-- ACTION FOOTER & SUBMIT BUTTON -->
                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center space-x-2 text-xs text-zinc-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span>Targeting: <strong class="text-white" x-text="broadcastAudience"></strong></span>
                        </div>

                        <button type="submit"
                                onclick="return confirm('Dispatch this announcement to Telegram subscribers?')"
                                class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs uppercase tracking-wider transition shadow-lg shadow-sky-600/30 flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18.716-.967 4.144-1.365 5.836-.168.716-.434.956-.69 0.98-.557.051-.98-.369-1.52-.723-.845-.554-1.323-.899-2.143-1.44-.948-.624-.333-.967.207-1.528.141-.147 2.598-2.383 2.645-2.586.006-.025.011-.122-.047-.174-.058-.052-.144-.034-.206-.02-.088.02-1.488.948-4.202 2.781-.397.272-.757.406-1.079.399-.356-.008-1.041-.202-1.551-.368-.625-.203-1.121-.311-1.078-.656.022-.18.271-.364.747-.553 2.923-1.274 4.873-2.115 5.85-2.524 2.785-1.163 3.364-1.365 3.743-1.371.083-.001.27.02.39.119.102.083.13.195.143.274.013.08.029.262.016.406z"/></svg>
                            <span>Send Telegram Broadcast</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- LIVE TELEGRAM CHAT PREVIEW -->
            <div class="bg-admin-card border border-admin-border rounded-2xl p-5 shadow-xl space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-admin-border">
                    <span class="text-xs font-bold text-zinc-400 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>📱</span>
                        <span>Live Telegram User Preview</span>
                    </span>
                    <span class="text-[10px] text-zinc-500 font-mono">Chat view as seen on Telegram phone screen</span>
                </div>

                <!-- Simulated Telegram Chat Screen Bubble -->
                <div class="bg-[#0f141c] p-4 rounded-2xl border border-sky-950/80 shadow-inner max-w-lg mx-auto">
                    <!-- Telegram Message Bubble -->
                    <div class="bg-[#1e293b] rounded-2xl p-4 text-xs text-white shadow space-y-2.5 border border-slate-700/50">
                        <div class="flex items-center space-x-2 border-b border-slate-700/40 pb-2">
                            <img src="{{ asset('images/logo.png') }}" alt="GB Logo" class="w-6 h-6 rounded-full object-contain border border-gold/40 bg-black">
                            <div>
                                <span class="font-bold text-sky-400 text-xs">Gura Bicha Bot</span>
                                <span class="text-[9px] text-zinc-400 ml-1">✓ bot</span>
                            </div>
                        </div>

                        <!-- Header Emoji & Category -->
                        <div class="text-[11px] font-bold"
                             :class="{
                                'text-sky-300': broadcastCategory === 'FIXTURE',
                                'text-amber-300': broadcastCategory === 'EVENT',
                                'text-emerald-300': broadcastCategory === 'MENU',
                                'text-purple-300': broadcastCategory === 'GENERAL'
                             }">
                            <span x-text="broadcastCategory === 'FIXTURE' ? '⚽ MATCHDAY ANNOUNCEMENT' : (broadcastCategory === 'EVENT' ? '🎸 SATURDAY ACOUSTIC NIGHT' : (broadcastCategory === 'MENU' ? '🍽️ CHEF & BAR SPECIAL' : '📢 GURA BICHA ANNOUNCEMENT'))"></span>
                        </div>

                        <!-- Title -->
                        <div class="font-bold text-sm text-white" x-text="broadcastTitle || 'Announcement Title'"></div>

                        <!-- Body -->
                        <div class="text-[11px] text-zinc-300 whitespace-pre-line leading-relaxed" x-text="broadcastMessage || 'Message text here...'"></div>

                        <!-- Footer -->
                        <div class="pt-1 text-[9px] text-zinc-500 italic border-t border-slate-700/30">
                            📍 Gura Bicha • Mexico, Chamber of Commerce Building, Addis Ababa
                        </div>
                    </div>

                    <!-- Simulated Telegram Inline Buttons -->
                    <div x-show="actionButtonType !== 'NONE'" class="mt-2 space-y-1.5" x-cloak>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="bg-sky-600/30 hover:bg-sky-600/40 border border-sky-500/40 rounded-xl py-2 text-center text-xs font-bold text-sky-300 cursor-default">
                                <span x-text="actionButtonType === 'FIXTURES' ? '⚽ Open Match Center' : (actionButtonType === 'RESERVE' ? '🎸 Book Table' : (actionButtonType === 'MENU' ? '🍽️ View Menu' : (actionButtonType === 'CUSTOM' ? (customButtonText || 'Button Link') : '🚀 Launch Mini App')))"></span>
                            </div>
                            <div class="bg-zinc-800/60 border border-zinc-700/40 rounded-xl py-2 text-center text-xs font-medium text-zinc-400 cursor-default">
                                <span>🌐 Web Browser</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PAST BROADCAST HISTORY TABLE -->
            <div class="bg-admin-card border border-admin-border rounded-2xl p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-admin-border">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider">Broadcast Dispatch History</h3>
                    <span class="text-xs text-zinc-400">{{ $broadcasts->total() }} total announcements</span>
                </div>

                @if($broadcasts->isEmpty())
                    <div class="text-center py-8 text-zinc-500 text-xs">
                        No previous broadcasts sent yet. Use the composer above to send your first Telegram announcement!
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-zinc-400 border-b border-admin-border">
                                    <th class="py-2.5 px-3">Date</th>
                                    <th class="py-2.5 px-3">Category</th>
                                    <th class="py-2.5 px-3">Title & Audience</th>
                                    <th class="py-2.5 px-3">Recipients</th>
                                    <th class="py-2.5 px-3 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-admin-border/50 text-zinc-300">
                                @foreach($broadcasts as $bc)
                                <tr>
                                    <td class="py-3 px-3 text-zinc-400 whitespace-nowrap">
                                        {{ $bc->created_at->format('M d, H:i') }}
                                    </td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                            @if($bc->category === 'FIXTURE') bg-sky-500/20 text-sky-300
                                            @elseif($bc->category === 'EVENT') bg-amber-500/20 text-amber-300
                                            @elseif($bc->category === 'MENU') bg-emerald-500/20 text-emerald-300
                                            @else bg-purple-500/20 text-purple-300 @endif">
                                            {{ $bc->category }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3">
                                        <strong class="text-white block">{{ $bc->title }}</strong>
                                        <span class="text-[10px] text-zinc-500">Audience: {{ $bc->target_audience }}</span>
                                    </td>
                                    <td class="py-3 px-3 font-mono font-bold text-sky-400">
                                        {{ $bc->recipients_count }} patrons
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                            {{ $bc->status }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pt-2">
                        {{ $broadcasts->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- ======================================================== -->
        <!-- RIGHT COLUMN: TELEGRAM CONFIGURATION & WEBHOOK (5 COLS)  -->
        <!-- ======================================================== -->
        <div class="xl:col-span-5 space-y-6">

            <!-- TELEGRAM CREDENTIALS CARD -->
            <div class="bg-admin-card border border-admin-border rounded-2xl p-6 shadow-xl space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-admin-border">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-sky-900/40 border border-sky-500/40 flex items-center justify-center text-sky-300 text-xl shadow-inner">
                            🤖
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-white tracking-wide">Telegram Bot Credentials</h2>
                            <p class="text-xs text-zinc-400">Configure your official Telegram Bot and Mini App endpoint</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $botInfo['is_configured'] ? 'bg-emerald-950 text-emerald-300 border border-emerald-600/40' : 'bg-amber-950 text-amber-300 border border-amber-600/40' }}">
                        {{ $botInfo['is_configured'] ? 'TOKEN ACTIVE' : 'DEV MOCK' }}
                    </span>
                </div>

                <!-- EDITABLE CREDENTIALS FORM -->
                <style>
                    .gb-token-masked {
                        -webkit-text-security: disc !important;
                        text-security: disc !important;
                    }
                </style>
                <form method="POST" action="{{ route('admin.telegram.settings.update') }}" class="space-y-4" autocomplete="off">
                    @csrf

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider">
                                Telegram Bot Token
                            </label>
                            <span class="text-[10px] font-mono text-zinc-500" x-text="showToken ? 'VISIBLE' : 'HIDDEN'">HIDDEN</span>
                        </div>
                        <div class="relative flex items-center">
                            <input type="text"
                                :class="{ 'gb-token-masked': !showToken }"
                                name="telegram_bot_token"
                                id="telegram_bot_token"
                                autocomplete="off"
                                autocorrect="off"
                                autocapitalize="off"
                                spellcheck="false"
                                data-lpignore="true"
                                data-1p-ignore="true"
                                value="{{ old('telegram_bot_token', $settings['telegram_bot_token']) }}"
                                placeholder="7123456789:AAFlkB4... (from @BotFather)"
                                class="w-full bg-admin-panel border border-admin-border rounded-xl pl-3.5 pr-20 py-2.5 text-xs font-mono text-gold focus:outline-none focus:border-gold">
                            <button type="button"
                                @click="showToken = !showToken"
                                class="absolute right-2 px-2.5 py-1 rounded-lg bg-admin-card hover:bg-zinc-800 border border-admin-border text-zinc-300 hover:text-white text-xs font-medium transition flex items-center space-x-1.5 select-none"
                                :title="showToken ? 'Hide Bot Token' : 'Show Bot Token'">
                                <svg x-show="!showToken" class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showToken" class="w-3.5 h-3.5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                                <span x-text="showToken ? 'Hide' : 'Show'">Show</span>
                            </button>
                        </div>
                        <p class="text-[10px] text-zinc-500 mt-1">Generated by Telegram <code class="text-zinc-400">@BotFather</code>. Can also be set in <code class="text-zinc-400">.env</code>.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">
                            Bot Username
                        </label>
                        <div class="flex items-center">
                            <span class="bg-admin-panel border border-r-0 border-admin-border rounded-l-xl px-3.5 py-2.5 text-xs text-zinc-500 font-mono">@</span>
                            <input type="text" name="telegram_bot_username" value="{{ old('telegram_bot_username', $settings['telegram_bot_username']) }}"
                                placeholder="GuraBichaAddisBot"
                                class="w-full bg-admin-panel border border-admin-border rounded-r-xl px-3.5 py-2.5 text-xs font-mono text-white focus:outline-none focus:border-gold">
                        </div>
                        <p class="text-[10px] text-zinc-500 mt-1">Registered username in @BotFather (e.g. <code class="text-zinc-400">GuraBichaAddisBot</code>).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">
                            Telegram WebApp Launch URL
                        </label>
                        <div class="flex items-center space-x-2">
                            <input type="url" name="telegram_webapp_url" value="{{ old('telegram_webapp_url', $settings['telegram_webapp_url']) }}"
                                placeholder="{{ url('/') }}"
                                class="w-full bg-admin-panel border border-admin-border rounded-xl px-3.5 py-2.5 text-xs font-mono text-zinc-300 focus:outline-none focus:border-gold">
                            <button type="button" @click="navigator.clipboard.writeText('{{ $settings['telegram_webapp_url'] }}'); copiedAppUrl = true; setTimeout(() => copiedAppUrl = false, 2000)" class="px-3.5 py-2.5 rounded-xl bg-admin-panel hover:bg-zinc-800 border border-admin-border text-zinc-300 text-xs font-semibold transition shrink-0">
                                <span x-text="copiedAppUrl ? 'Copied!' : 'Copy'">Copy</span>
                            </button>
                        </div>
                        <p class="text-[10px] text-zinc-500 mt-1">Must be HTTPS when deployed live for Telegram Mini App.</p>
                    </div>

                    <!-- ADMIN TELEGRAM CHAT ID (RECEIVE RESERVATIONS & FEEDBACK) -->
                    <div class="pt-2 border-t border-admin-border">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-gold uppercase tracking-wider flex items-center space-x-1.5">
                                <span>🔔</span>
                                <span>Admin Telegram Chat ID</span>
                            </label>
                            @if(!empty($settings['telegram_admin_chat_id']))
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    ✓ Active ID: {{ $settings['telegram_admin_chat_id'] }}
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30 animate-pulse">
                                    ⚠️ Not Configured
                                </span>
                            @endif
                        </div>
                        <div class="relative flex items-center">
                            <input type="text"
                                name="telegram_admin_chat_id"
                                id="telegram_admin_chat_id"
                                value="{{ old('telegram_admin_chat_id', $settings['telegram_admin_chat_id']) }}"
                                placeholder="e.g. 123456789"
                                class="w-full bg-admin-panel border border-gold/40 rounded-xl px-3.5 py-2.5 text-xs font-mono text-gold focus:outline-none focus:border-gold">
                        </div>
                        <p class="text-[10px] text-zinc-400 mt-1.5 leading-relaxed">
                            The designated Telegram Chat ID where Gura Bicha Admin receives instant notifications when customers <b>reserve a table (Pending)</b>, <b>cancel a booking</b>, or submit <b>patron feedback/suggestions</b>.
                        </p>

                        <!-- Guide on how to get Chat ID -->
                        <div class="mt-2.5 p-3 rounded-xl bg-admin-panel/80 border border-admin-border text-[11px] text-zinc-300 space-y-1">
                            <div class="font-bold text-white flex items-center space-x-1">
                                <span>💡</span>
                                <span>How to find your Telegram Chat ID:</span>
                            </div>
                            <ol class="list-decimal list-inside space-y-0.5 text-zinc-400 text-[10px]">
                                <li>Open Telegram and start <a href="https://t.me/userinfobot" target="_blank" class="text-sky-400 underline font-mono">@userinfobot</a> — it immediately replies with your numeric <b>Id</b>.</li>
                                <li>Or send <code class="text-gold">/start</code> to your venue bot <a href="https://t.me/{{ $settings['telegram_bot_username'] }}" target="_blank" class="text-sky-400 underline font-mono">@{{ $settings['telegram_bot_username'] }}</a>.</li>
                                <li>Copy that numeric ID into the field above and click <b>Save Credentials</b>.</li>
                            </ol>
                        </div>

                        <!-- Quick Select from Recent Subscribers -->
                        @if($subscribers->count() > 0)
                            <div class="mt-2.5 pt-2 border-t border-admin-border/60">
                                <span class="text-[10px] text-zinc-400 block mb-1 font-semibold uppercase tracking-wider">Quick Fill from Recent Subscribers:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($subscribers->take(4) as $sub)
                                        <button type="button"
                                                onclick="document.getElementById('telegram_admin_chat_id').value = '{{ $sub->chat_id }}'"
                                                class="px-2.5 py-1 rounded-lg bg-zinc-800/80 hover:bg-zinc-700 border border-admin-border text-[10px] text-zinc-300 hover:text-white transition flex items-center space-x-1">
                                            <span>👤 {{ $sub->first_name ?? $sub->username ?? 'User' }}</span>
                                            <span class="font-mono text-gold">({{ $sub->chat_id }})</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="pt-3 flex items-center justify-between border-t border-admin-border">
                        <div class="flex items-center space-x-3">
                            <a href="https://t.me/{{ $settings['telegram_bot_username'] }}" target="_blank" class="text-xs text-sky-400 hover:text-sky-300 font-semibold flex items-center space-x-1">
                                <span>Open Bot in Telegram</span>
                                <span>↗</span>
                            </a>
                        </div>

                        <div class="flex items-center space-x-2">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs uppercase tracking-wider transition shadow-md shadow-sky-600/30">
                                Save Credentials & Admin ID
                            </button>
                        </div>
                    </div>
                </form>

                <!-- TEST NOTIFICATION FORM (IF ADMIN CHAT ID SET) -->
                @if(!empty($settings['telegram_admin_chat_id']))
                    <div class="pt-3 border-t border-admin-border flex items-center justify-between">
                        <div class="text-[11px] text-zinc-400">
                            Verify that your phone receives alerts:
                        </div>
                        <form method="POST" action="{{ route('telegram.test-notification') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition flex items-center space-x-1.5 shadow-md shadow-emerald-600/20">
                                <span>🔔</span>
                                <span>Send Test Notification to Admin</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- WEBHOOK DIAGNOSTICS & MANAGEMENT -->
            <div class="bg-admin-card border border-admin-border rounded-2xl p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-admin-border">
                    <div class="flex items-center space-x-2">
                        <span class="text-base">⚡</span>
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">Telegram Webhook Diagnostics</h3>
                    </div>
                    <a href="{{ url('/api/telegram/info') }}" target="_blank" class="text-[11px] text-gold hover:underline">
                        Raw Diagnostics JSON ↗
                    </a>
                </div>

                <!-- LIVE TELEGRAM WEBHOOK STATUS BADGE -->
                <div class="p-3 rounded-xl border {{ $botInfo['has_active_remote'] ? 'bg-emerald-950/20 border-emerald-500/30 text-emerald-300' : 'bg-amber-950/20 border-amber-500/30 text-amber-300' }} text-xs space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="font-bold flex items-center space-x-1.5">
                            <span class="w-2 h-2 rounded-full {{ $botInfo['has_active_remote'] ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400' }}"></span>
                            <span>{{ $botInfo['has_active_remote'] ? 'Telegram Confirmed Active' : 'Webhook Not Yet Active at Telegram' }}</span>
                        </span>
                        @if($botInfo['has_active_remote'])
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-mono">LIVE</span>
                        @endif
                    </div>
                    @if($botInfo['has_active_remote'])
                        <div class="text-[11px] font-mono truncate text-white">
                            {{ $botInfo['remote_webhook_url'] }}
                        </div>
                    @endif
                    @if(!empty($botInfo['last_error_message']))
                        <div class="text-[10px] text-red-400 font-mono mt-1">
                            Last Telegram Error: {{ $botInfo['last_error_message'] }}
                        </div>
                    @endif
                </div>

                <!-- Webhook Form -->
                <form method="POST" action="{{ route('admin.telegram.webhook.set') }}" class="space-y-3">
                    @csrf
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] text-zinc-400 uppercase font-semibold">Webhook Destination URL</label>
                            <button type="button" 
                                    @click="$refs.webhookInput.value = '{{ rtrim($settings['telegram_webapp_url'], '/') }}/api/telegram/webhook'"
                                    class="text-[10px] text-sky-400 hover:text-sky-300 font-semibold transition">
                                ⚡ Fill from WebApp URL
                            </button>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="url" name="url" x-ref="webhookInput" value="{{ old('url', $botInfo['webhook_url']) }}" required
                                placeholder="https://your-tunnel.loca.lt/api/telegram/webhook"
                                class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-xs font-mono text-zinc-200 focus:outline-none focus:border-gold">
                            <button type="button" @click="navigator.clipboard.writeText($refs.webhookInput.value); copiedWebhookUrl = true; setTimeout(() => copiedWebhookUrl = false, 2000)" class="px-3 py-2 rounded-xl bg-admin-panel border border-admin-border text-zinc-300 hover:text-white text-xs font-medium transition shrink-0">
                                <span x-text="copiedWebhookUrl ? 'Copied!' : 'Copy'">Copy</span>
                            </button>
                        </div>
                        <p class="text-[10px] text-zinc-500 mt-1">For Localtunnel on port 8000: <code class="text-zinc-400">https://your-tunnel.loca.lt/api/telegram/webhook</code></p>
                    </div>

                    <!-- Webhook Submit Button -->
                    <div>
                        <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition text-center flex items-center justify-center space-x-1.5 shadow-md shadow-emerald-600/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Save & Register Webhook</span>
                        </button>
                    </div>
                </form>

                <!-- Delete Webhook Section -->
                <div class="flex items-center justify-between pt-2 border-t border-admin-border/50 text-xs">
                    <span class="text-zinc-400 text-[11px]">Need to test via CLI polling instead?</span>
                    <form method="POST" action="{{ route('admin.telegram.webhook.delete') }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Remove active webhook from Telegram servers?')" class="px-3 py-1.5 rounded-lg bg-red-950/40 hover:bg-red-900/50 border border-red-500/40 text-red-300 text-xs font-medium transition">
                            Delete Webhook
                        </button>
                    </form>
                </div>

                <p class="text-[10px] text-zinc-500 italic">
                    Note: Telegram requires a public HTTPS URL (such as Localtunnel, Cloudflare tunnel, or domain with SSL) for webhooks.
                </p>
            </div>

            <!-- SUBSCRIBER REGISTRY SNEAK PEEK -->
            <div class="bg-admin-card border border-admin-border rounded-2xl p-6 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-admin-border">
                    <div class="flex items-center space-x-2">
                        <span class="text-base">👥</span>
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">Recent Telegram Subscribers</h3>
                    </div>
                    <span class="text-xs text-sky-400 font-bold">{{ $stats['total_subscribers'] }} total</span>
                </div>

                @if($subscribers->isEmpty())
                    <div class="text-center py-6 text-zinc-500 text-xs">
                        No subscribers registered yet. Anyone messaging <code class="text-zinc-400">@{{ $botInfo['bot_username'] }}</code> will appear here automatically!
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach($subscribers as $sub)
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-admin-panel border border-admin-border text-xs">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-7 h-7 rounded-full bg-sky-500/20 text-sky-400 flex items-center justify-center font-bold text-[10px]">
                                    {{ strtoupper(substr($sub->display_name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-bold text-white block">{{ $sub->display_name }}</span>
                                    <span class="text-[10px] text-zinc-400 font-mono">ID: {{ $sub->chat_id }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    @if($sub->favorite_club === 'Arsenal') bg-red-500/20 text-red-400
                                    @elseif($sub->favorite_club === 'Manchester United') bg-amber-500/20 text-amber-400
                                    @elseif($sub->favorite_club === 'Chelsea') bg-blue-500/20 text-blue-400
                                    @else bg-zinc-800 text-zinc-300 @endif">
                                    {{ $sub->favorite_club ?? 'General' }}
                                </span>
                                <span class="text-[9px] text-zinc-500 block mt-0.5">{{ $sub->last_interaction_at ? $sub->last_interaction_at->diffForHumans() : 'Active' }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- BOTFATHER GUIDE CARD -->
            <div class="bg-admin-card border border-admin-border rounded-2xl p-6 shadow-xl space-y-3">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center space-x-2">
                    <span>📖</span>
                    <span>Quick @BotFather Setup Guide</span>
                </h3>
                <ol class="list-decimal list-inside space-y-2 text-xs text-zinc-300">
                    <li>Start a chat with <code class="text-gold">@BotFather</code> in Telegram and send <code class="text-gold">/newbot</code>.</li>
                    <li>Set your bot name (<code class="text-white">Gura Bicha Sports Bar</code>) and username (<code class="text-white">GuraBichaAddisBot</code>).</li>
                    <li>Paste the token into the <strong>Telegram Bot Token</strong> field above.</li>
                    <li>In @BotFather send <code class="text-gold">/setmenubutton</code>, select your bot, and configure URL: <code class="text-gold">{{ url('/') }}</code>.</li>
                    <li>Register the webhook or run <code class="text-white bg-black/40 px-1.5 py-0.5 rounded font-mono">php artisan telegram:poll</code>.</li>
                </ol>
            </div>

        </div>

    </div>

</div>
@endsection
