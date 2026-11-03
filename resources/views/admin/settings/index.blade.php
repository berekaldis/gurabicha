@extends('admin.layout', [
    'title' => 'Venue Settings & Menu QR Code',
    'header' => 'Venue Settings & QR Code',
    'subHeader' => 'Manage venue information, Addis Ababa location, acoustic session hours, and customer menu QR codes.'
])

@section('content')
<div class="space-y-8" x-data="{
    telegramBotUrl: 'https://t.me/GuraBichaLiveBot',
    selectedTable: 'ALL',
    copied: false,
    downloadingCard: false,
    get activeQrUrl() {
        return this.selectedTable === 'ALL' ? this.telegramBotUrl : this.telegramBotUrl + '?start=table_' + this.selectedTable;
    },
    copyLink() {
        navigator.clipboard.writeText(this.activeQrUrl);
        this.copied = true;
        setTimeout(() => this.copied = false, 2500);
    },
    downloadCard() {
        this.downloadingCard = true;
        const canvas = document.createElement('canvas');
        canvas.width = 800;
        canvas.height = 1100;
        const ctx = canvas.getContext('2d');

        // Background
        ctx.fillStyle = '#0a0a0c';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Gold Outer Border
        ctx.lineWidth = 12;
        ctx.strokeStyle = '#d4af37';
        ctx.strokeRect(30, 30, 740, 1040);

        // Gold Inner Accent Border
        ctx.lineWidth = 2;
        ctx.strokeStyle = '#f5bd19';
        ctx.strokeRect(44, 44, 712, 1012);

        // Header Title
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 52px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('GURA BICHA', 400, 140);

        ctx.fillStyle = '#d4af37';
        ctx.font = 'bold 22px sans-serif';
        ctx.fillText('SPORTS BAR & ACOUSTIC LOUNGE', 400, 180);

        ctx.fillStyle = '#94a3b8';
        ctx.font = '18px sans-serif';
        ctx.fillText('Mexico, Gura Bicha · Addis Ababa', 400, 215);

        // Table Badge
        const tableLabel = this.selectedTable === 'ALL' ? 'VIP GUEST' : 'TABLE ' + this.selectedTable;
        ctx.fillStyle = '#d4af37';
        ctx.fillRect(260, 250, 280, 50);
        ctx.fillStyle = '#000000';
        ctx.font = 'bold 26px sans-serif';
        ctx.fillText(tableLabel, 400, 285);

        // Load and Draw QR Code
        const qrImg = new Image();
        qrImg.crossOrigin = 'anonymous';
        const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=500x500&margin=20&data=' + encodeURIComponent(this.activeQrUrl);
        qrImg.onload = () => {
            // White card behind QR
            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            ctx.roundRect(175, 340, 450, 450, 24);
            ctx.fill();

            ctx.drawImage(qrImg, 190, 355, 420, 420);

            // Instructions below QR
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 30px sans-serif';
            ctx.fillText('SCAN WITH CAMERA', 400, 860);

            ctx.fillStyle = '#d4af37';
            ctx.font = 'bold 24px sans-serif';
            ctx.fillText('TO LAUNCH TELEGRAM MINI APP', 400, 905);

            ctx.fillStyle = '#64748b';
            ctx.font = '16px monospace';
            ctx.fillText('@GuraBichaLiveBot', 400, 950);

            // Download trigger
            const link = document.createElement('a');
            link.download = 'gura-bicha-tent-card-' + this.selectedTable + '.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
            this.downloadingCard = false;
        };
        qrImg.onerror = () => {
            alert('Could not download tent card. Please try again.');
            this.downloadingCard = false;
        };
        qrImg.src = qrUrl;
    }
}">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- SETTINGS FORM (2 COLS) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-admin-card border border-admin-border rounded-2xl p-6 shadow-xl">
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-admin-border">
                    <div class="w-10 h-10 rounded-xl bg-gold/10 border border-gold/30 flex items-center justify-center text-gold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white tracking-wide">General Venue Information</h2>
                        <p class="text-xs text-zinc-400">Update restaurant profile, Addis Ababa location, and acoustic schedule</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Venue Name</label>
                        <input type="text" name="venue_name" value="{{ old('venue_name', $settings['venue_name']) }}" required
                            class="w-full bg-admin-panel border border-admin-border rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-gold">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Phone Contact</label>
                            <input type="text" name="venue_phone" value="{{ old('venue_phone', $settings['venue_phone']) }}" required
                                class="w-full bg-admin-panel border border-admin-border rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-gold">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Email Contact</label>
                            <input type="email" name="venue_email" value="{{ old('venue_email', $settings['venue_email']) }}" required
                                class="w-full bg-admin-panel border border-admin-border rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-gold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">
                            Restaurant Physical Location (Ethiopia)
                        </label>
                        <input type="text" name="venue_location" value="{{ old('venue_location', $settings['venue_location']) }}" required
                            placeholder="Addis Ababa, Ethiopia, Mexico inside Chamber of Commerce Building"
                            class="w-full bg-admin-panel border border-admin-border rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-gold">
                        <p class="text-[11px] text-zinc-500 mt-1">Displayed on the public website footer, header, and reservation confirmations.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Acoustic Music Day</label>
                            <input type="text" name="acoustic_day" value="{{ old('acoustic_day', $settings['acoustic_day']) }}" required
                                class="w-full bg-admin-panel border border-admin-border rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-gold">
                            <p class="text-[11px] text-zinc-500 mt-1">Saturday Acoustic Groove Nights</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Acoustic Session Hours</label>
                            <input type="text" name="acoustic_time" value="{{ old('acoustic_time', $settings['acoustic_time']) }}" required
                                class="w-full bg-admin-panel border border-admin-border rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-gold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Overall Venue Operating Hours</label>
                        <input type="text" name="venue_hours" value="{{ old('venue_hours', $settings['venue_hours']) }}" required
                            class="w-full bg-admin-panel border border-admin-border rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-gold">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Google Maps Direct Link</label>
                        <input type="url" name="google_maps_url" value="{{ old('google_maps_url', $settings['google_maps_url']) }}" required
                            class="w-full bg-admin-panel border border-admin-border rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-gold">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Google Maps Embed URL (Iframe Src)</label>
                        <input type="text" name="google_maps_embed" value="{{ old('google_maps_embed', $settings['google_maps_embed']) }}"
                            class="w-full bg-admin-panel border border-admin-border rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-gold">
                    </div>

                    <!-- MAP PREVIEW -->
                    @if(!empty($settings['google_maps_embed']))
                    <div class="mt-4">
                        <span class="block text-xs font-semibold text-zinc-400 mb-2">Location Map Live Preview:</span>
                        <div class="w-full h-48 rounded-xl overflow-hidden border border-admin-border">
                            <iframe src="{{ $settings['google_maps_embed'] }}" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                    @endif

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gold hover:bg-gold-light text-black font-bold text-sm tracking-wide transition shadow-lg shadow-gold/20 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Save Venue Settings</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- DEDICATED TELEGRAM CONFIGURATION NOTIFICATION / LINK -->
            <div class="bg-admin-card border border-admin-border rounded-2xl p-5 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-3.5">
                    <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/30 flex items-center justify-center text-sky-400 shrink-0">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18.716-.967 4.144-1.365 5.836-.168.716-.434.956-.69 0.98-.557.051-.98-.369-1.52-.723-.845-.554-1.323-.899-2.143-1.44-.948-.624-.333-.967.207-1.528.141-.147 2.598-2.383 2.645-2.586.006-.025.011-.122-.047-.174-.058-.052-.144-.034-.206-.02-.088.02-1.488.948-4.202 2.781-.397.272-.757.406-1.079.399-.356-.008-1.041-.202-1.551-.368-.625-.203-1.121-.311-1.078-.656.022-.18.271-.364.747-.553 2.923-1.274 4.873-2.115 5.85-2.524 2.785-1.163 3.364-1.365 3.743-1.371.083-.001.27.02.39.119.102.083.13.195.143.274.013.08.029.262.016.406z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Telegram Bot & Mini App Configuration</h3>
                        <p class="text-xs text-zinc-400">Bot credentials, Webhook diagnostics, and Fan Broadcast Announcements now have a dedicated navigation console.</p>
                    </div>
                </div>
                <a href="{{ route('admin.telegram.index') }}" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs transition shadow-md shadow-sky-600/30 flex items-center justify-center space-x-1.5 shrink-0">
                    <span>Open Telegram Config</span>
                    <span>→</span>
                </a>
            </div>
        </div>

        <!-- MENU QR CODE & TENT CARDS (1 COL) -->
        <div class="space-y-6">
            <div class="bg-admin-card border border-admin-border rounded-2xl p-6 shadow-xl relative overflow-hidden">
                <div class="absolute -right-12 -top-12 w-32 h-32 bg-gold/10 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex items-center space-x-3 mb-5 pb-4 border-b border-admin-border">
                    <div class="w-10 h-10 rounded-xl bg-gold/10 border border-gold/30 flex items-center justify-center text-gold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white tracking-wide">Digital Menu QR Code</h2>
                        <p class="text-xs text-zinc-400">Generate table-specific or general QR codes</p>
                    </div>
                </div>

                <!-- TABLE SELECTOR -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-zinc-300 uppercase tracking-wider mb-1.5">Select Table for QR Tent Card:</label>
                    <select x-model="selectedTable" class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-gold">
                        <option value="ALL">General Bar & Dining (All Tables)</option>
                        <option value="T1">Table T1 — Stage Left View</option>
                        <option value="T2">Table T2 — Center Acoustic Floor</option>
                        <option value="T3">Table T3 — Stage Right View</option>
                        <option value="T4">Table T4 — Acoustic Stage Front</option>
                        <option value="T5">Table T5 — Mezzanine Lounge</option>
                        <option value="T6">Table T6 — Artist Circle</option>
                    </select>
                </div>

                <!-- QR CODE DISPLAY -->
                <div class="flex flex-col items-center justify-center bg-white p-6 rounded-2xl border-2 border-gold/40 shadow-inner my-4">
                    <div x-show="selectedTable !== 'ALL'" class="mb-2 px-3 py-0.5 rounded-full bg-black text-gold text-xs font-black tracking-widest uppercase">
                        <span x-text="'TABLE ' + selectedTable"></span>
                    </div>
                    <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=260x260&margin=10&data=' + encodeURIComponent(activeQrUrl)"
                        alt="Gura Bicha Telegram Bot QR Code"
                        class="w-48 h-48 rounded-lg shadow-sm">
                    <div class="mt-3 text-center">
                        <span class="text-[11px] font-black tracking-widest text-black uppercase">Gura Bicha • Addis Ababa</span>
                        <p class="text-[10px] text-zinc-600 mt-0.5 font-mono">Scan to launch Telegram Mini App</p>
                    </div>
                </div>

                <!-- MENU LINK & ACTIONS -->
                <div class="space-y-3 pt-2">
                    <div>
                        <label class="block text-[11px] text-zinc-400 mb-1">Target Telegram Link:</label>
                        <div class="flex items-center space-x-2">
                            <input type="text" readonly :value="activeQrUrl"
                                class="w-full bg-admin-panel border border-admin-border rounded-xl px-3 py-2 text-xs text-gold font-mono truncate focus:outline-none">
                            <button @click="copyLink"
                                class="px-3 py-2 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-xs text-white font-medium transition shrink-0 flex items-center space-x-1">
                                <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row gap-2">
                        <button @click="downloadCard()" :disabled="downloadingCard"
                            class="flex-1 text-center px-4 py-2.5 rounded-xl bg-admin-panel hover:bg-zinc-800 border border-gold/40 text-xs text-gold font-bold transition flex items-center justify-center space-x-2 shadow-sm">
                            <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span x-text="downloadingCard ? 'Generating Card...' : '📥 Download Card (PNG)'"></span>
                        </button>

                        <button onclick="window.print()"
                            class="px-4 py-2.5 rounded-xl bg-gold hover:bg-gold-light text-black text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow-md shadow-gold/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Print Tent Card</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- QUICK VENUE STATS / HIGHLIGHTS -->
            <div class="bg-admin-card border border-admin-border rounded-2xl p-5 shadow-xl">
                <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-3">Venue Quick Summary</h3>
                <ul class="space-y-2.5 text-xs">
                    <li class="flex items-start justify-between text-zinc-300">
                        <span class="text-zinc-500">City / District:</span>
                        <span class="font-medium text-white text-right">Addis Ababa, Mexico</span>
                    </li>
                    <li class="flex items-start justify-between text-zinc-300">
                        <span class="text-zinc-500">Building:</span>
                        <span class="font-medium text-gold text-right">Chamber of Commerce</span>
                    </li>
                    <li class="flex items-start justify-between text-zinc-300">
                        <span class="text-zinc-500">Acoustic Night:</span>
                        <span class="font-medium text-emerald-400">Every Saturday</span>
                    </li>
                    <li class="flex items-start justify-between text-zinc-300">
                        <span class="text-zinc-500">Football Viewing:</span>
                        <span class="font-medium text-white">Walk-in General Seating</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>

<!-- PRINT-ONLY TENT CARD STYLES -->
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #print-tent-card, #print-tent-card * {
        visibility: visible;
    }
    #print-tent-card {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        display: block !important;
    }
}
</style>

<!-- HIDDEN PRINT TEMPLATE FOR TABLE TENT CARD (SMALL & SMART VIP CARD) -->
<div id="print-tent-card" class="hidden p-6 max-w-xs mx-auto text-center font-sans text-black bg-white border-4 border-black rounded-3xl m-6">
    <div class="border-b-2 border-black pb-3 mb-3">
        <h1 class="text-xl font-black tracking-wider uppercase">GURA BICHA</h1>
        <p class="text-[10px] uppercase tracking-widest font-bold text-zinc-700">Sports Bar & Acoustic Lounge</p>
        <p class="text-[10px] text-zinc-600 mt-0.5">Mexico, Gura Bicha · Addis Ababa</p>
        <div x-show="selectedTable !== 'ALL'" class="mt-2 inline-block px-3 py-0.5 bg-black text-white rounded-lg text-xs font-black tracking-widest">
            <span x-text="'TABLE ' + selectedTable"></span>
        </div>
    </div>
    
    <div class="my-4 flex justify-center">
        <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=300x300&margin=10&data=' + encodeURIComponent(activeQrUrl)" 
             alt="QR Code" 
             class="w-48 h-48 border-2 border-black rounded-xl">
    </div>

    <div class="space-y-0.5">
        <h2 class="text-sm font-black uppercase tracking-wide">SCAN WITH CAMERA</h2>
        <p class="text-[11px] text-amber-700 font-bold uppercase">To Open on Telegram</p>
        <p class="text-[9px] text-zinc-500 font-mono">@GuraBichaLiveBot</p>
    </div>
</div>
@endsection
