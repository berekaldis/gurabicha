<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Gura Bicha Sports Bar & Acoustic Lounge — Game & Groove Addis Ababa</title>
    <meta name="description" content="Addis Ababa's premier sports bar & acoustic lounge in Mexico, inside Chamber of Commerce Building. Live Premier League, Champions League, Saturday Acoustic Sessions & authentic Ethiopian cuisine.">
    <meta name="theme-color" content="#060709">

    <!-- Immediate Theme & Telegram Insets Initialization Script (prevents theme flash & Telegram header overlap) -->
    <script>
        (function() {
            try {
                var savedTheme = localStorage.getItem('GB_THEME') || 'dark';
                document.documentElement.className = savedTheme;
                var isTg = (window.Telegram && window.Telegram.WebApp) ||
                           window.TelegramWebviewProxy !== undefined ||
                           window.location.hash.indexOf('tgWebAppData') !== -1 ||
                           window.location.search.indexOf('tgWebAppPlatform') !== -1 ||
                           /Telegram/i.test(navigator.userAgent);
                if (isTg) {
                    document.documentElement.classList.add('telegram-webapp');
                    document.documentElement.style.setProperty('--tg-top-inset', '56px');
                }
            } catch(e) {}
        })();
    </script>

    <!-- Favicon: Official Gura Bicha GB Golden Shield Emblem -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Telegram WebApp SDK -->
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <script>
        try {
            if (window.Telegram && window.Telegram.WebApp) {
                document.documentElement.classList.add('telegram-webapp');
                window.Telegram.WebApp.ready();
                var topInset = 56;
                if (window.Telegram.WebApp.safeAreaInset && window.Telegram.WebApp.safeAreaInset.top) {
                    topInset = Math.max(topInset, window.Telegram.WebApp.safeAreaInset.top);
                }
                if (window.Telegram.WebApp.contentSafeAreaInset && window.Telegram.WebApp.contentSafeAreaInset.top) {
                    topInset = Math.max(topInset, window.Telegram.WebApp.contentSafeAreaInset.top);
                }
                document.documentElement.style.setProperty('--tg-top-inset', topInset + 'px');
            }
        } catch(e) {}
    </script>

    <!-- Google Fonts: Oswald (Headings), Inter (Body) & Noto Sans Ethiopic (Amharic) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Ethiopic:wght@400;500;600;700;800&family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN with Custom Theme -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        gold: {
                            DEFAULT: '#f5bd19',
                            light: '#ffd34d',
                            dark: '#c49610',
                            glow: 'rgba(245, 189, 25, 0.25)',
                        },
                        dark: {
                            bg: '#060709',
                            card: '#0f1117',
                            panel: '#161922',
                            border: '#2a2f3d',
                            muted: '#94a3b8',
                        }
                    },
                    fontFamily: {
                        display: ['Oswald', 'sans-serif'],
                        sans: ['Inter', 'Noto Sans Ethiopic', 'sans-serif'],
                        ethiopic: ['Noto Sans Ethiopic', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js for State Management -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <!-- High-Contrast Styling & Dual Theme Support (Light / Dark) -->
    <style>
        /* Mobile Viewport & Telegram Mini App Responsiveness */
        :root {
            --app-height: 100dvh;
        }

        html {
            min-height: 100%;
            min-height: -webkit-fill-available;
            min-height: 100dvh;
            width: 100%;
            overflow-x: hidden;
            -webkit-text-size-adjust: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100%;
            min-height: -webkit-fill-available;
            min-height: 100dvh;
            width: 100%;
            max-width: 100vw;
            overflow-x: hidden;
            -webkit-tap-highlight-color: transparent;
            -webkit-overflow-scrolling: touch;
            touch-action: pan-y;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* Safe area helpers */
        .mobile-header-safe {
            padding-top: max(0.75rem, calc(var(--tg-top-inset, 0px) + var(--tg-safe-area-inset-top, 0px) + var(--tg-content-safe-area-inset-top, 0px) + env(safe-area-inset-top, 0px)));
        }

        /* In Telegram WebApp, guarantee full clearance below Telegram's native 'Close' & menu bar */
        html.telegram-webapp .mobile-header-safe,
        body.telegram-webapp .mobile-header-safe {
            padding-top: max(3.75rem, calc(var(--tg-top-inset, 56px) + 0.4rem)) !important;
        }

        .mobile-nav-safe {
            padding-bottom: max(0.5rem, calc(0.4rem + env(safe-area-inset-bottom, 0px)));
        }

        .main-container-safe {
            padding-bottom: max(6.5rem, calc(5.5rem + env(safe-area-inset-bottom, 0px)));
        }

        .floating-bar-safe {
            bottom: max(5rem, calc(4.75rem + env(safe-area-inset-bottom, 0px)));
        }

        .touch-scroll {
            -webkit-overflow-scrolling: touch;
            touch-action: pan-x;
        }
        .font-display {
            font-family: 'Oswald', sans-serif;
            letter-spacing: 0.03em;
        }

        /* Dark Mode High-Contrast Rules */
        html.dark body {
            background-color: #060709;
            color: #f1f5f9;
        }
        html.dark .gold-gradient-text {
            background: linear-gradient(135deg, #ffd34d 0%, #f5bd19 60%, #c49610 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ============================================================== */
        /* LIGHT MODE HIGH-CONTRAST TYPOGRAPHY & CARD PALETTES            */
        /* Warm Tuscan Luxury Cream & Polished Royal Gold                  */
        /* ============================================================== */
        html.light body {
            background-color: #faf8f5;
            color: #0f172a;
        }
        html.light .bg-dark-bg {
            background-color: #f3efe6 !important;
            color: #0f172a !important;
        }
        html.light .bg-dark-card {
            background-color: #ffffff !important;
            border-color: rgba(212, 175, 55, 0.3) !important;
            color: #0f172a !important;
            box-shadow: 0 10px 30px -4px rgba(180, 83, 9, 0.07), 0 2px 6px 0 rgba(0, 0, 0, 0.03) !important;
        }
        html.light .bg-dark-panel {
            background-color: #fbf9f6 !important;
            border-color: #e5ded4 !important;
            color: #0f172a !important;
        }
        html.light .border-dark-border {
            border-color: #e5ded4 !important;
        }
        html.light header {
            background-color: rgba(255, 255, 255, 0.96) !important;
            border-color: #e5ded4 !important;
            box-shadow: 0 4px 20px rgba(180, 83, 9, 0.05);
        }
        html.light nav.fixed {
            background-color: rgba(255, 255, 255, 0.98) !important;
            border-color: #e5ded4 !important;
            box-shadow: 0 -4px 20px rgba(180, 83, 9, 0.06);
        }
        html.light .gold-gradient-text {
            background: linear-gradient(135deg, #b45309 0%, #d97706 60%, #92400e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Light Mode Text Visibility - Deep High Contrast Hierarchies */
        html.light .text-white {
            color: #0f172a !important;
        }
        html.light .text-zinc-100 {
            color: #0f172a !important;
        }
        html.light .text-zinc-200 {
            color: #1e293b !important;
        }
        html.light .text-zinc-300 {
            color: #334155 !important;
        }
        html.light .text-zinc-400 {
            color: #475569 !important;
        }
        html.light .text-zinc-500 {
            color: #64748b !important;
        }
        /* Crisp Golden Amber on Light Backgrounds (prevents bright yellow on white) */
        html.light .text-gold {
            color: #b45309 !important;
        }
        html.light .text-gold-light {
            color: #92400e !important;
        }

        /* Form Controls in Light Mode */
        html.light input,
        html.light textarea,
        html.light select {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }
        html.light input::placeholder,
        html.light textarea::placeholder {
            color: #94a3b8 !important;
        }

        /* CRITICAL: Protect text readability inside dark photo badges in light mode */
        html.light [class*="bg-black/"],
        html.light [class*="bg-black"],
        html.light [class*="bg-zinc-900/"],
        html.light [class*="bg-zinc-900"] {
            color: #ffffff;
        }
        html.light [class*="bg-black/"] .text-white,
        html.light [class*="bg-black"] .text-white,
        html.light [class*="bg-zinc-900"] .text-white {
            color: #ffffff !important;
        }
        html.light [class*="bg-black/"] .text-gold,
        html.light [class*="bg-black"] .text-gold,
        html.light [class*="bg-zinc-900"] .text-gold {
            color: #fbbf24 !important; /* Vibrant gold over dark image badges */
        }
        html.light [class*="bg-black/"] .text-zinc-300,
        html.light [class*="bg-black"] .text-zinc-300 {
            color: #e2e8f0 !important;
        }

        /* Badges on cards in light mode */
        html.light .badge-pl {
            background-color: #f3e8ff !important;
            color: #6b21a8 !important;
            border-color: #d8b4fe !important;
        }
        html.light .badge-ucl {
            background-color: #e0f2fe !important;
            color: #0369a1 !important;
            border-color: #7dd3fc !important;
        }
        html.light .badge-saturday {
            background-color: #fef3c7 !important;
            color: #92400e !important;
            border-color: #fcd34d !important;
        }

        /* Card Hover Animations */
        .card-hover-lift {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
        }
        .card-hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px -8px rgba(245, 189, 25, 0.18);
            border-color: rgba(245, 189, 25, 0.6);
        }

        /* Soundwave Equalizer Animation */
        @keyframes soundwave {
            0%, 100% { height: 6px; }
            50% { height: 26px; }
        }
        .soundwave-1 { animation: soundwave 1.1s ease-in-out infinite; }
        .soundwave-2 { animation: soundwave 0.8s ease-in-out infinite 0.2s; }
        .soundwave-3 { animation: soundwave 1.4s ease-in-out infinite 0.4s; }
        .soundwave-4 { animation: soundwave 0.9s ease-in-out infinite 0.1s; }
        .soundwave-5 { animation: soundwave 1.2s ease-in-out infinite 0.3s; }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #f5bd19;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Hide default browser calendar picker indicator to avoid duplicate icon with custom gold picker button */
        input[type="date"]::-webkit-calendar-picker-indicator {
            display: none !important;
            -webkit-appearance: none;
            opacity: 0 !important;
            width: 0 !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            pointer-events: none;
        }

        /* Atmospheric Floating Particle Animations */
        @keyframes floatUp {
            0% { transform: translateY(0) rotate(0deg) scale(0.8); opacity: 0; }
            15% { opacity: 0.35; }
            85% { opacity: 0.25; }
            100% { transform: translateY(-110vh) rotate(360deg) scale(1.1); opacity: 0; }
        }
        .floating-particle {
            animation: floatUp 20s linear infinite;
            pointer-events: none;
            user-select: none;
        }
        .float-delay-1 { animation-delay: 0s; animation-duration: 22s; }
        .float-delay-2 { animation-delay: 4s; animation-duration: 18s; }
        .float-delay-3 { animation-delay: 8s; animation-duration: 25s; }
        .float-delay-4 { animation-delay: 12s; animation-duration: 21s; }
        .float-delay-5 { animation-delay: 16s; animation-duration: 24s; }

        /* Live Beacon Pulse */
        @keyframes pulseLive {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(1.25); }
        }
        .pulse-live {
            animation: pulseLive 1.4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Light Mode Background Overlay Scrim */
        html.light .html-light-bg-overlay {
            background: linear-gradient(to bottom, rgba(248, 250, 252, 0.92), rgba(241, 245, 249, 0.96), rgba(248, 250, 252, 0.98)) !important;
        }
    </style>

    <!-- App URLs & Pre-hydrated Data -->
    <script>
        (function() {
            var origin = window.location.origin;
            var path = window.location.pathname;
            var subPath = path.indexOf('/Gurabicha/public') !== -1 ? '/Gurabicha/public' : '';
            window.API_BASE = origin + subPath + '/api';
            window.APP_BASE = origin + subPath;
            window.MENU_URL = origin + subPath + '/#menu';
        })();
        window.INITIAL_MENU = @json($menu ?? []);
        window.INITIAL_EVENTS = @json($events ?? []);
    </script>
</head>
<body class="bg-dark-bg text-zinc-100 min-h-screen pb-24 selection:bg-gold selection:text-black relative" x-data="guraBichaApp()" x-init="initApp()">

    <!-- ============================================================== -->
    <!-- DYNAMIC BACKGROUND SLIDESHOW: FOOTBALL & ACOUSTIC MUSIC        -->
    <!-- Alternating slides, subtle Ken Burns pan/zoom, in/out effects  -->
    <!-- ============================================================== -->
    <div class="fixed inset-0 pointer-events-none -z-20 overflow-hidden select-none">
        <template x-for="(slide, index) in bgSlides" :key="index">
            <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
                 :class="{
                    'opacity-100 scale-105 transition-all duration-[7000ms] ease-out': currentBgIndex === index,
                    'opacity-0 scale-100 pointer-events-none': currentBgIndex !== index
                 }">
                <img :src="slide.image" :alt="slide.title" class="w-full h-full object-cover object-center filter brightness-[0.4] contrast-125 transition-transform duration-[7000ms]" loading="lazy">
                
                <!-- Ambient badge in top corner for the active vibe -->
                <div class="absolute top-20 right-4 hidden md:flex items-center space-x-2 px-3 py-1 rounded-full bg-black/40 backdrop-blur-md border border-white/10 text-[10px] text-zinc-300 font-medium">
                    <span x-text="slide.icon"></span>
                    <span x-text="slide.title"></span>
                </div>
            </div>
        </template>

        <!-- Protective Dark/Light Gradient Scrim (Guarantees 100% text readability) -->
        <div class="absolute inset-0 bg-gradient-to-b from-[#060709]/80 via-[#060709]/88 to-[#060709] html-light-bg-overlay backdrop-blur-[1px]"></div>

        <!-- Ambient Floating Elements (Musical notes & footballs drifting up) -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <span class="floating-particle float-delay-1 text-gold/20 text-2xl absolute left-[8%] bottom-[-30px]">⚽</span>
            <span class="floating-particle float-delay-2 text-gold/25 text-xl absolute left-[28%] bottom-[-30px]">🎸</span>
            <span class="floating-particle float-delay-3 text-gold/20 text-2xl absolute left-[52%] bottom-[-30px]">🎵</span>
            <span class="floating-particle float-delay-4 text-gold/25 text-xl absolute left-[76%] bottom-[-30px]">⚽</span>
            <span class="floating-particle float-delay-5 text-gold/20 text-2xl absolute left-[92%] bottom-[-30px]">🎶</span>
        </div>
    </div>

    <!-- BACKGROUND AMBIENT GLOW -->
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-5xl h-96 bg-gradient-to-b from-amber-500/10 via-gold/5 to-transparent blur-3xl pointer-events-none -z-10"></div>

    <!-- TOP HEADER -->
    <header class="sticky top-0 z-40 bg-dark-bg/95 backdrop-blur-lg border-b border-dark-border px-3 sm:px-4 py-2 sm:py-2.5 shadow-md mobile-header-safe">
        <div class="w-full max-w-xl mx-auto md:max-w-4xl flex items-center justify-between gap-1.5 sm:gap-2">
            <div class="flex items-center space-x-2 sm:space-x-3 cursor-pointer min-w-0 flex-1" @click="setScreen('home')">
                <img src="{{ asset('images/logo.png') }}" alt="Gura Bicha Sport Bar Logo" class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl object-contain shadow-md shadow-gold/25 transform hover:scale-105 transition shrink-0 border border-gold/40 bg-black p-0.5">
                <div class="min-w-0 flex-1">
                    <h1 class="font-display text-sm sm:text-lg tracking-wider text-white font-bold leading-none flex items-center space-x-1 sm:space-x-1.5 truncate">
                        <span x-text="t('app_title')">GURA BICHA</span>
                        <span class="text-[8px] sm:text-[9px] px-1 sm:px-1.5 py-0.2 sm:py-0.5 rounded bg-gold/15 text-gold border border-gold/30 font-sans font-semibold uppercase shrink-0">Addis</span>
                    </h1>
                    <p class="text-[8px] sm:text-[10px] text-zinc-400 tracking-wider uppercase font-semibold truncate mt-0.5" x-text="t('app_subtitle')">
                        Sports Bar & Lounge · Mexico
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-1.5 sm:space-x-2 shrink-0">
                <!-- LANGUAGE SWITCHER PILL (ENGLISH / AMHARIC) -->
                <button type="button" 
                        @click="toggleLang()" 
                        :title="lang === 'en' ? 'Switch to Amharic (አማርኛ)' : 'Switch to English'" 
                        class="px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-xl bg-dark-panel border border-gold/40 text-gold hover:border-gold hover:bg-gold/15 transition-all shadow-sm flex items-center space-x-1 font-bold text-xs shrink-0 active:scale-95">
                    <span class="text-xs">🌐</span>
                    <span class="tracking-wider uppercase text-[11px] font-extrabold" x-text="lang === 'en' ? 'EN' : 'አማ'"></span>
                </button>

                <!-- THEME SWITCHER PILL (VIP DARK / LUXURY DAY) -->
                <button type="button" 
                        @click="toggleTheme()" 
                        :title="theme === 'dark' ? 'Switch to Bright Mode' : 'Switch to Dark VIP Mode'" 
                        class="px-2.5 py-1.5 sm:px-3 sm:py-2 rounded-xl bg-dark-panel border border-gold/40 hover:border-gold hover:bg-gold/15 transition-all shadow-sm flex items-center space-x-1 font-bold text-xs shrink-0 active:scale-95">
                    <template x-if="theme === 'dark'">
                        <span class="flex items-center space-x-1 text-gold">
                            <span class="text-xs">🌙</span>
                            <span class="text-[10px] font-black uppercase tracking-wider">VIP</span>
                        </span>
                    </template>
                    <template x-if="theme === 'light'">
                        <span class="flex items-center space-x-1 text-amber-700">
                            <span class="text-xs">☀️</span>
                            <span class="text-[10px] font-black uppercase tracking-wider">DAY</span>
                        </span>
                    </template>
                </button>

                <!-- VENUE INFO & DIRECTIONS BUTTON -->
                <button type="button" @click="showVenueModal = true" class="px-2 sm:px-2.5 py-1.5 sm:py-2 text-xs rounded-xl bg-dark-panel border border-dark-border text-zinc-200 hover:text-gold hover:border-gold/50 transition flex items-center space-x-1 font-medium shrink-0 active:scale-95">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="hidden sm:inline text-[11px]" x-text="t('map_chamber')">Mexico</span>
                    <span class="sm:hidden text-xs">📍</span>
                </button>
            </div>
        </div>
    </header>

    <!-- MAIN VIEW CONTAINER -->
    <main class="w-full max-w-xl mx-auto md:max-w-4xl px-2.5 sm:px-4 pt-2.5 sm:pt-4 main-container-safe overflow-x-hidden space-y-4 sm:space-y-5">

        <!-- ========================================== -->
        <!-- SCREEN 1: HOME                             -->
        <!-- ========================================== -->
        <div x-show="screen === 'home'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4 sm:space-y-5">
            
            <!-- HERO BANNER -->
            <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden border border-gold/30 bg-dark-card shadow-2xl flex flex-col justify-end min-h-[17rem] sm:min-h-[20rem]">
                <div class="absolute inset-0 bg-gradient-to-t from-dark-bg via-dark-bg/60 to-black/30 z-10"></div>
                <img src="{{ asset('images/hero-gura.jpg') }}" alt="Gura Bicha Atmosphere" class="absolute inset-0 w-full h-full object-cover object-center filter brightness-75">
                
                <div class="relative z-20 p-4 sm:p-6 space-y-2 sm:space-y-3">
                    <!-- DUAL VIBE BADGES -->
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                        <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-gold/20 border border-gold/40 text-gold text-[10px] sm:text-xs font-semibold backdrop-blur-md">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold animate-ping"></span>
                            <span x-text="t('hero_acoustic_badge')">LIVE ACOUSTIC</span>
                        </div>
                        <div class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full bg-emerald-900/40 border border-emerald-500/30 text-emerald-300 text-[10px] sm:text-xs font-semibold backdrop-blur-md">
                            <span x-text="t('hero_football_badge')">⚽ 4K MATCHDAYS</span>
                        </div>
                    </div>

                    <h2 class="font-display text-xl sm:text-3xl font-bold uppercase text-white tracking-wide leading-tight">
                        <span x-text="t('hero_title_1')">Game & Groove</span> <br class="hidden sm:inline"><span class="gold-gradient-text" x-text="t('hero_title_2')">Mexico · Addis Ababa</span>
                    </h2>

                    <div class="flex flex-wrap items-center gap-2 sm:gap-2.5 pt-1">
                        <button @click="openReservationWizard()" class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl bg-gold hover:bg-gold-light text-black font-bold text-xs sm:text-sm transition shadow-lg shadow-gold/25 flex items-center justify-center space-x-1.5 transform active:scale-95">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/></svg>
                            <span x-text="t('hero_reserve_btn')">Reserve Table</span>
                        </button>
                        <button @click="setScreen('fixtures')" class="px-3.5 py-2.5 rounded-xl bg-dark-panel hover:bg-zinc-800 border border-dark-border hover:border-gold/50 text-white font-medium text-xs sm:text-sm transition flex items-center justify-center space-x-1.5 shrink-0">
                            <span x-text="t('hero_fixtures_btn')">⚽ Fixtures</span>
                        </button>
                        <button @click="setScreen('fanclub')" class="px-3.5 py-2.5 rounded-xl bg-dark-panel hover:bg-zinc-800 border border-dark-border text-gold font-bold text-xs sm:text-sm transition flex items-center justify-center space-x-1.5 shrink-0">
                            <span x-text="t('hero_fanclub_btn')">🔔 Alerts</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- CURRENT FIXTURES: HIGHLIGHTED MATCH CARD -->
            <div class="rounded-2xl border border-gold/30 bg-dark-card p-4 sm:p-5 relative overflow-hidden card-hover-lift shadow-xl" x-show="featuredMatch">
                <div class="flex items-center justify-between border-b border-dark-border/60 pb-3 mb-3">
                    <div class="flex items-center space-x-2 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" :class="isMatchLive(featuredMatch) ? 'bg-red-500 animate-ping' : 'bg-gold'"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-gold truncate" x-text="isMatchLive(featuredMatch) ? '🔴 LIVE MATCH' : 'FEATURED MATCH'"></span>
                        <span class="text-xs text-zinc-400">·</span>
                        <span class="text-xs text-zinc-300 font-medium truncate" x-text="featuredMatch ? (featuredMatch.competition + ' · ' + featuredMatch.stage) : ''"></span>
                    </div>
                    <span class="text-xs px-2.5 py-0.5 rounded-lg bg-dark-panel text-gold border border-gold/40 font-mono font-bold shrink-0 ml-2" x-text="featuredMatch ? formatKickoffTime(featuredMatch.kickoff) : ''"></span>
                </div>

                <div class="flex items-center justify-between py-2" x-show="featuredMatch">
                    <!-- Home Team with Crest -->
                    <div class="flex-1 min-w-0 flex flex-col items-center text-center px-1">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-dark-panel border border-gold/40 flex items-center justify-center p-2 mb-1.5 shadow-md relative overflow-hidden">
                            <img x-show="getTeamLogo(featuredMatch ? featuredMatch.homeTeam : '')" 
                                 :src="getTeamLogo(featuredMatch ? featuredMatch.homeTeam : '')" 
                                 :alt="featuredMatch ? featuredMatch.homeTeam : ''" 
                                 class="w-full h-full object-contain filter drop-shadow"
                                 x-on:error="$el.style.display='none'; if ($el.nextElementSibling) $el.nextElementSibling.style.display='flex'">
                            <span class="font-display font-bold text-gold text-base sm:text-lg items-center justify-center w-full h-full" 
                                  :style="getTeamLogo(featuredMatch ? featuredMatch.homeTeam : '') ? 'display: none' : 'display: flex'" 
                                  x-text="teamInitials(featuredMatch ? featuredMatch.homeTeam : '')"></span>
                        </div>
                        <span class="text-xs sm:text-sm font-bold text-white block truncate max-w-full px-1" x-text="featuredMatch ? featuredMatch.homeTeam : ''"></span>
                    </div>

                    <!-- Versus / Score -->
                    <div class="px-3 sm:px-5 text-center shrink-0">
                        <template x-if="featuredMatch && (featuredMatch.homeScore !== null && featuredMatch.awayScore !== null)">
                            <div class="font-display text-xl sm:text-2xl font-bold text-gold px-3.5 py-1 bg-dark-panel rounded-xl border border-gold/40 shadow-inner">
                                <span x-text="featuredMatch.homeScore"></span> : <span x-text="featuredMatch.awayScore"></span>
                            </div>
                        </template>
                        <template x-if="!featuredMatch || featuredMatch.homeScore === null">
                            <div class="flex flex-col items-center">
                                <span class="font-display text-xl sm:text-2xl font-black text-gold tracking-widest px-2.5 py-0.5 rounded-lg bg-dark-panel border border-dark-border">VS</span>
                                <span class="text-[10px] text-zinc-400 mt-1 uppercase font-semibold" x-text="featuredMatch ? formatMatchDate(featuredMatch.kickoff) : ''"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Away Team with Crest -->
                    <div class="flex-1 min-w-0 flex flex-col items-center text-center px-1">
                        <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-dark-panel border border-gold/40 flex items-center justify-center p-2 mb-1.5 shadow-md relative overflow-hidden">
                            <img x-show="getTeamLogo(featuredMatch ? featuredMatch.awayTeam : '')" 
                                 :src="getTeamLogo(featuredMatch ? featuredMatch.awayTeam : '')" 
                                 :alt="featuredMatch ? featuredMatch.awayTeam : ''" 
                                 class="w-full h-full object-contain filter drop-shadow"
                                 x-on:error="$el.style.display='none'; if ($el.nextElementSibling) $el.nextElementSibling.style.display='flex'">
                            <span class="font-display font-bold text-gold text-base sm:text-lg items-center justify-center w-full h-full" 
                                  :style="getTeamLogo(featuredMatch ? featuredMatch.awayTeam : '') ? 'display: none' : 'display: flex'" 
                                  x-text="teamInitials(featuredMatch ? featuredMatch.awayTeam : '')"></span>
                        </div>
                        <span class="text-xs sm:text-sm font-bold text-white block truncate max-w-full px-1" x-text="featuredMatch ? featuredMatch.awayTeam : ''"></span>
                    </div>
                </div>

                <div class="mt-3 pt-3 border-t border-dark-border/60 flex items-center justify-between">
                    <span class="text-xs text-zinc-300 font-medium" x-text="featuredMatch ? formatMatchDate(featuredMatch.kickoff) : ''"></span>
                    <button @click="setScreen('fixtures')" class="px-3.5 py-1.5 rounded-xl bg-gold/15 hover:bg-gold text-gold hover:text-black font-bold text-xs transition border border-gold/40 flex items-center space-x-1">
                        <span>Current Fixtures</span>
                        <span>→</span>
                    </button>
                </div>
            </div>

            <!-- EVENTS: LIVE ACOUSTIC SESSIONS -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-display text-xl font-bold uppercase tracking-wider text-white">Live Acoustic Events</h3>
                        <p class="text-xs text-zinc-300">Live band sessions (7:00 PM – 11:30 PM)</p>
                    </div>
                    <button @click="openReservationWizard()" class="text-xs text-gold hover:underline font-semibold flex items-center space-x-1">
                        <span>Book Table</span>
                        <span>→</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <template x-for="event in events" :key="event.id">
                        <div class="rounded-2xl border border-dark-border bg-dark-card overflow-hidden card-hover-lift">
                            <div class="h-40 relative overflow-hidden">
                                <img :src="event.imageUrl || '{{ asset('images/event-acoustic.jpg') }}'" :alt="event.title" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-dark-card via-dark-card/40 to-transparent"></div>
                                <div class="absolute top-3 left-3 flex items-center space-x-1.5">
                                    <span class="px-2.5 py-0.5 rounded-lg bg-gold text-black text-xs font-bold uppercase tracking-wider" x-text="event.category"></span>
                                    <span class="px-2 py-0.5 rounded-lg bg-black/70 backdrop-blur-md text-gold text-[10px] font-semibold border border-gold/30" x-text="formatDateShort(event.date)"></span>
                                </div>
                            </div>
                            <div class="p-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <h4 class="font-display text-lg font-bold text-white uppercase" x-text="event.title"></h4>
                                    <span class="text-xs text-gold font-semibold" x-text="event.startTime + ' – ' + event.endTime"></span>
                                </div>
                                <p class="text-xs text-zinc-300 line-clamp-2" x-text="event.description"></p>
                                <div class="pt-2 flex items-center justify-between border-t border-dark-border/60">
                                    <span class="text-xs text-zinc-300 font-medium" x-text="formatDateShort(event.date)"></span>
                                    <button @click="openReservationWizard(event.date)" class="px-3.5 py-1.5 rounded-xl bg-gold hover:bg-gold-light text-black text-xs font-bold transition shadow-sm">
                                        Reserve Table
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ABOUT GURA BICHA SPORTS BAR & LOUNGE (WITH ENHANCED ANIMATIONS) -->
            <div class="p-5 sm:p-6 rounded-3xl bg-gradient-to-br from-dark-card via-dark-card to-amber-950/20 border border-gold/40 shadow-2xl relative overflow-hidden space-y-4 gold-glow-pulse">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Gura Bicha Emblem" class="w-12 h-12 rounded-2xl object-contain border border-gold/50 bg-black p-0.5 shadow-lg shadow-gold/20 animate-float shrink-0">
                    <div>
                        <span class="text-[10px] font-bold text-gold uppercase tracking-widest block">VIP EXPERIENCE</span>
                        <h3 class="font-display text-xl sm:text-2xl font-bold uppercase text-white tracking-wide">
                            About Gura Bicha Sports Bar
                        </h3>
                    </div>
                </div>

                <p class="text-xs sm:text-sm text-zinc-300 leading-relaxed">
                    Located in Addis Ababa at Mexico inside the Chamber of Commerce Building, <strong>Gura Bicha</strong> brings together the thrills of <strong>4K stadium matchday screenings</strong> and soulful <strong>Live Acoustic music sessions</strong>.
                </p>

                <!-- 4 FEATURE HIGHLIGHT CARDS -->
                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div class="p-3.5 rounded-2xl bg-dark-panel/90 border border-dark-border hover:border-gold/50 transition-all card-hover-lift">
                        <div class="w-9 h-9 rounded-xl bg-gold/15 text-gold flex items-center justify-center text-lg mb-2 shadow-inner">
                            ⚽
                        </div>
                        <h4 class="font-bold text-xs text-white uppercase tracking-wider">4K Stadium Screens</h4>
                        <p class="text-[11px] text-zinc-400 mt-1 leading-snug">Multi-angle ultra HD screens & arena sound for Premier League & Champions League.</p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-dark-panel/90 border border-dark-border hover:border-gold/50 transition-all card-hover-lift">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/15 text-gold flex items-center justify-center text-lg mb-2 shadow-inner">
                            🎸
                        </div>
                        <h4 class="font-bold text-xs text-white uppercase tracking-wider">Live Acoustic Nights</h4>
                        <p class="text-[11px] text-zinc-400 mt-1 leading-snug">Soulful Saturday acoustic band performances, reserved seating & cozy lounge vibes.</p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-dark-panel/90 border border-dark-border hover:border-gold/50 transition-all card-hover-lift">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center text-lg mb-2 shadow-inner">
                            🍔
                        </div>
                        <h4 class="font-bold text-xs text-white uppercase tracking-wider">Kitchen & Grill</h4>
                        <p class="text-[11px] text-zinc-400 mt-1 leading-snug">Juicy burgers, wood-style pizzas, wings, and authentic Ethiopian culinary specialties.</p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-dark-panel/90 border border-dark-border hover:border-gold/50 transition-all card-hover-lift">
                        <div class="w-9 h-9 rounded-xl bg-sky-500/15 text-sky-400 flex items-center justify-center text-lg mb-2 shadow-inner">
                            🍺
                        </div>
                        <h4 class="font-bold text-xs text-white uppercase tracking-wider">Full Bar & Draft</h4>
                        <p class="text-[11px] text-zinc-400 mt-1 leading-snug">Cold draft beers, local and imported wines, and handcrafted signature cocktails.</p>
                    </div>
                </div>
            </div>

            <!-- LOCATION, MAP & CONTACT SECTION -->
            <div class="p-5 rounded-2xl bg-dark-card border border-dark-border space-y-4">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="text-gold font-bold text-xs uppercase tracking-wider">📍 FIND US IN ADDIS ABABA</span>
                        </div>
                        <h3 class="font-display text-xl font-bold uppercase text-white mt-0.5">Mexico, Inside Chamber of Commerce Building</h3>
                        <p class="text-xs text-zinc-300 mt-1">Directly accessible in Mexico square, Addis Ababa, Ethiopia. Free secure parking.</p>
                    </div>
                    <a href="https://maps.app.goo.gl/CK9ZByHcwZeUaHDK6" target="_blank" class="px-3 py-1.5 rounded-xl bg-gold/15 hover:bg-gold text-gold hover:text-black text-xs font-semibold border border-gold/30 transition shrink-0">
                        Get Directions
                    </a>
                </div>

                <!-- EMBEDDED INTERACTIVE GOOGLE MAP -->
                <div class="w-full h-52 rounded-xl overflow-hidden border border-dark-border bg-zinc-900 shadow-inner">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3940.540118350811!2d38.7441584!3d9.0143899!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x164b8591e5c54c33%3A0xb1a9c37e6f851eb!2sAddis%20Ababa%20Chamber%20of%20Commerce!5e0!3m2!1sen!2set!4v1700000000000" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-1 text-xs">
                    <div class="p-2.5 rounded-lg bg-dark-panel border border-dark-border">
                        <span class="text-zinc-400 block text-[10px] uppercase font-semibold">Hours:</span>
                        <span class="text-white font-medium">Daily 12:00 PM – 2:00 AM</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-dark-panel border border-dark-border">
                        <span class="text-zinc-400 block text-[10px] uppercase font-semibold">Phone:</span>
                        <a href="tel:+251905888724" class="text-gold font-bold hover:underline">+251905888724</a>
                    </div>
                    <div class="p-2.5 rounded-lg bg-dark-panel border border-dark-border col-span-2 sm:col-span-1">
                        <span class="text-zinc-400 block text-[10px] uppercase font-semibold">Acoustic Sessions:</span>
                        <span class="text-emerald-400 font-bold">Saturdays 7:00 PM</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- SCREEN 2: SIMPLE & CURRENT-TIME FOOTBALL FIXTURES -->
        <!-- ========================================== -->
        <div x-show="screen === 'fixtures'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
            
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">LIVE INTERNET SCOREBOARD</span>
                    </div>
                    <h2 class="font-display text-2xl font-bold uppercase text-white tracking-wide" x-text="t('fixtures_title')">
                        Current Football Fixtures
                    </h2>
                    <p class="text-xs text-zinc-300" x-text="t('fixtures_subtitle')">Live screening & upcoming Premier League / Champions League matches (Addis Ababa EAT)</p>
                </div>

                <!-- QUICK REFRESH BUTTON -->
                <button @click="loadMatches()" class="px-3 py-2 rounded-xl bg-dark-panel hover:bg-zinc-800 border border-dark-border text-gold hover:border-gold/50 transition flex items-center space-x-1.5 text-xs font-semibold shrink-0 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span class="hidden sm:inline">Refresh</span>
                </button>
            </div>

            <!-- DYNAMIC NEXT KICKOFF / LIVE BANNER -->
            <template x-if="featuredMatch">
                <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-r from-purple-950/40 via-dark-card to-amber-950/30 border border-purple-500/40 flex items-center justify-between text-xs shadow-md">
                    <div class="flex items-center space-x-3">
                        <span class="text-xl">⏳</span>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-purple-300 tracking-wider block" x-text="t('next_kickoff_in')">NEXT BIG KICKOFF IN</span>
                            <span class="font-mono font-bold text-base text-gold" x-text="kickoffCountdownText"></span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-zinc-400 block uppercase font-medium" x-text="featuredMatch.competition + ' · ' + (featuredMatch.stage || 'Matchday')"></span>
                        <span class="text-zinc-100 font-bold text-xs sm:text-sm" x-text="featuredMatch.homeTeam + ' vs ' + featuredMatch.awayTeam"></span>
                    </div>
                </div>
            </template>


            <!-- SIMPLE STATUS / PERIOD TABS -->
            <div class="flex items-center space-x-2 overflow-x-auto pb-1 no-scrollbar">
                <button @click="fixtureTab = 'ALL'"
                        :class="fixtureTab === 'ALL' ? 'bg-gold text-black font-bold shadow-md shadow-gold/20' : 'bg-dark-panel text-zinc-300 border border-dark-border hover:border-gold/40'"
                        class="px-3.5 py-2 rounded-xl text-xs transition shrink-0 font-medium">
                    <span x-text="t('tab_all_fixtures')">🔥 All Current</span> (<span x-text="allMatches.length"></span>)
                </button>
                <button @click="fixtureTab = 'TODAY'"
                        :class="fixtureTab === 'TODAY' ? 'bg-red-600 text-white font-bold ring-2 ring-red-400' : 'bg-dark-panel text-zinc-300 border border-dark-border hover:border-red-400'"
                        class="px-3.5 py-2 rounded-xl text-xs transition shrink-0 flex items-center space-x-1.5 font-medium">
                    <span class="w-2 h-2 rounded-full bg-red-400 animate-pulse"></span>
                    <span x-text="t('tab_today_fixtures')">🔴 Today & Live</span>
                </button>
                <button @click="fixtureTab = 'SATURDAY'"
                        :class="fixtureTab === 'SATURDAY' ? 'bg-gold text-black font-bold shadow-md shadow-gold/20' : 'bg-dark-panel text-zinc-300 border border-dark-border hover:border-gold/40'"
                        class="px-3.5 py-2 rounded-xl text-xs transition shrink-0 flex items-center space-x-1.5 font-medium">
                    <span>🎸</span>
                    <span x-text="t('tab_saturday_fixtures')">Saturday Specials</span>
                </button>
                <button @click="fixtureTab = 'RESULTS'"
                        :class="fixtureTab === 'RESULTS' ? 'bg-gold text-black font-bold shadow-md shadow-gold/20' : 'bg-dark-panel text-zinc-300 border border-dark-border hover:border-gold/40'"
                        class="px-3.5 py-2 rounded-xl text-xs transition shrink-0 font-medium">
                    <span x-text="t('tab_results_fixtures')">🏆 Recent Results</span>
                </button>
            </div>

            <!-- SEARCH & COMPETITIONS ROW -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1">
                <div class="relative flex-1">
                    <input type="text" x-model="teamSearch" :placeholder="t('search_club_placeholder')" class="w-full bg-dark-card border border-dark-border rounded-xl pl-9 pr-4 py-2 text-white text-xs focus:outline-none focus:border-gold">
                    <svg class="w-4 h-4 text-zinc-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="inline-flex p-1 rounded-xl bg-dark-panel border border-dark-border shrink-0 text-xs">
                    <button @click="matchFilter = 'ALL'" :class="matchFilter === 'ALL' ? 'bg-gold text-black font-bold' : 'text-zinc-400 hover:text-white'" class="px-2.5 py-1 rounded-lg transition font-medium">
                        All
                    </button>
                    <button @click="matchFilter = 'Premier League'" :class="matchFilter === 'Premier League' ? 'bg-gold text-black font-bold' : 'text-zinc-400 hover:text-white'" class="px-2.5 py-1 rounded-lg transition font-medium">
                        Premier League
                    </button>
                    <button @click="matchFilter = 'Champions League'" :class="matchFilter === 'Champions League' ? 'bg-gold text-black font-bold' : 'text-zinc-400 hover:text-white'" class="px-2.5 py-1 rounded-lg transition font-medium">
                        Champions League
                    </button>
                </div>
            </div>
            <!-- CLEAN, SIMPLE CURRENT MATCH CARDS LIST -->
            <div class="space-y-3">
                <template x-if="filteredMatches.length === 0">
                    <div class="text-center py-12 text-zinc-400 bg-dark-card rounded-2xl border border-dark-border space-y-2">
                        <span class="text-3xl block">⚽</span>
                        <p class="font-bold text-white text-sm" x-text="lang === 'am' ? 'ምንም ጨዋታ አልተገኘም' : 'No matches found for this filter.'"></p>
                        <p class="text-xs text-zinc-400" x-text="lang === 'am' ? 'እባክዎን ሌላ አማራጭ ወይም ፍለጋ ይሞክሩ' : 'Try switching tabs or clearing your search.'"></p>
                    </div>
                </template>

                <template x-for="match in filteredMatches" :key="match.id">
                    <div class="p-3 sm:p-4 rounded-2xl bg-dark-card border border-dark-border hover:border-gold/40 transition card-hover-lift space-y-2">
                        <!-- Top meta: Competition, Stage, Kickoff / Status -->
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center space-x-1.5 min-w-0">
                                <span class="px-2 py-0.5 rounded font-bold uppercase text-[10px] shrink-0"
                                      :class="match.competition === 'Premier League' ? 'bg-purple-900/60 text-purple-300 border border-purple-700/50' : 'bg-cyan-900/60 text-cyan-300 border border-cyan-700/50'"
                                      x-text="match.competition"></span>
                                <span class="text-zinc-400 font-medium truncate text-[11px]" x-text="match.stage"></span>
                                <template x-if="isMatchLive(match)">
                                    <span class="px-2 py-0.2 rounded-full bg-red-600 text-white font-black text-[9px] animate-pulse shrink-0">🔴 LIVE</span>
                                </template>
                            </div>
                            <div class="flex items-center space-x-1.5 shrink-0 ml-2">
                                <span class="text-zinc-300 font-semibold text-[11px]" x-text="formatMatchDate(match.kickoff)"></span>
                                <span class="text-gold font-bold font-mono text-xs" x-text="formatKickoffTime(match.kickoff)"></span>
                            </div>
                        </div>

                        <!-- Main Teams & Scoreboard -->
                        <div class="flex items-center justify-between py-1">
                            <!-- Home Team with Crest Logo -->
                            <div class="flex-1 min-w-0 flex items-center space-x-2 sm:space-x-2.5">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-dark-panel border border-dark-border flex items-center justify-center p-1 sm:p-1.5 shrink-0 shadow-sm relative overflow-hidden">
                                    <img x-show="getTeamLogo(match.homeTeam)" 
                                         :src="getTeamLogo(match.homeTeam)" 
                                         :alt="match.homeTeam" 
                                         class="w-full h-full object-contain filter drop-shadow" 
                                         loading="lazy"
                                         x-on:error="$el.style.display='none'; if ($el.nextElementSibling) $el.nextElementSibling.style.display='flex'">
                                    <span class="font-display text-xs font-bold text-gold items-center justify-center w-full h-full" 
                                          :style="getTeamLogo(match.homeTeam) ? 'display: none' : 'display: flex'" 
                                          x-text="teamInitials(match.homeTeam)"></span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-xs sm:text-sm font-bold text-white block truncate" x-text="match.homeTeam"></span>
                                    <template x-if="fanForm.favoriteClub && fanForm.favoriteClub === match.homeTeam">
                                        <span class="text-[9px] text-emerald-400 font-semibold flex items-center space-x-0.5"><span>⭐</span><span>Your Club</span></span>
                                    </template>
                                </div>
                            </div>

                            <!-- Score / Status -->
                            <div class="px-2 sm:px-3 text-center shrink-0">
                                <template x-if="match.homeScore !== null && match.awayScore !== null">
                                    <div class="px-2.5 sm:px-3 py-1 rounded-xl bg-dark-panel border border-gold/40 shadow-inner font-display font-extrabold text-xs sm:text-base text-gold">
                                        <span x-text="match.homeScore"></span> - <span x-text="match.awayScore"></span>
                                    </div>
                                </template>
                                <template x-if="match.homeScore === null || match.awayScore === null">
                                    <span class="text-[11px] sm:text-xs text-zinc-400 font-extrabold tracking-wider px-2 py-0.5 rounded bg-dark-panel border border-dark-border">VS</span>
                                </template>
                            </div>

                            <!-- Away Team with Crest Logo -->
                            <div class="flex-1 min-w-0 flex items-center justify-end space-x-2 sm:space-x-2.5 text-right">
                                <div class="min-w-0 flex-1">
                                    <span class="text-xs sm:text-sm font-bold text-white block truncate" x-text="match.awayTeam"></span>
                                    <template x-if="fanForm.favoriteClub && fanForm.favoriteClub === match.awayTeam">
                                        <span class="text-[9px] text-emerald-400 font-semibold flex items-center justify-end space-x-0.5"><span>Your Club</span><span>⭐</span></span>
                                    </template>
                                </div>
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-dark-panel border border-dark-border flex items-center justify-center p-1 sm:p-1.5 shrink-0 shadow-sm relative overflow-hidden">
                                    <img x-show="getTeamLogo(match.awayTeam)" 
                                         :src="getTeamLogo(match.awayTeam)" 
                                         :alt="match.awayTeam" 
                                         class="w-full h-full object-contain filter drop-shadow" 
                                         loading="lazy"
                                         x-on:error="$el.style.display='none'; if ($el.nextElementSibling) $el.nextElementSibling.style.display='flex'">
                                    <span class="font-display text-xs font-bold text-gold items-center justify-center w-full h-full" 
                                          :style="getTeamLogo(match.awayTeam) ? 'display: none' : 'display: flex'" 
                                          x-text="teamInitials(match.awayTeam)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SCREEN 3: RESERVE WIZARD (LIVE ACOUSTIC)   -->
        <!-- ========================================== -->
        <div x-show="screen === 'reserve'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
            
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-display text-xl sm:text-2xl font-bold uppercase text-white tracking-wide">
                        Table Reservation
                    </h2>
                    <p class="text-xs text-zinc-300">Live acoustic sessions · 7:00 PM – 11:30 PM</p>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full bg-dark-panel border border-dark-border text-gold font-semibold" x-text="'Step ' + wizardStep + ' of 4'"></span>
            </div>

            <!-- STEP 1: DATE & SESSION -->
            <div x-show="wizardStep === 1" class="space-y-4">
                <div class="p-4 sm:p-5 rounded-2xl bg-dark-card border border-dark-border space-y-4">
                    <label class="block text-sm font-semibold text-white flex items-center justify-between">
                        <span x-text="t('select_sat_date')">Select Date</span>
                        <span class="text-xs text-gold font-normal">Acoustic Sessions</span>
                    </label>

                    <!-- DATE INPUT WITH PROMINENT CALENDAR ICON & EXPLICIT CALENDAR PICKER BUTTON (ICON ONLY) -->
                    <div class="relative flex items-center">
                        <div class="absolute left-3.5 pointer-events-none text-gold">
                            <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input id="saturday-date-picker"
                               x-ref="saturdayDatePicker"
                               type="date"
                               x-model="reserveDraft.date"
                               :min="todayDateString"
                               @click="$el.showPicker ? $el.showPicker() : null"
                               @change="fetchTablesAvailability()"
                               class="w-full bg-dark-panel border border-dark-border rounded-xl pl-11 pr-12 py-3 text-white text-sm focus:outline-none focus:border-gold font-medium cursor-pointer">
                        
                        <button type="button"
                                @click="$refs.saturdayDatePicker?.showPicker ? $refs.saturdayDatePicker.showPicker() : $refs.saturdayDatePicker?.focus()"
                                aria-label="Open Calendar"
                                title="Pick Date from Calendar"
                                class="absolute right-2 p-2 rounded-lg bg-gold hover:bg-gold-light text-black transition shadow flex items-center justify-center">
                            <svg class="w-4 h-4 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Quick Picks for Upcoming Acoustic Dates -->
                    <div class="pt-1">
                        <span class="text-xs text-zinc-300 block mb-2 font-medium" x-text="t('quick_pick_saturdays')">Available Dates:</span>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="item in upcomingSaturdays" :key="item.date">
                                <button @click="reserveDraft.date = item.date; fetchTablesAvailability()"
                                        :class="reserveDraft.date === item.date ? 'bg-gold text-black font-bold ring-2 ring-gold' : 'bg-dark-panel text-zinc-300 border border-dark-border hover:border-gold/40'"
                                        class="px-3 py-1.5 rounded-xl text-xs transition flex items-center space-x-1.5 font-medium">
                                    <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span x-text="item.label"></span>
                                    <span x-show="item.tag" class="ml-1 text-[9px] px-1.5 py-0.5 rounded bg-gold/20 text-gold font-bold uppercase" x-text="item.tag"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="p-4 sm:p-5 rounded-2xl bg-dark-card border border-dark-border space-y-3">
                    <label class="block text-sm font-semibold text-white" x-text="t('session_time')">Session Time</label>
                    <div class="p-3.5 rounded-xl border border-gold/40 bg-gold/10 text-left transition flex items-center justify-between">
                        <div>
                            <span class="block text-sm font-bold text-gold" x-text="t('sat_evening_groove')">Evening Acoustic Session</span>
                            <span class="text-xs text-zinc-300" x-text="t('sat_groove_time')">7:00 PM – 11:30 PM · Live band starts 8:00 PM</span>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded bg-gold text-black font-bold" x-text="t('selected_badge')">SELECTED</span>
                    </div>
                </div>

                <button @click="goToStep(2)" :disabled="!reserveDraft.date" class="w-full py-3 rounded-xl bg-gold hover:bg-gold-light disabled:opacity-40 disabled:cursor-not-allowed text-black font-bold text-sm transition shadow-lg shadow-gold/20" x-text="t('continue_tables')">
                    Continue to Table Selection
                </button>
            </div>

            <!-- STEP 2: INTERACTIVE TABLE MAP (ACOUSTIC ZONES) -->
            <div x-show="wizardStep === 2" class="space-y-4">
                <div class="p-5 rounded-2xl bg-dark-card border border-dark-border space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Acoustic Stage & Seating Map</h3>
                            <p class="text-xs text-zinc-300" x-text="remainingTablesCount + ' of 6 tables available for ' + formatDateShort(reserveDraft.date)"></p>
                        </div>
                        <button @click="fetchTablesAvailability()" class="text-xs text-gold hover:underline flex items-center space-x-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Refresh</span>
                        </button>
                    </div>

                    <!-- ACOUSTIC STAGE BAR -->
                    <div class="py-2.5 px-4 rounded-xl bg-gradient-to-r from-amber-500/20 via-gold/30 to-amber-500/20 border border-gold/40 text-center text-xs font-bold text-gold tracking-widest uppercase shadow-md flex items-center justify-center space-x-2">
                        <span>🎸 LIVE ACOUSTIC STAGE & SOUNDBOARD 🎶</span>
                    </div>

                    <!-- 6-TABLE GRID -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <template x-for="t in tables" :key="t.code">
                            <div @click="t.available && selectDraftTable(t)" :class="{
                                'border-gold bg-gold/20 text-white ring-2 ring-gold cursor-pointer shadow-lg shadow-gold/20': reserveDraft.table && reserveDraft.table.code === t.code,
                                'border-dark-border bg-dark-panel hover:border-gold/60 cursor-pointer': t.available && (!reserveDraft.table || reserveDraft.table.code !== t.code),
                                'border-zinc-800 bg-zinc-950/60 opacity-40 cursor-not-allowed': !t.available
                            }" class="p-3.5 rounded-xl border text-center transition relative card-hover-lift">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-display font-bold text-lg" x-text="t.code"></span>
                                    <span class="text-[10px] px-2 py-0.5 rounded font-bold" :class="t.available ? 'bg-emerald-900/60 text-emerald-300' : 'bg-red-900/60 text-red-300'" x-text="t.available ? 'FREE' : 'BOOKED'"></span>
                                </div>
                                <span class="text-xs font-medium block text-zinc-200" x-text="t.zone"></span>
                                <span class="text-[11px] text-zinc-400 block mt-0.5" x-text="t.seats"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button @click="goToStep(1)" class="w-1/3 py-3 rounded-xl bg-dark-panel border border-dark-border text-zinc-300 font-semibold text-sm transition">
                        Back
                    </button>
                    <button @click="goToStep(3)" :disabled="!reserveDraft.table" class="w-2/3 py-3 rounded-xl bg-gold hover:bg-gold-light disabled:opacity-40 disabled:cursor-not-allowed text-black font-bold text-sm transition shadow-lg shadow-gold/20">
                        Continue to Guests
                    </button>
                </div>
            </div>

            <!-- STEP 3: GUEST COUNT -->
            <div x-show="wizardStep === 3" class="space-y-4">
                <div class="p-5 rounded-2xl bg-dark-card border border-dark-border space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-white">Number of Guests</h3>
                        <p class="text-xs text-zinc-300" x-text="'Table ' + (reserveDraft.table ? reserveDraft.table.code : '') + ' accommodates up to ' + (reserveDraft.table ? reserveDraft.table.capacity : 6) + ' guests'"></p>
                    </div>

                    <div class="flex items-center justify-center space-x-6 py-4">
                        <button @click="reserveDraft.guests = Math.max(1, reserveDraft.guests - 1)" class="w-12 h-12 rounded-xl bg-dark-panel border border-dark-border text-white text-xl font-bold hover:border-gold transition">
                            -
                        </button>
                        <span class="font-display text-4xl font-bold text-gold" x-text="reserveDraft.guests"></span>
                        <button @click="reserveDraft.guests = Math.min(reserveDraft.table ? reserveDraft.table.capacity : 12, reserveDraft.guests + 1)" class="w-12 h-12 rounded-xl bg-dark-panel border border-dark-border text-white text-xl font-bold hover:border-gold transition">
                            +
                        </button>
                    </div>

                    <div class="flex items-center justify-center space-x-2">
                        <template x-for="count in [2, 4, 6, 8]" :key="count">
                            <button @click="if (count <= (reserveDraft.table ? reserveDraft.table.capacity : 12)) reserveDraft.guests = count" :disabled="count > (reserveDraft.table ? reserveDraft.table.capacity : 12)" :class="reserveDraft.guests === count ? 'bg-gold text-black font-bold' : 'bg-dark-panel text-zinc-300 border border-dark-border disabled:opacity-20'" class="px-3.5 py-1.5 rounded-lg text-xs transition">
                                <span x-text="count + ' guests'"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button @click="goToStep(2)" class="w-1/3 py-3 rounded-xl bg-dark-panel border border-dark-border text-zinc-300 font-semibold text-sm transition">
                        Back
                    </button>
                    <button @click="goToStep(4)" class="w-2/3 py-3 rounded-xl bg-gold hover:bg-gold-light text-black font-bold text-sm transition shadow-lg shadow-gold/20">
                        Continue to Details
                    </button>
                </div>
            </div>

            <!-- STEP 4: GUEST DETAILS & SUBMISSION -->
            <div x-show="wizardStep === 4" class="space-y-4">
                <div class="p-5 rounded-2xl bg-dark-card border border-dark-border space-y-3.5">
                    <h3 class="text-sm font-semibold text-white">Guest Contact Details</h3>

                    <div>
                        <label class="block text-xs text-zinc-300 font-semibold mb-1">Full Name *</label>
                        <input type="text" x-model="reserveForm.name" placeholder="Abebe Kebede" class="w-full bg-dark-panel border border-dark-border rounded-xl px-3.5 py-2.5 text-white text-sm focus:outline-none focus:border-gold">
                    </div>

                    <div>
                        <label class="block text-xs text-zinc-300 font-semibold mb-1">Phone Number (Ethiopian Mobile) *</label>
                        <input type="tel" x-model="reserveForm.phone" placeholder="+251905888724" class="w-full bg-dark-panel border border-dark-border rounded-xl px-3.5 py-2.5 text-white text-sm focus:outline-none focus:border-gold">
                    </div>

                    <div>
                        <label class="block text-xs text-zinc-300 font-semibold mb-1">Email Address (Optional)</label>
                        <input type="email" x-model="reserveForm.email" placeholder="abebe@example.com" class="w-full bg-dark-panel border border-dark-border rounded-xl px-3.5 py-2.5 text-white text-sm focus:outline-none focus:border-gold">
                    </div>

                    <div>
                        <label class="block text-xs text-zinc-300 font-semibold mb-1">Special Requests (Optional)</label>
                        <textarea x-model="reserveForm.request" rows="2" placeholder="Birthday celebration, dietary preference, stage view..." class="w-full bg-dark-panel border border-dark-border rounded-xl px-3.5 py-2.5 text-white text-sm focus:outline-none focus:border-gold"></textarea>
                    </div>

                    <div class="flex items-center space-x-2 pt-1">
                        <input type="checkbox" id="saveDetails" x-model="reserveForm.saveDetails" class="rounded border-dark-border text-gold focus:ring-gold bg-dark-panel">
                        <label for="saveDetails" class="text-xs text-zinc-300">Remember my details for faster booking</label>
                    </div>

                    <div x-show="errorMessage" class="p-3 rounded-xl bg-red-950/60 border border-red-800 text-red-200 text-xs" x-text="errorMessage"></div>
                </div>

                <div class="flex items-center space-x-3">
                    <button @click="goToStep(3)" class="w-1/3 py-3 rounded-xl bg-dark-panel border border-dark-border text-zinc-300 font-semibold text-sm transition">
                        Back
                    </button>
                    <button @click="submitReservation()" :disabled="submitting" class="w-2/3 py-3 rounded-xl bg-gold hover:bg-gold-light text-black font-bold text-sm transition flex items-center justify-center space-x-2 shadow-lg shadow-gold/25">
                        <span x-show="!submitting">Confirm Table Booking</span>
                        <span x-show="submitting">Processing...</span>
                    </button>
                </div>
            </div>

            <!-- STEP 5: CONFIRMATION SUCCESS -->
            <div x-show="wizardStep === 5" class="space-y-4 text-center">
                <div class="p-6 rounded-2xl bg-dark-card border border-gold/40 shadow-2xl space-y-4">
                    <div class="w-16 h-16 rounded-full bg-gold/20 border border-gold flex items-center justify-center mx-auto text-gold shadow-lg shadow-gold/20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>

                    <div>
                        <span class="text-xs font-semibold text-gold tracking-widest uppercase">ACOUSTIC NIGHT CONFIRMED</span>
                        <h3 class="font-display text-3xl font-bold text-white mt-1">Booking Confirmed!</h3>
                        <p class="text-xs text-zinc-300 mt-1">Your table has been reserved at Gura Bicha, Mexico Chamber of Commerce Bldg.</p>
                    </div>

                    <!-- BOOKING CODE CARD -->
                    <div class="p-4 rounded-xl bg-dark-panel border border-gold/40 inline-block w-full">
                        <span class="text-xs text-zinc-300 block uppercase tracking-wider font-semibold">Your Booking Code</span>
                        <span class="font-display text-3xl font-bold text-gold tracking-widest block my-1" x-text="confirmedReservation ? confirmedReservation.code : ''"></span>
                        <span class="text-[11px] text-zinc-400">Show this code or your phone number upon arrival.</span>
                    </div>

                    <!-- RESERVATION SUMMARY -->
                    <div class="text-left text-xs space-y-2 p-4 rounded-xl bg-dark-panel border border-dark-border text-zinc-200">
                        <div class="flex justify-between">
                            <span class="text-zinc-400">Date:</span>
                            <span class="font-semibold text-white" x-text="confirmedReservation ? formatDateLong(confirmedReservation.date) : ''"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-400">Session:</span>
                            <span class="font-semibold text-gold">Evening Acoustic Session (7:00 PM – 11:30 PM)</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-400">Table & Party:</span>
                            <span class="font-semibold text-white" x-text="confirmedReservation ? ('Table ' + confirmedReservation.tableCode + ' · ' + confirmedReservation.guests + ' Guests') : ''"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-400">Location:</span>
                            <span class="font-semibold text-white">Mexico, Chamber of Commerce Bldg</span>
                        </div>
                    </div>

                    <!-- CALENDAR & SHARING ACTIONS -->
                    <div class="grid grid-cols-2 gap-2 pt-2">
                        <a :href="'{{ url('/ics') }}/' + (confirmedReservation ? confirmedReservation.code : '')" class="py-2.5 px-3 rounded-xl bg-dark-panel hover:bg-dark-border text-white text-xs font-semibold border border-dark-border transition flex items-center justify-center space-x-1.5">
                            <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Download .ICS</span>
                        </a>
                        <button @click="shareReservation(confirmedReservation)" class="py-2.5 px-3 rounded-xl bg-dark-panel hover:bg-dark-border text-white text-xs font-semibold border border-dark-border transition flex items-center justify-center space-x-1.5">
                            <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            <span>Share</span>
                        </button>
                    </div>

                    <div class="pt-2 flex items-center space-x-2">
                        <button @click="setScreen('home')" class="w-1/2 py-2.5 rounded-xl bg-dark-panel text-zinc-300 font-semibold text-xs hover:text-white transition">
                            Back to Home
                        </button>
                        <button @click="setScreen('reservations')" class="w-1/2 py-2.5 rounded-xl bg-gold text-black font-bold text-xs hover:bg-gold-light transition">
                            My Reservations
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- SCREEN 4: ENHANCED FOOD & BAR MENU         -->
        <!-- ========================================== -->
        <div x-show="screen === 'menu'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
            
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-display text-2xl font-bold uppercase text-white tracking-wide">
                        Food & Bar Menu
                    </h2>
                    <p class="text-xs text-zinc-300">GB Gura Bicha — Good Food • Great Drinks • Best Vibes</p>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- View Official Menu Card -->
                    <button @click="showMenuBoardModal = true" class="px-3.5 py-2 rounded-xl bg-gold/15 hover:bg-gold text-gold hover:text-black border border-gold/40 text-xs font-semibold flex items-center space-x-1.5 transition">
                        <span>📜 Menu Board</span>
                    </button>
                </div>
            </div>

            <!-- SCANNED TABLE GREETING (IF ACCESSED VIA PHYSICAL TABLE QR) -->
            <div x-show="activeTableCode" class="p-3.5 rounded-2xl bg-gradient-to-r from-gold/20 via-dark-card to-dark-card border border-gold/50 text-xs text-white flex items-center justify-between shadow-lg">
                <div class="flex items-center space-x-2.5">
                    <span class="px-2.5 py-1 rounded-lg bg-black text-gold font-mono font-bold border border-gold/40" x-text="'TABLE ' + activeTableCode"></span>
                    <div>
                        <span class="font-bold text-gold block">Welcome to Table <span x-text="activeTableCode"></span>!</span>
                        <span class="text-zinc-300 text-[11px]">Browse our food & drinks selection below or ask your server for today's specials.</span>
                    </div>
                </div>
                <button @click="activeTableCode = null" class="text-zinc-400 hover:text-white p-1">✕</button>
            </div>

            <!-- SEARCH BAR & SORT SELECTOR -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                <div class="relative sm:col-span-2">
                    <input type="text" x-model="menuSearch" placeholder="Search Cheese Burger, Wings, Pizza, Heineken Draft, Mojito..." class="w-full bg-dark-card border border-dark-border rounded-xl pl-9 pr-4 py-2.5 text-white text-sm focus:outline-none focus:border-gold">
                    <svg class="w-4 h-4 text-zinc-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div>
                    <select x-model="menuSort" class="w-full bg-dark-card border border-dark-border rounded-xl px-3 py-2.5 text-white text-xs focus:outline-none focus:border-gold">
                        <option value="popular">★ Sort: Popular First</option>
                        <option value="price_asc">Price: Low to High</option>
                        <option value="price_desc">Price: High to Low</option>
                    </select>
                </div>
            </div>

            <!-- MAIN CATEGORY FILTER PILLS -->
            <div class="flex items-center space-x-2 overflow-x-auto pb-1">
                <button @click="menuCategory = 'ALL'; menuSubCategory = 'ALL'" :class="menuCategory === 'ALL' ? 'bg-gold text-black font-bold shadow-md shadow-gold/20' : 'bg-dark-panel text-zinc-300 border border-dark-border hover:border-gold/40'" class="px-3.5 py-1.5 rounded-xl text-xs shrink-0 transition">
                    All (<span x-text="menuItems.length"></span>)
                </button>
                <button @click="menuCategory = 'FOOD'; menuSubCategory = 'ALL'" :class="menuCategory === 'FOOD' ? 'bg-gold text-black font-bold shadow-md shadow-gold/20' : 'bg-dark-panel text-zinc-300 border border-dark-border hover:border-gold/40'" class="px-3.5 py-1.5 rounded-xl text-xs shrink-0 transition">
                    🍔 Food (<span x-text="menuItems.filter(i => i.category === 'FOOD').length"></span>)
                </button>
                <button @click="menuCategory = 'PIZZA'; menuSubCategory = 'ALL'" :class="menuCategory === 'PIZZA' ? 'bg-gold text-black font-bold shadow-md shadow-gold/20' : 'bg-dark-panel text-zinc-300 border border-dark-border hover:border-gold/40'" class="px-3.5 py-1.5 rounded-xl text-xs shrink-0 transition">
                    🍕 Pizza (<span x-text="menuItems.filter(i => i.category === 'PIZZA').length"></span>)
                </button>
                <button @click="menuCategory = 'DRINKS'; menuSubCategory = 'ALL'" :class="menuCategory === 'DRINKS' ? 'bg-gold text-black font-bold shadow-md shadow-gold/20' : 'bg-dark-panel text-zinc-300 border border-dark-border hover:border-gold/40'" class="px-3.5 py-1.5 rounded-xl text-xs shrink-0 transition">
                    🍺 Beer & Drinks (<span x-text="menuItems.filter(i => i.category === 'DRINKS').length"></span>)
                </button>
            </div>

            <!-- SUBCATEGORY FILTER CHIPS -->
            <div x-show="menuCategory === 'FOOD'" class="flex items-center space-x-2 overflow-x-auto pb-1 text-xs">
                <button @click="menuSubCategory = 'ALL'" :class="menuSubCategory === 'ALL' ? 'bg-gold/20 text-gold border-gold font-semibold' : 'bg-dark-card text-zinc-400 border-dark-border'" class="px-3 py-1 rounded-lg border shrink-0 transition">
                    All Food
                </button>
                <button @click="menuSubCategory = 'BURGERS_SANDWICHES'" :class="menuSubCategory === 'BURGERS_SANDWICHES' ? 'bg-gold/20 text-gold border-gold font-semibold' : 'bg-dark-card text-zinc-400 border-dark-border'" class="px-3 py-1 rounded-lg border shrink-0 transition">
                    🍔 Burgers & Sandwiches
                </button>
                <button @click="menuSubCategory = 'WINGS'" :class="menuSubCategory === 'WINGS' ? 'bg-gold/20 text-gold border-gold font-semibold' : 'bg-dark-card text-zinc-400 border-dark-border'" class="px-3 py-1 rounded-lg border shrink-0 transition">
                    🍗 Wings
                </button>
                <button @click="menuSubCategory = 'BUFFET_LASAGNA'" :class="menuSubCategory === 'BUFFET_LASAGNA' ? 'bg-gold/20 text-gold border-gold font-semibold' : 'bg-dark-card text-zinc-400 border-dark-border'" class="px-3 py-1 rounded-lg border shrink-0 transition">
                    🍲 Buffet & Lasagna
                </button>
            </div>

            <div x-show="menuCategory === 'PIZZA'" class="flex items-center space-x-2 overflow-x-auto pb-1 text-xs">
                <button @click="menuSubCategory = 'ALL'" :class="menuSubCategory === 'ALL' ? 'bg-gold/20 text-gold border-gold font-semibold' : 'bg-dark-card text-zinc-400 border-dark-border'" class="px-3 py-1 rounded-lg border shrink-0 transition">
                    All Pizzas
                </button>
                <button @click="menuSubCategory = 'MARGHERITA'" :class="menuSubCategory === 'MARGHERITA' ? 'bg-gold/20 text-gold border-gold font-semibold' : 'bg-dark-card text-zinc-400 border-dark-border'" class="px-3 py-1 rounded-lg border shrink-0 transition">
                    🧀 Margherita
                </button>
                <button @click="menuSubCategory = 'TUNA'" :class="menuSubCategory === 'TUNA' ? 'bg-gold/20 text-gold border-gold font-semibold' : 'bg-dark-card text-zinc-400 border-dark-border'" class="px-3 py-1 rounded-lg border shrink-0 transition">
                    🐟 Tuna
                </button>
            </div>

            <div x-show="menuCategory === 'DRINKS'" class="flex items-center space-x-2 overflow-x-auto pb-1 text-xs">
                <button @click="menuSubCategory = 'ALL'" :class="menuSubCategory === 'ALL' ? 'bg-gold/20 text-gold border-gold font-semibold' : 'bg-dark-card text-zinc-400 border-dark-border'" class="px-3 py-1 rounded-lg border shrink-0 transition">
                    All Drinks
                </button>
                <button @click="menuSubCategory = 'BEERS'" :class="menuSubCategory === 'BEERS' ? 'bg-gold/20 text-gold border-gold font-semibold' : 'bg-dark-card text-zinc-400 border-dark-border'" class="px-3 py-1 rounded-lg border shrink-0 transition">
                    🍺 Beers & Draft
                </button>
                <button @click="menuSubCategory = 'COCKTAIL'" :class="menuSubCategory === 'COCKTAIL' ? 'bg-gold/20 text-gold border-gold font-semibold' : 'bg-dark-card text-zinc-400 border-dark-border'" class="px-3 py-1 rounded-lg border shrink-0 transition">
                    🍸 Fresh Mojito
                </button>
            </div>

            <!-- MENU GRID WITH HIGH QUALITY PHOTOS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <template x-for="item in filteredMenuItems" :key="item.id">
                    <div class="group rounded-2xl bg-dark-card border border-dark-border hover:border-gold/50 transition-all duration-300 overflow-hidden shadow-lg card-hover-lift flex flex-col justify-between">
                        <div>
                            <!-- Item Photo Header -->
                            <div class="relative h-44 w-full bg-zinc-900 overflow-hidden cursor-pointer" @click="previewItem = item">
                                <img :src="item.imageUrl || (item.category === 'FOOD' ? 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600&auto=format&fit=crop&q=80' : 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=600&auto=format&fit=crop&q=80')" 
                                     :alt="item.name" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                     loading="lazy"
                                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1544025162-d76694265947?w=600&auto=format&fit=crop&q=80';">
                                <div class="absolute inset-0 bg-gradient-to-t from-dark-card via-black/30 to-black/10"></div>

                                <!-- Category & Popular Badges -->
                                <div class="absolute top-3 left-3 flex items-center space-x-1.5">
                                    <span class="px-2 py-0.5 rounded-md bg-black/70 backdrop-blur-md text-gold text-[10px] font-bold uppercase tracking-wider border border-gold/30" x-text="item.category"></span>
                                    <template x-if="item.popular">
                                        <span class="px-2 py-0.5 rounded-md bg-amber-500 text-black text-[10px] font-extrabold shadow-md">★ POPULAR</span>
                                    </template>
                                </div>

                                <!-- Price Badge -->
                                <div class="absolute bottom-3 right-3">
                                    <span class="font-display font-extrabold text-base text-gold bg-black/80 backdrop-blur-md px-2.5 py-1 rounded-lg border border-gold/40" x-text="item.price + ' ETB'"></span>
                                </div>
                            </div>

                            <!-- Details Body -->
                            <div class="p-4 space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="font-semibold text-base text-white group-hover:text-gold transition cursor-pointer" @click="previewItem = item" x-text="item.name"></h4>
                                </div>
                                <p class="text-xs text-zinc-300 line-clamp-2 leading-relaxed" x-text="item.description"></p>

                                <!-- Tag Chips -->
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    <template x-for="tag in getItemTags(item)" :key="tag">
                                        <span class="text-[10px] px-2 py-0.5 rounded bg-dark-panel border border-dark-border text-zinc-300" x-text="tag"></span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="p-4 pt-2 border-t border-dark-border/60 flex items-center justify-between">
                            <span class="font-display font-bold text-sm text-gold" x-text="item.price + ' ETB'"></span>
                            <button @click="previewItem = item" class="px-3.5 py-1.5 rounded-xl bg-gold/15 hover:bg-gold text-gold hover:text-black border border-gold/40 text-xs font-semibold transition flex items-center space-x-1">
                                <span>Details</span>
                                <span>→</span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- SCREEN 5: FAN CLUB & MATCHDAY ALERTS       -->
        <!-- ========================================== -->
        <div x-show="screen === 'fanclub'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
            
            <div class="p-6 rounded-3xl bg-gradient-to-br from-emerald-950/40 via-dark-card to-dark-card border border-emerald-500/40 shadow-xl space-y-3">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-900/60 border border-emerald-500/50 flex items-center justify-center text-2xl shadow-inner">
                        ⚽
                    </div>
                    <div>
                        <span class="text-[10px] font-bold tracking-widest text-emerald-400 uppercase">FAN CLUB</span>
                        <h2 class="font-display text-xl sm:text-2xl font-bold uppercase text-white tracking-wide">
                            Matchday Alerts
                        </h2>
                    </div>
                </div>
                <p class="text-xs text-zinc-300">
                    Get live matchday alerts and score updates directly on Telegram.
                </p>
            </div>

            <!-- SUCCESS CONFIRMATION BADGE -->
            <div x-show="fanSaved" class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500 text-emerald-100 text-xs flex items-center space-x-3 shadow-xl">
                <span class="text-2xl">🎉</span>
                <div class="flex-1">
                    <span class="font-bold text-white text-sm block">Welcome to the Fan Club!</span>
                    <span class="text-emerald-300 text-[11px]" x-text="'Registered as a ' + (fanForm.favoriteClub || 'club') + ' supporter! A welcome notification has been sent to your Telegram chat.'"></span>
                </div>
            </div>

            <!-- FAN PROFILE REGISTRATION FORM -->
            <div class="p-4 sm:p-5 rounded-2xl bg-dark-card border border-dark-border space-y-4 shadow-xl">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Your Fan Profile</h3>
                    <template x-if="isTelegramUser">
                        <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full bg-sky-500/20 border border-sky-400/40 text-sky-400 text-[10px] font-semibold">
                            <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.46c.537-.195 1.006.128.832.926z"/></svg>
                            <span x-text="'Telegram: ' + (telegramUsername || 'Patron')"></span>
                        </span>
                    </template>
                </div>

                <!-- TELEGRAM ACCOUNT STATUS -->
                <template x-if="isTelegramUser">
                    <div class="p-3 rounded-xl bg-sky-950/40 border border-sky-500/30 text-sky-200 text-xs flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg">✈️</span>
                            <div>
                                <span class="font-bold text-white block">Connected via Telegram</span>
                                <span class="text-[11px] text-zinc-300" x-text="'Notifications will be delivered to ' + (telegramUsername || 'your chat')"></span>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-400 text-[10px] font-bold border border-emerald-500/40">Active</span>
                    </div>
                </template>

                <!-- CLUB SELECTION CAROUSEL -->
                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-2">Select Your Football Club *</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <template x-for="club in popularClubs" :key="club.name">
                            <button type="button" @click="fanForm.favoriteClub = club.name" 
                                    :class="fanForm.favoriteClub === club.name ? 'border-gold bg-gold/15 text-gold ring-2 ring-gold' : 'border-dark-border bg-dark-panel text-zinc-300 hover:border-gold/50'"
                                    class="p-2 sm:p-2.5 rounded-xl border text-center transition flex flex-col items-center space-y-1">
                                <div class="w-8 h-8 flex items-center justify-center relative overflow-hidden">
                                    <img x-show="club.logo" :src="club.logo" :alt="club.name" class="w-full h-full object-contain filter drop-shadow" x-on:error="$el.style.display='none'; if ($el.nextElementSibling) $el.nextElementSibling.style.display='flex'">
                                    <span class="text-sm items-center justify-center w-full h-full" :style="club.logo ? 'display: none' : 'display: flex'">⚽</span>
                                </div>
                                <span class="text-xs font-bold truncate max-w-full" x-text="club.name"></span>
                            </button>
                        </template>
                    </div>

                    <!-- Custom Club Write-in -->
                    <div class="mt-2.5">
                        <label class="block text-[11px] text-zinc-400 mb-1">Or write in another club / team:</label>
                        <input type="text" x-model="fanForm.favoriteClub" placeholder="e.g. Saint George SC, Bayern, Juventus..." class="w-full bg-dark-panel border border-dark-border rounded-xl px-3.5 py-2 text-white text-xs focus:outline-none focus:border-gold">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-dark-border/60">
                    <div>
                        <label class="block text-xs text-zinc-300 font-semibold mb-1">Your Full Name *</label>
                        <input type="text" x-model="fanForm.name" placeholder="Abebe Kebede" class="w-full bg-dark-panel border border-dark-border rounded-xl px-3.5 py-2 text-white text-xs focus:outline-none focus:border-gold">
                    </div>

                    <div>
                        <label class="block text-xs text-zinc-300 font-semibold mb-1">Email Address (Optional)</label>
                        <input type="email" x-model="fanForm.email" placeholder="abebe@example.com" class="w-full bg-dark-panel border border-dark-border rounded-xl px-3.5 py-2 text-white text-xs focus:outline-none focus:border-gold">
                    </div>
                </div>

                <template x-if="!isTelegramUser">
                    <div>
                        <label class="block text-xs text-zinc-300 font-semibold mb-1">Telegram Username (Optional)</label>
                        <input type="text" x-model="telegramUsername" placeholder="@yourusername" class="w-full bg-dark-panel border border-dark-border rounded-xl px-3.5 py-2 text-white text-xs focus:outline-none focus:border-gold">
                    </div>
                </template>

                <!-- NOTIFICATION CHECKBOXES -->
                <div class="space-y-2.5 pt-2 border-t border-dark-border/60">
                    <span class="block text-xs text-white font-bold uppercase tracking-wider">Alert Subscriptions</span>
                    
                    <label class="flex items-center space-x-2.5 text-xs text-zinc-200 cursor-pointer">
                        <input type="checkbox" x-model="fanForm.notifyMatchday" class="rounded border-dark-border text-gold focus:ring-gold bg-dark-panel">
                        <span>Send matchday screening reminders 2 hours before kickoff</span>
                    </label>

                    <label class="flex items-center space-x-2.5 text-xs text-zinc-200 cursor-pointer">
                        <input type="checkbox" x-model="fanForm.notifyScores" class="rounded border-dark-border text-gold focus:ring-gold bg-dark-panel">
                        <span>Send live final score notifications for <strong class="text-gold" x-text="fanForm.favoriteClub || 'my club'"></strong></span>
                    </label>

                    <label class="flex items-center space-x-2.5 text-xs text-zinc-200 cursor-pointer">
                        <input type="checkbox" x-model="fanForm.notifyAcoustic" class="rounded border-dark-border text-gold focus:ring-gold bg-dark-panel">
                        <span>Send Saturday Acoustic Groove lineups & artist announcements</span>
                    </label>
                </div>

                <div x-show="fanErrorMessage" class="p-3 rounded-xl bg-red-950/60 border border-red-800 text-red-200 text-xs" x-text="fanErrorMessage"></div>

                <div class="pt-2">
                    <button @click="saveFanProfile()" :disabled="savingFan || !fanForm.name" 
                            class="w-full py-3 rounded-xl bg-gold hover:bg-gold-light disabled:opacity-40 text-black font-bold text-sm transition shadow-lg shadow-gold/25 flex items-center justify-center space-x-2">
                        <span x-show="!savingFan">⚡ Save Alerts & Join Fan Club</span>
                        <span x-show="savingFan">Saving Preferences...</span>
                    </button>
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- SCREEN 6: MY RESERVATIONS                  -->
        <!-- ========================================== -->
        <div x-show="screen === 'reservations'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
            
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-display text-2xl font-bold uppercase text-white tracking-wide">
                        My Bookings
                    </h2>
                    <p class="text-xs text-zinc-300">Look up your Saturday Acoustic table reservations</p>
                </div>
                <button @click="setScreen('fanclub')" class="px-3 py-1.5 rounded-xl bg-dark-panel border border-gold/40 text-gold text-xs font-bold">
                    Fan Alerts
                </button>
            </div>

            <!-- SEARCH PHONE FORM -->
            <div class="p-5 rounded-2xl bg-dark-card border border-dark-border space-y-3">
                <label class="block text-xs text-zinc-300 font-semibold">Search by Mobile Phone Number</label>
                <div class="flex space-x-2">
                    <input type="tel" x-model="searchPhone" placeholder="0905 888 724" class="flex-1 bg-dark-panel border border-dark-border rounded-xl px-3.5 py-2 text-white text-sm focus:outline-none focus:border-gold">
                    <button @click="lookupReservations()" :disabled="searchingReservations" class="px-5 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-bold text-xs transition">
                        <span x-show="!searchingReservations">Find</span>
                        <span x-show="searchingReservations">...</span>
                    </button>
                </div>
            </div>

            <!-- RESERVATIONS LIST -->
            <div class="space-y-3">
                <template x-if="userReservations.length === 0 && hasSearched">
                    <div class="text-center py-12 text-zinc-400 bg-dark-card rounded-2xl border border-dark-border">
                        <p>No reservations found for that phone number.</p>
                    </div>
                </template>

                <template x-for="res in userReservations" :key="res.id">
                    <div class="p-5 rounded-2xl bg-dark-card border border-dark-border space-y-3.5 card-hover-lift">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="font-display font-bold text-xl text-gold" x-text="res.code"></span>
                                <span class="text-xs px-2.5 py-0.5 rounded font-bold" :class="res.status === 'CONFIRMED' ? 'bg-emerald-900/60 text-emerald-300' : 'bg-red-900/60 text-red-300'" x-text="res.status"></span>
                            </div>
                            <span class="text-xs text-zinc-300 font-semibold" x-text="formatDateShort(res.date)"></span>
                        </div>

                        <div class="text-xs text-zinc-300 space-y-1.5 p-3.5 rounded-xl bg-dark-panel border border-dark-border/60">
                            <div class="flex justify-between">
                                <span class="text-zinc-400">Session:</span>
                                <span class="font-medium text-white">Live Acoustic Session (7:00 – 11:30 PM)</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-400">Table & Party:</span>
                                <span class="font-medium text-white" x-text="'Table ' + res.tableCode + ' · ' + res.guests + ' Guests'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-400">Venue:</span>
                                <span class="font-medium text-gold">Mexico Chamber of Commerce Bldg</span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-dark-border/60 flex items-center justify-between">
                            <a :href="'{{ url('/ics') }}/' + res.code" class="text-xs text-gold hover:underline flex items-center space-x-1 font-bold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Add to Calendar (.ICS)</span>
                            </a>
                            <template x-if="res.status === 'CONFIRMED'">
                                <button @click="cancelReservation(res.id)" class="px-3 py-1.5 rounded-lg bg-red-950/60 hover:bg-red-900 text-red-300 text-xs font-semibold transition border border-red-800">
                                    Cancel Booking
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

        </div>

    </main>

    <!-- BOTTOM NAVIGATION BAR -->
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-dark-bg/95 backdrop-blur-lg border-t border-dark-border py-1.5 px-2 sm:px-4 shadow-2xl mobile-nav-safe">
        <div class="w-full max-w-xl mx-auto md:max-w-4xl flex items-center justify-around">
            <button @click="setScreen('home')" :class="screen === 'home' ? 'text-gold' : 'text-zinc-400 hover:text-white'" class="flex-1 flex flex-col items-center text-[9px] sm:text-[10px] font-semibold transition py-1">
                <svg class="w-5 h-5 mb-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="truncate" x-text="t('nav_home')">Home</span>
            </button>

            <button @click="setScreen('fixtures')" :class="screen === 'fixtures' ? 'text-gold' : 'text-zinc-400 hover:text-white'" class="flex-1 flex flex-col items-center text-[9px] sm:text-[10px] font-semibold transition py-1">
                <svg class="w-5 h-5 mb-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="truncate" x-text="t('nav_fixtures')">Fixtures</span>
            </button>

            <!-- RAISED RESERVE BUTTON (SATURDAY ACOUSTIC) -->
            <button @click="openReservationWizard()" class="-mt-4 sm:-mt-5 w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-gold hover:bg-gold-light text-black flex flex-col items-center justify-center shadow-xl shadow-gold/35 font-bold transition transform active:scale-95 border-2 border-dark-bg shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"/></svg>
                <span class="text-[7px] sm:text-[8px] uppercase tracking-tighter font-black" x-text="t('nav_reserve')">RESERVE</span>
            </button>

            <button @click="setScreen('menu')" :class="screen === 'menu' ? 'text-gold' : 'text-zinc-400 hover:text-white'" class="flex-1 flex flex-col items-center text-[9px] sm:text-[10px] font-semibold transition py-1">
                <svg class="w-5 h-5 mb-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span class="truncate" x-text="t('nav_menu')">Menu</span>
            </button>

            <button @click="setScreen('fanclub')" :class="screen === 'fanclub' ? 'text-gold' : 'text-zinc-400 hover:text-white'" class="flex-1 flex flex-col items-center text-[9px] sm:text-[10px] font-semibold transition py-1">
                <svg class="w-5 h-5 mb-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span class="truncate" x-text="t('nav_fanclub')">Fan Club</span>
            </button>
        </div>
    </nav>

    <!-- VENUE INFO & DIRECTIONS MODAL -->
    <div x-show="showVenueModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/85 backdrop-blur-md" x-cloak>
        <div class="bg-dark-card border border-dark-border rounded-3xl max-w-sm w-[94%] sm:w-full p-5 sm:p-6 space-y-4 shadow-2xl max-h-[90dvh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-dark-border pb-3">
                <div class="flex items-center space-x-2.5">
                    <img src="{{ asset('images/logo.png') }}" alt="Gura Bicha" class="w-8 h-8 rounded-lg object-contain border border-gold/40 bg-black">
                    <h3 class="font-display text-lg font-bold uppercase text-white">Gura Bicha Sports Bar</h3>
                </div>
                <button @click="showVenueModal = false" class="text-zinc-400 hover:text-white text-lg">✕</button>
            </div>

            <div class="space-y-3 text-xs text-zinc-300">
                <p class="leading-relaxed">📍 <strong>Location:</strong> Addis Ababa, Ethiopia, Mexico inside Chamber of Commerce Building</p>
                <p>🕒 <strong>Hours:</strong> Daily 12:00 PM – 2:00 AM</p>
                <p>📞 <strong>Phone:</strong> <a href="tel:+251905888724" class="text-gold font-bold">+251905888724</a></p>
                <p>🎸 <strong>Live Acoustic Sessions:</strong> 7:00 PM – 11:30 PM</p>
                <p>⚽ <strong>Football Matchdays:</strong> 4K Stadium screens for Premier League & Champions League clashes</p>
            </div>

            <!-- MAP PREVIEW -->
            <div class="w-full h-40 rounded-xl overflow-hidden border border-dark-border">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3940.540118350811!2d38.7441584!3d9.0143899!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x164b8591e5c54c33%3A0xb1a9c37e6f851eb!2sAddis%20Ababa%20Chamber%20of%20Commerce!5e0!3m2!1sen!2set!4v1700000000000" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>

            <div class="flex space-x-2 pt-2">
                <a href="https://maps.app.goo.gl/CK9ZByHcwZeUaHDK6" target="_blank" class="w-1/2 py-2.5 rounded-xl bg-gold text-black font-bold text-xs text-center flex items-center justify-center space-x-1">
                    <span>Directions</span>
                </a>
                <button @click="showVenueModal = false" class="w-1/2 py-2.5 rounded-xl bg-dark-panel text-zinc-300 font-semibold text-xs border border-dark-border">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- DISH / DRINK DETAILS MODAL (previewItem) -->
    <div x-show="previewItem" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/85 backdrop-blur-md" x-cloak>
        <div class="bg-dark-card border border-dark-border rounded-3xl max-w-sm w-[94%] sm:w-full overflow-hidden shadow-2xl space-y-4 max-h-[92dvh] overflow-y-auto">
            <div class="relative h-52 w-full bg-zinc-900">
                <img :src="previewItem ? previewItem.imageUrl : ''" :alt="previewItem ? previewItem.name : ''" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-dark-card via-black/20 to-transparent"></div>
                <button @click="previewItem = null" class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/70 text-white flex items-center justify-center hover:bg-black transition">✕</button>
                <div class="absolute bottom-3 left-4 right-4 flex items-center justify-between">
                    <span class="px-2.5 py-1 rounded-lg bg-black/80 text-gold text-xs font-bold uppercase tracking-wider border border-gold/40" x-text="previewItem ? previewItem.category : ''"></span>
                    <span class="font-display font-extrabold text-lg text-gold bg-black/80 px-3 py-1 rounded-xl border border-gold/40" x-text="previewItem ? (previewItem.price + ' ETB') : ''"></span>
                </div>
            </div>
            <div class="p-5 pt-0 space-y-3 text-xs">
                <h3 class="font-display text-xl font-bold text-white uppercase tracking-wide" x-text="previewItem ? previewItem.name : ''"></h3>
                <p class="text-zinc-300 leading-relaxed text-xs" x-text="previewItem ? previewItem.description : ''"></p>
                <div class="p-3 rounded-xl bg-dark-panel border border-dark-border space-y-1">
                    <span class="text-gold font-bold block text-[11px] uppercase tracking-wider">Kitchen & Bar Note:</span>
                    <span class="text-zinc-300 text-[11px]">Prepared fresh to order at Gura Bicha Sport Bar. Good Food • Great Drinks • Best Vibes!</span>
                </div>
                <div class="pt-2">
                    <button @click="previewItem = null" class="w-full py-3 rounded-xl bg-gold hover:bg-gold-light text-black font-bold text-xs transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- OFFICIAL MENU BOARD PHOTO MODAL -->
    <div x-show="showMenuBoardModal" class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 bg-black/90 backdrop-blur-md" x-cloak>
        <div class="bg-dark-card border border-gold/50 rounded-3xl max-w-lg w-[95%] sm:w-full overflow-hidden shadow-2xl flex flex-col max-h-[92dvh]">
            <div class="p-3.5 sm:p-4 border-b border-dark-border flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-xl">📜</span>
                    <div>
                        <h3 class="font-display text-sm sm:text-base font-bold text-white uppercase">Official Menu Board</h3>
                        <p class="text-[10px] sm:text-[11px] text-gold">GB Gura Bicha Sport Bar — Good Food Great Drinks Best Vibes</p>
                    </div>
                </div>
                <button @click="showMenuBoardModal = false" class="w-8 h-8 rounded-full bg-dark-panel text-zinc-300 hover:text-white flex items-center justify-center border border-dark-border transition">✕</button>
            </div>
            <div class="p-2 sm:p-3 overflow-y-auto bg-black flex items-center justify-center">
                <img src="/images/menu-card.jpg" alt="Gura Bicha Official Menu Board" class="rounded-xl sm:rounded-2xl max-h-[65dvh] sm:max-h-[70vh] object-contain shadow-2xl border border-zinc-800">
            </div>
            <div class="p-3 bg-dark-card border-t border-dark-border flex items-center justify-between text-xs">
                <span class="text-zinc-400 text-[11px]">All prices in ETB · Chamber Bldg</span>
                <button @click="showMenuBoardModal = false" class="px-4 py-2 rounded-xl bg-gold hover:bg-gold-light text-black font-bold text-xs">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- CLUB LOGOS DICTIONARY & APP JAVASCRIPT LOGIC -->
    <script>
        const FDATA_CDN = 'https://crests.football-data.org/';
        const APISPORTS_CDN = 'https://media.api-sports.io/football/teams/';

        const CLUB_LOGOS = {
            // Premier League (Full, Short & Nicknames)
            'Arsenal': FDATA_CDN + '57.png',
            'Aston Villa': FDATA_CDN + '58.png',
            'Bournemouth': FDATA_CDN + '1044.png',
            'Brentford': FDATA_CDN + '402.png',
            'Brighton': FDATA_CDN + '397.png',
            'Brighton & Hove Albion': FDATA_CDN + '397.png',
            'Chelsea': FDATA_CDN + '61.png',
            'Crystal Palace': FDATA_CDN + '354.png',
            'Everton': FDATA_CDN + '62.png',
            'Fulham': FDATA_CDN + '63.png',
            'Ipswich Town': FDATA_CDN + '349.png',
            'Ipswich': FDATA_CDN + '349.png',
            'Leicester City': FDATA_CDN + '338.png',
            'Leicester': FDATA_CDN + '338.png',
            'Liverpool': FDATA_CDN + '64.png',
            'Manchester City': FDATA_CDN + '65.png',
            'Man City': FDATA_CDN + '65.png',
            'Manchester United': FDATA_CDN + '66.png',
            'Man United': FDATA_CDN + '66.png',
            'Man Utd': FDATA_CDN + '66.png',
            'Newcastle United': FDATA_CDN + '67.png',
            'Newcastle': FDATA_CDN + '67.png',
            'Nottingham Forest': FDATA_CDN + '351.png',
            'Southampton': FDATA_CDN + '340.png',
            'Tottenham Hotspur': FDATA_CDN + '73.png',
            'Tottenham': FDATA_CDN + '73.png',
            'Spurs': FDATA_CDN + '73.png',
            'West Ham United': FDATA_CDN + '563.png',
            'West Ham': FDATA_CDN + '563.png',
            'Wolverhampton Wanderers': FDATA_CDN + '76.png',
            'Wolves': FDATA_CDN + '76.png',
            'Burnley': FDATA_CDN + '328.png',
            'Leeds United': FDATA_CDN + '341.png',
            'Leeds': FDATA_CDN + '341.png',
            'Coventry City': FDATA_CDN + '1076.png',
            'Hull City': FDATA_CDN + '322.png',
            'Sunderland': FDATA_CDN + '71.png',

            // La Liga
            'Real Madrid': FDATA_CDN + '86.png',
            'Barcelona': FDATA_CDN + '81.png',
            'Barca': FDATA_CDN + '81.png',
            'Atletico Madrid': FDATA_CDN + '78.png',
            'Athletic Club': FDATA_CDN + '77.png',
            'Athletic Bilbao': FDATA_CDN + '77.png',
            'Real Betis': FDATA_CDN + '90.png',
            'Villarreal': FDATA_CDN + '94.png',

            // Bundesliga
            'Bayern Munich': FDATA_CDN + '5.png',
            'Bayern': FDATA_CDN + '5.png',
            'Borussia Dortmund': FDATA_CDN + '4.png',
            'Dortmund': FDATA_CDN + '4.png',
            'Bayer Leverkusen': FDATA_CDN + '3.png',
            'Leverkusen': FDATA_CDN + '3.png',
            'RB Leipzig': FDATA_CDN + '721.png',
            'Leipzig': FDATA_CDN + '721.png',
            'Eintracht Frankfurt': FDATA_CDN + '19.png',
            'Frankfurt': FDATA_CDN + '19.png',
            'VfB Stuttgart': FDATA_CDN + '10.png',
            'Stuttgart': FDATA_CDN + '10.png',

            // Serie A
            'Inter': FDATA_CDN + '108.png',
            'Inter Milan': FDATA_CDN + '108.png',
            'Internazionale': FDATA_CDN + '108.png',
            'Juventus': FDATA_CDN + '109.png',
            'AC Milan': FDATA_CDN + '98.png',
            'Milan': FDATA_CDN + '98.png',
            'Atalanta': FDATA_CDN + '102.png',
            'Napoli': FDATA_CDN + '113.png',
            'Roma': FDATA_CDN + '100.png',
            'AS Roma': FDATA_CDN + '100.png',
            'Como': FDATA_CDN + '1063.png',

            // Ligue 1
            'PSG': FDATA_CDN + '524.png',
            'Paris Saint-Germain': FDATA_CDN + '524.png',
            'Marseille': FDATA_CDN + '516.png',
            'Monaco': FDATA_CDN + '548.png',
            'Lille': FDATA_CDN + '521.png',
            'Lens': FDATA_CDN + '546.png',

            // European Champions League & Continental Contenders
            'Ajax Amsterdam': FDATA_CDN + '678.png',
            'Ajax': FDATA_CDN + '678.png',
            'Feyenoord Rotterdam': FDATA_CDN + '675.png',
            'Feyenoord': FDATA_CDN + '675.png',
            'PSV': FDATA_CDN + '674.png',
            'PSV Eindhoven': FDATA_CDN + '674.png',
            'Benfica': FDATA_CDN + '1903.png',
            'Porto': FDATA_CDN + '503.png',
            'Sporting CP': FDATA_CDN + '498.png',
            'Sporting': FDATA_CDN + '498.png',
            'Galatasaray': APISPORTS_CDN + '645.png',
            'Fenerbahce': APISPORTS_CDN + '611.png',
            'Celtic': FDATA_CDN + '732.png',
            'Club Brugge': FDATA_CDN + '851.png',
            'Shakhtar Donetsk': FDATA_CDN + '683.png',
            'Bodø/Glimt': FDATA_CDN + '1879.png',
            'Slavia Prague': FDATA_CDN + '1908.png',
            'Slovan Bratislava': FDATA_CDN + '1906.png',
            'Olympiacos': FDATA_CDN + '654.png',
            'Copenhagen': FDATA_CDN + '1876.png',
            'FC Copenhagen': FDATA_CDN + '1876.png',
            'Union St.-Gilloise': FDATA_CDN + '1901.png',
            'LASK': FDATA_CDN + '2016.png',
            'AEK Athens': FDATA_CDN + '1878.png',
            'FK Qarabag': APISPORTS_CDN + '558.png',
            'Kairat Almaty': APISPORTS_CDN + '2275.png',
            'Sabah FK': APISPORTS_CDN + '6463.png',
            'Pafos': APISPORTS_CDN + '3389.png',
            'Viking': APISPORTS_CDN + '328.png'
        };

        function guraBichaApp() {
            return {
                theme: localStorage.getItem('GB_THEME') || 'dark',
                lang: localStorage.getItem('GB_LANG') || 'en',
                screen: 'home',
                showVenueModal: false,
                telegramBotUsername: '{{ config('services.telegram.bot_username', 'GuraBichaAddisBot') }}',
                fixtureTab: 'ALL', // 'ALL' | 'TODAY' | 'SATURDAY' | 'RESULTS'
                activeTableCode: null,
                events: (window.INITIAL_EVENTS && window.INITIAL_EVENTS.length) ? window.INITIAL_EVENTS : [],
                allMatches: [],
                menuItems: (window.INITIAL_MENU && window.INITIAL_MENU.length) ? window.INITIAL_MENU : [],
                tables: [],
                remainingTablesCount: 6,
                featuredMatch: null,

                // Background Slideshow State (Football & Acoustic Music)
                bgSlides: [
                    {
                        image: "{{ asset('images/event-sports.jpg') }}",
                        title: 'Premier League Matchday Vibes',
                        icon: '⚽'
                    },
                    {
                        image: "{{ asset('images/event-acoustic.jpg') }}",
                        title: 'Live Acoustic Soul Sessions',
                        icon: '🎸'
                    },
                    {
                        image: "{{ asset('images/hero-gura.jpg') }}",
                        title: 'Gura Bicha Stadium Atmosphere',
                        icon: '🏟️'
                    },
                    {
                        image: 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=1600&q=80',
                        title: 'Live Acoustic Guitar & Vocals',
                        icon: '🎶'
                    },
                    {
                        image: 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=1600&q=80',
                        title: 'Champions League 4K Multi-Screen Arena',
                        icon: '🏆'
                    },
                    {
                        image: 'https://images.unsplash.com/photo-1465847899084-d164df4dedc6?auto=format&fit=crop&w=1600&q=80',
                        title: 'Candlelight Acoustic Lounge Mood',
                        icon: '🕯️'
                    }
                ],
                currentBgIndex: 0,

                // Real-time Fixtures & Match Calendar State
                fixtureViewMode: 'list', // 'list' | 'calendar'
                matchStatusFilter: 'ALL', // 'ALL' | 'LIVE' | 'UPCOMING' | 'FINISHED'
                calendarYear: 2026,
                calendarMonth: 8, // August
                selectedCalendarDate: '2026-08-15',

                // Real-Time Live Simulated Ongoing Matches
                liveMatches: [
                    {
                        id: 'live-1',
                        homeTeam: 'Arsenal',
                        awayTeam: 'Chelsea',
                        homeScore: 2,
                        awayScore: 1,
                        competition: 'Premier League',
                        stage: 'Matchweek 1 Blockbuster',
                        minute: 74,
                        events: ['⚽ 22\' B. Saka', '🟡 39\' M. Caicedo', '⚽ 58\' C. Palmer', '⚽ 67\' K. Havertz']
                    },
                    {
                        id: 'live-2',
                        homeTeam: 'Real Madrid',
                        awayTeam: 'Bayern Munich',
                        homeScore: 3,
                        awayScore: 2,
                        competition: 'Champions League',
                        stage: 'Super Clash',
                        minute: 82,
                        events: ['⚽ 14\' Vinicius Jr', '⚽ 29\' H. Kane', '⚽ 52\' J. Bellingham', '⚽ 71\' J. Musiala', '⚽ 79\' K. Mbappe']
                    }
                ],
                activeLiveMatchIndex: 0,
                kickoffCountdownSeconds: 9918,
                kickoffCountdownText: '02h 45m 18s',

                get activeLiveMatch() {
                    return this.liveMatches[this.activeLiveMatchIndex] || null;
                },

                // Translation Dictionary (English / Amharic)
                i18n: {
                    en: {
                        app_title: 'GURA BICHA',
                        app_subtitle: 'Sports Bar & Acoustic Lounge · Mexico',
                        nav_home: 'Home',
                        nav_fixtures: 'Fixtures',
                        nav_reserve: 'RESERVE',
                        nav_menu: 'Menu',
                        nav_fanclub: 'Fan Club',
                        map_chamber: 'Chamber Bldg · Mexico',
                        map_btn: 'Map',
                        hero_acoustic_badge: 'LIVE ACOUSTIC GROOVE',
                        hero_football_badge: '⚽ PREMIER LEAGUE 4K ARENA',
                        hero_title_1: 'Football Passion & Live Grooves',
                        hero_title_2: 'Mexico, Chamber of Commerce',
                        hero_reserve_btn: 'Reserve Acoustic Table',
                        hero_fixtures_btn: '⚽ Current Fixtures',
                        hero_fanclub_btn: '🔔 Fan Club Alerts',
                        fan_callout_tag: 'FAN CLUB ALERTS',
                        fan_callout_free: 'Free Telegram Updates',
                        fan_callout_title: 'Support Arsenal, Chelsea, Man Utd or Real Madrid?',
                        fan_callout_desc: 'Get notified when your club plays at Gura Bicha & receive live match scores.',
                        fan_callout_btn: 'Join Club',
                        sat_strip_every: 'Live Acoustic Nights',
                        sat_strip_hours: '7:00 PM – 11:30 PM',
                        sat_strip_title: 'Game & Groove Acoustic Sessions',
                        sat_strip_desc: 'Intimate live band performances, candlelight mood, and reserved premium seating.',
                        sat_strip_btn: 'Book Acoustic Table',
                        fixtures_title: 'Current Football Fixtures',
                        fixtures_subtitle: 'Live screening & upcoming Premier League / Champions League matches (Addis Ababa EAT)',
                        tab_all_fixtures: '🔥 All Current',
                        tab_today_fixtures: '🔴 Today & Live',
                        tab_saturday_fixtures: '🎸 Weekend Specials',
                        tab_results_fixtures: '🏆 Recent Results',
                        view_list: 'List',
                        view_calendar: 'Calendar',
                        live_now_badge: 'LIVE MATCH NOW AT GURA BICHA',
                        next_kickoff_in: 'NEXT BIG KICKOFF IN',
                        seating_note_title: 'ℹ️ SEATING NOTE:',
                        seating_note_desc: 'Live 4K stadium screen with high-fidelity sound. Table booking available for live acoustic sessions.',
                        search_club_placeholder: 'Search club (e.g. Arsenal, Chelsea, Liverpool, Man City, Real Madrid)...',
                        all_comps: 'All Competitions',
                        status_all: 'All Matches',
                        status_live: 'Live Now',
                        status_upcoming: 'Upcoming',
                        status_ft: 'Results (FT)',
                        walk_in_badge: '📺 Walk-in Seating',
                        select_sat_date: 'Select Event Date',
                        pick_calendar: 'Calendar',
                        quick_pick_saturdays: 'Quick-pick upcoming Acoustic Sessions:',
                        session_time: 'Session Time',
                        sat_evening_groove: 'Evening Acoustic Groove Session',
                        sat_groove_time: '7:00 PM – 11:30 PM · Live band starts 8:00 PM',
                        selected_badge: 'SELECTED',
                        continue_tables: 'Continue to Table Selection'
                    },
                    am: {
                        app_title: 'ጉራ ብቻ',
                        app_subtitle: 'የስፖርት ባር እና አኮስቲክ ላውንጅ · ሜክሲኮ',
                        nav_home: 'ዋና ገጽ',
                        nav_fixtures: 'ጨዋታዎች',
                        nav_reserve: 'ቦታ ይያዙ',
                        nav_menu: 'ሜኑ',
                        nav_fanclub: 'ደጋፊዎች',
                        map_chamber: 'ንግድ ምክር ቤት · ሜክሲኮ',
                        map_btn: 'ካርታ',
                        hero_acoustic_badge: 'የቀጥታ አኮስቲክ ሙዚቃ',
                        hero_football_badge: '⚽ የፕሪሚየር ሊግ 4K ስክሪን',
                        hero_title_1: 'የእግር ኳስ ፍቅርና የቀጥታ ዜማ',
                        hero_title_2: 'ሜክሲኮ፣ የንግድ ምክር ቤት ህንፃ',
                        hero_reserve_btn: 'የአኮስቲክ ጠረጴዛ ይያዙ',
                        hero_fixtures_btn: '⚽ የወቅቱ ጨዋታዎች',
                        hero_fanclub_btn: '🔔 የደጋፊዎች ማሳወቂያ',
                        fan_callout_tag: 'የደጋፊዎች ክበብ ማሳወቂያ',
                        fan_callout_free: 'በነጻ የቴሌግራም መልእክት',
                        fan_callout_title: 'አርሰናል፣ ቼልሲ፣ ማን ዩናይትድ ወይስ ሪያል ማድሪድ ይደግፋሉ?',
                        fan_callout_desc: 'ክለብዎ ጉራ ብቻ ሲጫወት እና የቀጥታ ውጤት በቴሌግራም ያግኙ።',
                        fan_callout_btn: 'ክበቡን ይቀላቀሉ',
                        sat_strip_every: 'የቀጥታ አኮስቲክ ምሽቶች',
                        sat_strip_hours: 'ከምሽቱ 1:00 – 5:30',
                        sat_strip_title: 'የጨዋታና ዜማ የቀጥታ አኮስቲክ',
                        sat_strip_desc: 'የቀጥታ ባንድ ዝግጅት፣ የሻማ ብርሃን ድባብ እና የተያዙ የፊት ወንበሮች።',
                        sat_strip_btn: 'ጠረጴዛ ይያዙ',
                        fixtures_title: 'የወቅቱ የእግር ኳስ ጨዋታዎች',
                        fixtures_subtitle: 'የቀጥታ ስርጭት እና የፕሪሚየር ሊግ / ሻምፒዮንስ ሊግ መርሃ-ግብር (አዲስ አበባ ሰዓት)',
                        tab_all_fixtures: '🔥 የወቅቱ ጨዋታዎች',
                        tab_today_fixtures: '🔴 ዛሬና ቀጥታ',
                        tab_saturday_fixtures: '🎸 የሳምንቱ መጨረሻ',
                        tab_results_fixtures: '🏆 የቅርብ ውጤቶች',
                        view_list: 'ዝርዝር',
                        view_calendar: 'ቀን መቁጠሪያ',
                        live_now_badge: 'ጉራ ብቻ አሁን በቀጥታ በመታየት ላይ ያለ',
                        next_kickoff_in: 'ቀጣዩ ትልቅ ጨዋታ የሚጀምርበት ጊዜ',
                        seating_note_title: 'ℹ️ የመቀመጫ ማስታወሻ፡',
                        seating_note_desc: 'የ4K ስክሪን የቀጥታ ስርጭት። ጠረጴዛ ማስያዝ ለአኮስቲክ ሙዚቃ ክፍለ-ጊዜዎች ይገኛል።',
                        search_club_placeholder: 'ክለብ ይፈልጉ (ለምሳሌ አርሰናል፣ ቼልሲ፣ ማን ሲቲ፣ ሪያል ማድሪድ)...',
                        all_comps: 'ሁሉም ውድድሮች',
                        status_all: 'ሁሉም ጨዋታዎች',
                        status_live: 'አሁን በቀጥታ',
                        status_upcoming: 'የሚመጡ',
                        status_ft: 'ውጤቶች (ያለቁ)',
                        walk_in_badge: '📺 ያለቦታ ማስያዝ መግባት',
                        select_sat_date: 'ቀን ይምረጡ',
                        pick_calendar: 'ቀን መቁጠሪያ',
                        quick_pick_saturdays: 'የሚመጡ የአኮስቲክ ምሽቶች ፈጣን ምርጫ፡',
                        session_time: 'የዝግጅቱ ሰዓት',
                        sat_evening_groove: 'የምሽት አኮስቲክ ሙዚቃ',
                        sat_groove_time: 'ከምሽቱ 1:00 – 5:30 · ባንዱ 2:00 ላይ ይጀምራል',
                        selected_badge: 'ተመርጧል',
                        continue_tables: 'ወደ ጠረጴዛ ምርጫ ይቀጥሉ'
                    }
                },

                toggleLang() {
                    this.lang = this.lang === 'en' ? 'am' : 'en';
                    localStorage.setItem('GB_LANG', this.lang);
                    if (this.tgWebApp && this.tgWebApp.HapticFeedback) {
                        this.tgWebApp.HapticFeedback.impactOccurred('medium');
                    }
                    this.syncTelegramButtons();
                },

                t(key) {
                    const table = this.i18n[this.lang] || this.i18n['en'];
                    return (table && table[key]) ? table[key] : (this.i18n['en'][key] || key);
                },

                // Fixtures filters (STARTS FROM AUGUST: 8)
                matchFilter: 'ALL',
                teamSearch: '',
                selectedMonth: 8, // Aug by default (Matchweek 1)
                months: [
                    { num: 8, name: 'Aug', count: 20, badge: 'MW 1' },
                    { num: 9, name: 'Sep', count: 48 },
                    { num: 10, name: 'Oct', count: 73 },
                    { num: 11, name: 'Nov', count: 69 },
                    { num: 12, name: 'Dec', count: 78 },
                    { num: 1, name: 'Jan', count: 85 },
                    { num: 2, name: 'Feb', count: 58 },
                    { num: 3, name: 'Mar', count: 48 },
                    { num: 4, name: 'Apr', count: 40 },
                    { num: 5, name: 'May', count: 44, badge: 'FINALE' },
                ],

                // Popular clubs for quick selection
                popularClubs: [
                    { name: 'Arsenal', logo: CLUB_LOGOS['Arsenal'] },
                    { name: 'Chelsea', logo: CLUB_LOGOS['Chelsea'] },
                    { name: 'Manchester United', logo: CLUB_LOGOS['Manchester United'] },
                    { name: 'Manchester City', logo: CLUB_LOGOS['Manchester City'] },
                    { name: 'Liverpool', logo: CLUB_LOGOS['Liverpool'] },
                    { name: 'Real Madrid', logo: CLUB_LOGOS['Real Madrid'] },
                    { name: 'Barcelona', logo: CLUB_LOGOS['Barcelona'] },
                    { name: 'Tottenham Hotspur', logo: CLUB_LOGOS['Tottenham Hotspur'] },
                ],

                // Fan Club & Alerts State
                fanForm: {
                    name: '',
                    phone: '',
                    email: '',
                    favoriteClub: 'Arsenal',
                    notifyMatchday: true,
                    notifyScores: true,
                    notifyAcoustic: true,
                },
                fanSaved: false,
                savingFan: false,
                fanErrorMessage: '',

                // Menu State
                menuCategory: 'ALL',
                menuSubCategory: 'ALL',
                menuSearch: '',
                menuSort: 'popular',
                showMenuBoardModal: false,
                previewItem: null,

                // Reserve Wizard (EXCLUSIVELY FOR SATURDAY ACOUSTIC)
                wizardStep: 1,
                todayDateString: new Date().toISOString().slice(0, 10),
                upcomingSaturdays: [],
                reserveDraft: {
                    date: '',
                    slot: 'EVENING',
                    table: null,
                    guests: 4,
                },
                reserveForm: {
                    name: '',
                    phone: '',
                    email: '',
                    request: '',
                    saveDetails: true,
                },
                submitting: false,
                errorMessage: '',
                confirmedReservation: null,

                // My Reservations
                searchPhone: '',
                userReservations: [],
                searchingReservations: false,
                hasSearched: false,

                // Telegram WebApp State
                tgWebApp: null,
                isTelegramUser: false,
                telegramUsername: '',
                telegramUserId: null,

                updateTelegramColors() {
                    if (!this.tgWebApp) return;
                    const isDark = this.theme === 'dark';
                    try {
                        if (this.tgWebApp.setHeaderColor) {
                            this.tgWebApp.setHeaderColor(isDark ? '#060709' : '#ffffff');
                        }
                        if (this.tgWebApp.setBackgroundColor) {
                            this.tgWebApp.setBackgroundColor(isDark ? '#060709' : '#faf8f5');
                        }
                        if (this.tgWebApp.setBottomBarColor) {
                            this.tgWebApp.setBottomBarColor(isDark ? '#060709' : '#ffffff');
                        }
                    } catch (e) {}
                },

                syncTelegramButtons() {
                    if (!this.tgWebApp) return;

                    // Back button handling
                    try {
                        if (this.tgWebApp.BackButton) {
                            if (this.screen !== 'home') {
                                this.tgWebApp.BackButton.show();
                            } else {
                                this.tgWebApp.BackButton.hide();
                            }
                        }
                    } catch (e) {}

                    // Main button handling (Step 4 Saturday reservation confirmation)
                    try {
                        if (this.tgWebApp.MainButton) {
                            if (this.screen === 'reserve' && this.wizardStep === 4) {
                                this.tgWebApp.MainButton.setText(this.lang === 'am' ? 'ቦታውን አረጋግጥ' : 'CONFIRM RESERVATION');
                                this.tgWebApp.MainButton.enable();
                                this.tgWebApp.MainButton.show();
                            } else {
                                this.tgWebApp.MainButton.hide();
                            }
                        }
                    } catch (e) {}
                },

                toggleTheme() {
                    this.theme = this.theme === 'dark' ? 'light' : 'dark';
                    localStorage.setItem('GB_THEME', this.theme);
                    document.documentElement.classList.remove('dark', 'light');
                    document.documentElement.classList.add(this.theme);
                    this.updateTelegramColors();
                    if (this.tgWebApp && this.tgWebApp.HapticFeedback) {
                        this.tgWebApp.HapticFeedback.impactOccurred('medium');
                    }
                },

                async initApp() {
                    // Apply saved theme
                    document.documentElement.classList.remove('dark', 'light');
                    document.documentElement.classList.add(this.theme);

                    // Start background slideshow and live match simulation timers
                    this.startBgSlideshow();
                    this.startLiveSimulation();

                    // Check Telegram WebApp
                    if (window.Telegram && window.Telegram.WebApp) {
                        this.tgWebApp = window.Telegram.WebApp;
                        document.documentElement.classList.add('telegram-webapp');
                        try {
                            this.tgWebApp.ready();
                            this.tgWebApp.expand();

                            // Prevent unwanted sheet dismissal when scrolling inside Mini App
                            if (typeof this.tgWebApp.disableVerticalSwipes === 'function') {
                                this.tgWebApp.disableVerticalSwipes();
                            }

                            // Enable closing confirmation to prevent accidental loss of state
                            if (typeof this.tgWebApp.enableClosingConfirmation === 'function') {
                                this.tgWebApp.enableClosingConfirmation();
                            }

                            // Request fullscreen if supported (Telegram WebApp 8.0+)
                            if (typeof this.tgWebApp.requestFullscreen === 'function') {
                                try { this.tgWebApp.requestFullscreen(); } catch (e) {}
                            }

                            // Dynamically calculate and apply safe area clearance below Telegram's native 'Close' & menu bar
                            const updateTgInsets = () => {
                                let topInset = 54;
                                if (this.tgWebApp.isFullscreen) {
                                    document.documentElement.classList.add('telegram-fullscreen');
                                }
                                if (this.tgWebApp.safeAreaInset && this.tgWebApp.safeAreaInset.top) {
                                    topInset = Math.max(topInset, this.tgWebApp.safeAreaInset.top);
                                }
                                if (this.tgWebApp.contentSafeAreaInset && this.tgWebApp.contentSafeAreaInset.top) {
                                    topInset = Math.max(topInset, this.tgWebApp.contentSafeAreaInset.top);
                                }
                                document.documentElement.style.setProperty('--tg-top-inset', `${topInset}px`);
                                if (this.tgWebApp.viewportHeight) {
                                    document.documentElement.style.setProperty('--app-height', `${this.tgWebApp.viewportHeight}px`);
                                }
                            };
                            updateTgInsets();

                            if (typeof this.tgWebApp.onEvent === 'function') {
                                this.tgWebApp.onEvent('safeAreaChanged', updateTgInsets);
                                this.tgWebApp.onEvent('contentSafeAreaChanged', updateTgInsets);
                                this.tgWebApp.onEvent('fullscreenChanged', updateTgInsets);
                                this.tgWebApp.onEvent('viewportChanged', updateTgInsets);
                            }

                            this.updateTelegramColors();
                        } catch (e) {}

                        if (this.tgWebApp.initDataUnsafe && this.tgWebApp.initDataUnsafe.user) {
                            const u = this.tgWebApp.initDataUnsafe.user;
                            this.isTelegramUser = true;
                            this.telegramUserId = u.id;
                            this.telegramUsername = u.username ? '@' + u.username : '';
                            const fullName = ((u.first_name || '') + (u.last_name ? ' ' + u.last_name : '')).trim();
                            if (fullName) {
                                this.reserveForm.name = fullName;
                                this.fanForm.name = fullName;
                            }
                        }

                        // Attach Telegram BackButton listener
                        try {
                            if (this.tgWebApp.BackButton) {
                                this.tgWebApp.BackButton.onClick(() => {
                                    if (this.screen === 'reserve' && this.wizardStep > 1) {
                                        this.goToStep(this.wizardStep - 1);
                                    } else {
                                        this.setScreen('home');
                                    }
                                });
                            }
                        } catch (e) {}

                        // Attach Telegram MainButton listener
                        try {
                            if (this.tgWebApp.MainButton) {
                                this.tgWebApp.MainButton.onClick(() => {
                                    if (this.screen === 'reserve' && this.wizardStep === 4 && !this.submitting) {
                                        this.submitReservation();
                                    }
                                });
                            }
                        } catch (e) {}
                    }

                    // Restore saved details
                    try {
                        const saved = localStorage.getItem('GB_DETAILS');
                        if (saved) {
                            const d = JSON.parse(saved);
                            this.reserveForm.name = d.name || this.reserveForm.name;
                            this.reserveForm.phone = d.phone || '';
                            this.reserveForm.email = d.email || '';
                            this.searchPhone = d.phone || '';

                            this.fanForm.name = d.name || this.fanForm.name;
                            this.fanForm.phone = d.phone || '';
                            this.fanForm.email = d.email || '';
                            this.fanForm.favoriteClub = d.favoriteClub || 'Arsenal';
                        }
                    } catch (e) {}

                    // Compute upcoming Saturdays
                    this.computeSaturdays();

                    // Load initial data
                    await Promise.all([
                        this.loadEvents(),
                        this.loadMatches(),
                        this.loadMenu(),
                    ]);

                    // Handle URL parameters (e.g. ?table=T1 from table QR codes)
                    try {
                        const urlParams = new URLSearchParams(window.location.search);
                        const tableParam = urlParams.get('table');
                        if (tableParam) {
                            this.activeTableCode = tableParam.toUpperCase();
                            this.setScreen('menu');
                        }
                    } catch (e) {}

                    // Handle URL hash navigation (e.g. #menu, #fixtures, #fanclub)
                    if (window.location.hash && !this.activeTableCode) {
                        const target = window.location.hash.replace('#', '');
                        if (['home', 'fixtures', 'reserve', 'menu', 'fanclub', 'reservations'].includes(target)) {
                            this.setScreen(target);
                        }
                    }

                    // If phone is saved, auto-lookup reservations
                    if (this.searchPhone) {
                        this.lookupReservations();
                    }
                },

                computeSaturdays() {
                    const out = [];
                    const added = new Set();
                    const todayIso = new Date().toISOString().slice(0, 10);

                    // 1. Add dates from active scheduled events (flexible dates: could be any day!)
                    if (this.events && this.events.length > 0) {
                        for (const ev of this.events) {
                            if (ev.date && ev.date >= todayIso && !added.has(ev.date)) {
                                const d = new Date(ev.date + 'T00:00:00');
                                const label = d.toLocaleDateString(this.lang === 'am' ? 'am-ET' : 'en-US', { weekday: 'short', month: 'short', day: 'numeric' });
                                out.push({ date: ev.date, label: label, tag: 'Event' });
                                added.add(ev.date);
                            }
                        }
                    }

                    // 2. Add upcoming weekend & next session dates
                    let cursor = new Date();
                    cursor.setDate(cursor.getDate() + 1);
                    while (out.length < 5) {
                        const iso = cursor.toISOString().slice(0, 10);
                        if (!added.has(iso)) {
                            const day = cursor.getDay();
                            // Include Fridays (5), Saturdays (6), Sundays (0) or next immediate days
                            if (day === 5 || day === 6 || day === 0 || out.length < 3) {
                                const label = cursor.toLocaleDateString(this.lang === 'am' ? 'am-ET' : 'en-US', { weekday: 'short', month: 'short', day: 'numeric' });
                                out.push({ date: iso, label: label, tag: '' });
                                added.add(iso);
                            }
                        }
                        cursor.setDate(cursor.getDate() + 1);
                    }

                    out.sort((a, b) => a.date.localeCompare(b.date));
                    this.upcomingSaturdays = out;
                    if (out.length > 0 && !this.reserveDraft.date) {
                        this.reserveDraft.date = out[0].date;
                    }
                },

                setScreen(s) {
                    this.screen = s;
                    window.location.hash = s;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    if (this.tgWebApp && this.tgWebApp.HapticFeedback) {
                        this.tgWebApp.HapticFeedback.selectionChanged();
                    }
                    this.syncTelegramButtons();
                },

                getTeamLogo(name) {
                    if (!name) return null;
                    if (CLUB_LOGOS[name]) return CLUB_LOGOS[name];
                    const clean = name.trim().toLowerCase();
                    
                    // Direct case-insensitive match
                    let key = Object.keys(CLUB_LOGOS).find(k => k.toLowerCase() === clean);
                    if (key) return CLUB_LOGOS[key];

                    // Alias map for common variations & abbreviations
                    const aliases = {
                        'paris': 'PSG',
                        'paris sg': 'PSG',
                        'paris saint germain': 'PSG',
                        'paris saint-germain': 'PSG',
                        'manchester united': 'Manchester United',
                        'manchester utd': 'Manchester United',
                        'man united': 'Manchester United',
                        'man utd': 'Manchester United',
                        'man city': 'Manchester City',
                        'manchester city': 'Manchester City',
                        'tottenham': 'Tottenham Hotspur',
                        'spurs': 'Tottenham Hotspur',
                        'wolves': 'Wolves',
                        'wolverhampton': 'Wolves',
                        'wolverhampton wanderers': 'Wolves',
                        'brighton': 'Brighton',
                        'brighton and hove albion': 'Brighton',
                        'brighton & hove albion': 'Brighton',
                        'newcastle': 'Newcastle United',
                        'west ham': 'West Ham United',
                        'nottingham': 'Nottingham Forest',
                        'leicester': 'Leicester City',
                        'ipswich': 'Ipswich Town',
                        'atletico': 'Atletico Madrid',
                        'atlético': 'Atletico Madrid',
                        'atlético madrid': 'Atletico Madrid',
                        'athletic bilbao': 'Athletic Club',
                        'inter milan': 'Inter',
                        'internazionale': 'Inter',
                        'ac milan': 'AC Milan',
                        'milan': 'AC Milan',
                        'bayern': 'Bayern Munich',
                        'bayern münchen': 'Bayern Munich',
                        'bayern munchen': 'Bayern Munich',
                        'leverkusen': 'Bayer Leverkusen',
                        'dortmund': 'Borussia Dortmund',
                        'leipzig': 'RB Leipzig',
                        'sporting lisbon': 'Sporting CP',
                        'sporting': 'Sporting CP',
                        'shakhtar': 'Shakhtar Donetsk',
                        'qarabag': 'FK Qarabag',
                        'bodo/glimt': 'Bodø/Glimt',
                        'bodo glimt': 'Bodø/Glimt',
                        'slavia praha': 'Slavia Prague',
                        'feyenoord': 'Feyenoord Rotterdam',
                        'ajax': 'Ajax Amsterdam'
                    };
                    if (aliases[clean] && CLUB_LOGOS[aliases[clean]]) {
                        return CLUB_LOGOS[aliases[clean]];
                    }

                    // Strip common team prefixes & suffixes
                    const stripped = clean.replace(/^(fc|afc|cf|sc|sv|as|rb|vfb)\s+/, '')
                                          .replace(/\s+(fc|afc|cf|sc|sv|united|city|town|wanderers)$/, '')
                                          .trim();
                    if (stripped && stripped !== clean) {
                        key = Object.keys(CLUB_LOGOS).find(k => k.toLowerCase() === stripped);
                        if (key) return CLUB_LOGOS[key];
                        if (aliases[stripped] && CLUB_LOGOS[aliases[stripped]]) {
                            return CLUB_LOGOS[aliases[stripped]];
                        }
                    }

                    // Substring match for safe names with length >= 4
                    key = Object.keys(CLUB_LOGOS).find(k => {
                        const kl = k.toLowerCase();
                        return (clean.length >= 4 && kl.includes(clean)) || (kl.length >= 4 && clean.includes(kl));
                    });
                    return key ? CLUB_LOGOS[key] : null;
                },

                async saveFanProfile() {
                    if (!this.fanForm.name) {
                        this.fanErrorMessage = 'Please enter your name.';
                        return;
                    }
                    this.savingFan = true;
                    this.fanErrorMessage = '';

                    try {
                        const payload = {
                            name: this.fanForm.name,
                            favorite_club: this.fanForm.favoriteClub || 'Arsenal',
                            telegram_chat_id: this.telegramUserId ? String(this.telegramUserId) : null,
                            telegram_username: this.telegramUsername ? this.telegramUsername.replace(/^@/, '') : null,
                            email: this.fanForm.email || null,
                            notify_matchday: Boolean(this.fanForm.notifyMatchday),
                            notify_scores: Boolean(this.fanForm.notifyScores),
                            notify_acoustic: Boolean(this.fanForm.notifyAcoustic),
                            notes: this.telegramUsername ? `Telegram: ${this.telegramUsername} (ID: ${this.telegramUserId || 'N/A'})` : null,
                        };
                        if (this.fanForm.phone) {
                            payload.phone = this.fanForm.phone;
                        }

                        const res = await fetch(`${window.API_BASE}/customer/profile`, {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload),
                        });

                        const json = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            this.fanErrorMessage = json.error || json.message || 'Could not save fan profile.';
                            this.savingFan = false;
                            return;
                        }

                        this.fanSaved = true;
                        // Also persist locally
                        try {
                            localStorage.setItem('GB_DETAILS', JSON.stringify({
                                name: this.fanForm.name,
                                email: this.fanForm.email,
                                favoriteClub: this.fanForm.favoriteClub,
                            }));
                        } catch (e) {}

                        if (this.tgWebApp && this.tgWebApp.HapticFeedback) {
                            this.tgWebApp.HapticFeedback.notificationOccurred('success');
                        }

                    } catch (e) {
                        console.error('Fan profile save error:', e);
                        this.fanErrorMessage = 'Unable to save at the moment. Please try again.';
                    } finally {
                        this.savingFan = false;
                    }
                },

                async loadEvents() {
                    try {
                        const res = await fetch(`${window.API_BASE}/events`);
                        if (res.ok) {
                            const data = await res.json();
                            if (data && Array.isArray(data.events) && data.events.length > 0) {
                                this.events = data.events;
                            }
                        }
                        this.computeSaturdays();
                    } catch (e) {
                        console.error('Events failed:', e);
                        this.computeSaturdays();
                    }
                },

                async loadMatches() {
                    try {
                        const res = await fetch(`${window.API_BASE}/matches?current=1`);
                        const data = await res.json();
                        this.allMatches = data.matches || [];

                        // Featured match: first live, or first upcoming, or first match
                        const liveMatch = this.allMatches.find(m => this.isMatchLive(m));
                        const upcomingMatch = this.allMatches.find(m => (m.homeScore === null || m.awayScore === null) && new Date(m.kickoff) > new Date());
                        this.featuredMatch = liveMatch || upcomingMatch || (this.allMatches.length > 0 ? this.allMatches[0] : null);

                        if (this.featuredMatch && this.featuredMatch.kickoff) {
                            const diff = Math.floor((new Date(this.featuredMatch.kickoff).getTime() - Date.now()) / 1000);
                            this.kickoffCountdownSeconds = diff > 0 ? diff : 0;
                            const h = String(Math.floor(this.kickoffCountdownSeconds / 3600)).padStart(2, '0');
                            const m = String(Math.floor((this.kickoffCountdownSeconds % 3600) / 60)).padStart(2, '0');
                            const s = String(this.kickoffCountdownSeconds % 60).padStart(2, '0');
                            this.kickoffCountdownText = `${h}h ${m}m ${s}s`;
                        }

                        if (this.allMatches.length > 0 && this.allMatches[0].kickoff) {
                            this.selectedCalendarDate = this.allMatches[0].kickoff.slice(0, 10);
                        } else {
                            this.selectedCalendarDate = new Date().toISOString().slice(0, 10);
                        }
                    } catch (e) {
                        console.error('Matches failed:', e);
                    }
                },

                async loadMenu() {
                    try {
                        const res = await fetch(`${window.API_BASE}/menu`);
                        if (res.ok) {
                            const data = await res.json();
                            if (data && Array.isArray(data.menu) && data.menu.length > 0) {
                                this.menuItems = data.menu;
                            }
                        }
                    } catch (e) {
                        console.error('Menu failed:', e);
                    }
                },

                get filteredMatches() {
                    const todayEat = new Date().toLocaleDateString('en-CA', { timeZone: 'Africa/Addis_Ababa' });

                    return this.allMatches.filter(m => {
                        // Status & Period Tab Filter
                        if (this.fixtureTab === 'TODAY') {
                            const isToday = m.kickoff && m.kickoff.slice(0, 10) === todayEat;
                            if (!isToday && !this.isMatchLive(m)) return false;
                        } else if (this.fixtureTab === 'SATURDAY') {
                            if (!m.kickoff || !this.isSaturdayDate(m.kickoff.slice(0, 10))) return false;
                        } else if (this.fixtureTab === 'RESULTS') {
                            const isFinished = (m.homeScore !== null && m.awayScore !== null) || m.status === 'FINISHED' || m.status === 'STATUS_FINAL';
                            if (!isFinished) return false;
                        }

                        // Competition filter
                        if (this.matchFilter !== 'ALL' && m.competition !== this.matchFilter) return false;

                        // Team search query
                        if (this.teamSearch && this.teamSearch.trim() !== '') {
                            const q = this.teamSearch.toLowerCase().trim();
                            const home = (m.homeTeam || '').toLowerCase();
                            const away = (m.awayTeam || '').toLowerCase();
                            return home.includes(q) || away.includes(q);
                        }

                        return true;
                    });
                },

                getItemTags(item) {
                    if (!item || !item.name) return [];
                    const name = item.name.toLowerCase();
                    const tags = [];
                    if (name.includes('burger') || name.includes('steak')) {
                        tags.push('🥩 Prime Beef');
                    }
                    if (name.includes('chicken') || name.includes('wings')) {
                        tags.push('🍗 Crispy & Tender');
                    }
                    if (name.includes('pizza') || item.category === 'PIZZA') {
                        tags.push('🍕 Stone-Baked');
                    }
                    if (name.includes('lasagna') || name.includes('buffet')) {
                        tags.push('🔥 Chef Special');
                    }
                    if (name.includes('draft') || name.includes('beer') || name.includes('heineken') || name.includes('arada')) {
                        tags.push('🍺 Ice Cold');
                    }
                    if (name.includes('mojito')) {
                        tags.push('🍸 Fresh Mint & Rum');
                    }
                    return tags;
                },

                get filteredMenuItems() {
                    let items = this.menuItems.filter(item => {
                        if (this.menuCategory !== 'ALL' && item.category !== this.menuCategory) return false;

                        // Subcategory filter
                        if (this.menuSubCategory !== 'ALL') {
                            const n = item.name.toLowerCase();
                            if (this.menuSubCategory === 'BURGERS_SANDWICHES') {
                                if (!n.includes('burger') && !n.includes('sandwich')) return false;
                            } else if (this.menuSubCategory === 'WINGS') {
                                if (!n.includes('wings')) return false;
                            } else if (this.menuSubCategory === 'BUFFET_LASAGNA') {
                                if (!n.includes('buffet') && !n.includes('lasagna')) return false;
                            } else if (this.menuSubCategory === 'MARGHERITA') {
                                if (!n.includes('margherita')) return false;
                            } else if (this.menuSubCategory === 'TUNA') {
                                if (!n.includes('tuna')) return false;
                            } else if (this.menuSubCategory === 'BEERS') {
                                if (!n.includes('draft') && !n.includes('beer') && !n.includes('heineken') && !n.includes('arada')) return false;
                            } else if (this.menuSubCategory === 'COCKTAIL') {
                                if (!n.includes('mojito')) return false;
                            }
                        }

                        // Search query
                        if (this.menuSearch.trim() !== '') {
                            const q = this.menuSearch.toLowerCase();
                            return item.name.toLowerCase().includes(q) || item.description.toLowerCase().includes(q);
                        }
                        return true;
                    });

                    // Sorting
                    if (this.menuSort === 'price_asc') {
                        return items.slice().sort((a, b) => a.price - b.price);
                    } else if (this.menuSort === 'price_desc') {
                        return items.slice().sort((a, b) => b.price - a.price);
                    } else {
                        return items.slice().sort((a, b) => (b.popular ? 1 : 0) - (a.popular ? 1 : 0));
                    }
                },

                openReservationWizard(date = null) {
                    if (date) {
                        this.reserveDraft.date = date.slice(0, 10);
                    } else if (!this.reserveDraft.date && this.upcomingSaturdays.length > 0) {
                        this.reserveDraft.date = this.upcomingSaturdays[0].date;
                    }
                    this.wizardStep = 1;
                    this.setScreen('reserve');
                    this.fetchTablesAvailability();
                },

                async fetchTablesAvailability() {
                    if (!this.reserveDraft.date || !this.reserveDraft.slot) return;
                    try {
                        const res = await fetch(`${window.API_BASE}/tables?date=${this.reserveDraft.date}&slot=${this.reserveDraft.slot}`);
                        const data = await res.json();
                        this.tables = data.tables || [];
                        this.remainingTablesCount = data.remaining || 0;
                    } catch (e) {
                        console.error('Tables failed:', e);
                    }
                },

                goToStep(step) {
                    this.wizardStep = step;
                    if (step === 2) {
                        this.fetchTablesAvailability();
                    }
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    this.syncTelegramButtons();
                },

                selectDraftTable(table) {
                    this.reserveDraft.table = table;
                    if (this.reserveDraft.guests > table.capacity) {
                        this.reserveDraft.guests = table.capacity;
                    }
                    if (this.tgWebApp && this.tgWebApp.HapticFeedback) {
                        this.tgWebApp.HapticFeedback.impactOccurred('medium');
                    }
                },

                async submitReservation() {
                    this.submitting = true;
                    this.errorMessage = '';

                    const payload = {
                        date: this.reserveDraft.date,
                        slot: this.reserveDraft.slot,
                        tableCode: this.reserveDraft.table ? this.reserveDraft.table.code : '',
                        guests: this.reserveDraft.guests,
                        name: this.reserveForm.name,
                        phone: this.reserveForm.phone,
                        email: this.reserveForm.email,
                        request: this.reserveForm.request,
                        matchId: null,
                    };

                    const headers = { 'Content-Type': 'application/json' };
                    if (this.tgWebApp && this.tgWebApp.initData) {
                        headers['x-telegram-init-data'] = this.tgWebApp.initData;
                    }

                    try {
                        const res = await fetch(`${window.API_BASE}/reservations`, {
                            method: 'POST',
                            headers: headers,
                            body: JSON.stringify(payload),
                        });

                        const json = await res.json();

                        if (!res.ok) {
                            this.errorMessage = json.error || 'Failed to complete reservation. Please try again.';
                            this.submitting = false;
                            return;
                        }

                        this.confirmedReservation = json.reservation;
                        this.wizardStep = 5;
                        this.syncTelegramButtons();

                        // Save details if requested
                        if (this.reserveForm.saveDetails) {
                            try {
                                localStorage.setItem('GB_DETAILS', JSON.stringify({
                                    name: this.reserveForm.name,
                                    phone: this.reserveForm.phone,
                                    email: this.reserveForm.email,
                                    favoriteClub: this.fanForm.favoriteClub || 'Arsenal',
                                }));
                            } catch (e) {}
                        }

                        if (this.tgWebApp && this.tgWebApp.HapticFeedback) {
                            this.tgWebApp.HapticFeedback.notificationOccurred('success');
                        }

                        this.searchPhone = this.reserveForm.phone;
                        this.lookupReservations();

                    } catch (e) {
                        this.errorMessage = 'Network error. Please check your connection.';
                    } finally {
                        this.submitting = false;
                    }
                },

                async lookupReservations() {
                    if (!this.searchPhone || this.searchPhone.trim() === '') return;
                    this.searchingReservations = true;
                    this.hasSearched = true;

                    const headers = {};
                    if (this.tgWebApp && this.tgWebApp.initData) {
                        headers['x-telegram-init-data'] = this.tgWebApp.initData;
                    }

                    try {
                        const res = await fetch(`${window.API_BASE}/reservations?phone=${encodeURIComponent(this.searchPhone.trim())}`, {
                            headers: headers
                        });
                        const data = await res.json();
                        this.userReservations = data.reservations || [];
                    } catch (e) {
                        console.error('Lookup failed:', e);
                    } finally {
                        this.searchingReservations = false;
                    }
                },

                async cancelReservation(id) {
                    if (!confirm('Are you sure you want to cancel this reservation? This will free up the table.')) {
                        return;
                    }

                    try {
                        const res = await fetch(`${window.API_BASE}/reservations/${id}`, {
                            method: 'DELETE',
                        });
                        if (res.ok) {
                            await this.lookupReservations();
                            await this.fetchTablesAvailability();
                            if (this.tgWebApp && this.tgWebApp.HapticFeedback) {
                                this.tgWebApp.HapticFeedback.notificationOccurred('warning');
                            }
                        }
                    } catch (e) {
                        alert('Could not cancel reservation.');
                    }
                },

                shareReservation(res) {
                    if (!res) return;
                    const text = `Booking at Gura Bicha Sports Bar! Code: ${res.code} · Table ${res.tableCode} (${res.guests} guests). Live Acoustic Night in Mexico Chamber of Commerce Bldg!`;
                    if (navigator.share) {
                        navigator.share({ title: 'Gura Bicha Reservation', text: text, url: window.location.href });
                    } else {
                        navigator.clipboard.writeText(text);
                        alert('Reservation details copied to clipboard!');
                    }
                },

                // Utility formatters
                teamInitials(team) {
                    if (!team) return '';
                    return team.split(/\s+/).map(w => w[0]).join('').slice(0, 3).toUpperCase();
                },

                formatKickoffTime(iso) {
                    if (!iso) return '';
                    const d = new Date(iso);
                    return d.toLocaleTimeString('en-GB', { timeZone: 'Africa/Addis_Ababa', hour: '2-digit', minute: '2-digit' }) + ' EAT';
                },

                formatMatchDate(iso) {
                    if (!iso) return '';
                    const d = new Date(iso);
                    return d.toLocaleDateString('en-US', { timeZone: 'Africa/Addis_Ababa', weekday: 'short', month: 'short', day: 'numeric' });
                },

                formatDateShort(iso) {
                    if (!iso) return '';
                    const d = new Date(iso);
                    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                },

                formatDateLong(iso) {
                    if (!iso) return '';
                    const d = new Date(iso);
                    return d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
                },

                startBgSlideshow() {
                    setInterval(() => {
                        this.currentBgIndex = (this.currentBgIndex + 1) % this.bgSlides.length;
                    }, 7000);
                },

                startLiveSimulation() {
                    // Countdown clock ticker
                    setInterval(() => {
                        if (this.kickoffCountdownSeconds > 0) {
                            this.kickoffCountdownSeconds--;
                            const h = String(Math.floor(this.kickoffCountdownSeconds / 3600)).padStart(2, '0');
                            const m = String(Math.floor((this.kickoffCountdownSeconds % 3600) / 60)).padStart(2, '0');
                            const s = String(this.kickoffCountdownSeconds % 60).padStart(2, '0');
                            this.kickoffCountdownText = `${h}h ${m}m ${s}s`;
                        }
                    }, 1000);

                    // Live match minute ticker
                    setInterval(() => {
                        if (this.liveMatches.length > 0) {
                            this.liveMatches.forEach(lm => {
                                if (lm.minute < 94) {
                                    lm.minute++;
                                }
                            });
                        }
                    }, 20000);
                },

                // Calendar computed properties & methods
                get calendarMonthTitle() {
                    const monthNamesEn = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    const monthNamesAm = ['ጥር', 'የካቲት', 'መጋቢት', 'ሚያዝያ', 'ግንቦት', 'ሰኔ', 'ሐምሌ', 'ነሐሴ', 'መስከረም', 'ጥቅምት', 'ህዳር', 'ታህሳስ'];
                    const name = this.lang === 'am' ? monthNamesAm[this.calendarMonth - 1] : monthNamesEn[this.calendarMonth - 1];
                    return `${name} ${this.calendarYear}`;
                },

                get calendarMonthMatches() {
                    return this.allMatches.filter(m => {
                        const d = new Date(m.kickoff);
                        return (d.getUTCMonth() + 1) === this.calendarMonth && d.getUTCFullYear() === this.calendarYear;
                    });
                },

                get calendarMonthMatchesCount() {
                    return this.calendarMonthMatches.length;
                },

                get calendarGridCells() {
                    const year = this.calendarYear;
                    const month = this.calendarMonth;
                    const totalDays = new Date(year, month, 0).getDate();
                    const firstDay = new Date(year, month - 1, 1).getDay();
                    const leadingBlanks = (firstDay + 6) % 7;

                    const cells = [];
                    const prevMonthDays = new Date(year, month - 1, 0).getDate();
                    for (let i = leadingBlanks - 1; i >= 0; i--) {
                        const day = prevMonthDays - i;
                        cells.push({
                            key: `prev-${day}`,
                            day: day,
                            date: '',
                            isCurrentMonth: false,
                            isSaturday: false,
                            matchCount: 0,
                            hasLive: false,
                        });
                    }

                    for (let day = 1; day <= totalDays; day++) {
                        const monthStr = String(month).padStart(2, '0');
                        const dayStr = String(day).padStart(2, '0');
                        const iso = `${year}-${monthStr}-${dayStr}`;
                        const dayOfWeek = new Date(year, month - 1, day).getDay();
                        const isSaturday = dayOfWeek === 6;

                        const dayMatches = this.allMatches.filter(m => m.kickoff && m.kickoff.slice(0, 10) === iso);
                        const hasLive = dayMatches.some(m => this.isMatchLive(m));

                        cells.push({
                            key: `curr-${day}`,
                            day: day,
                            date: iso,
                            isCurrentMonth: true,
                            isSaturday: isSaturday,
                            matchCount: dayMatches.length,
                            hasLive: hasLive,
                        });
                    }

                    const remaining = (7 - (cells.length % 7)) % 7;
                    for (let day = 1; day <= remaining; day++) {
                        cells.push({
                            key: `next-${day}`,
                            day: day,
                            date: '',
                            isCurrentMonth: false,
                            isSaturday: false,
                            matchCount: 0,
                            hasLive: false,
                        });
                    }

                    return cells;
                },

                get calendarSelectedMatches() {
                    if (!this.selectedCalendarDate) return [];
                    return this.allMatches.filter(m => m.kickoff && m.kickoff.slice(0, 10) === this.selectedCalendarDate);
                },

                selectCalendarDate(date) {
                    if (!date) return;
                    this.selectedCalendarDate = date;
                    if (this.tgWebApp && this.tgWebApp.HapticFeedback) {
                        this.tgWebApp.HapticFeedback.selectionChanged();
                    }
                },

                selectCalendarMonth(num) {
                    this.calendarMonth = num;
                    this.selectedMonth = num;
                    if (num >= 8) {
                        this.calendarYear = 2026;
                    } else {
                        this.calendarYear = 2027;
                    }
                    const firstWithMatches = this.allMatches.find(m => {
                        const d = new Date(m.kickoff);
                        return (d.getUTCMonth() + 1) === num && d.getUTCFullYear() === this.calendarYear;
                    });
                    if (firstWithMatches) {
                        this.selectedCalendarDate = firstWithMatches.kickoff.slice(0, 10);
                    } else {
                        const monthStr = String(num).padStart(2, '0');
                        this.selectedCalendarDate = `${this.calendarYear}-${monthStr}-01`;
                    }
                },

                prevCalendarMonth() {
                    let m = this.calendarMonth - 1;
                    let y = this.calendarYear;
                    if (m < 1) {
                        m = 12;
                        y--;
                    }
                    this.calendarYear = y;
                    this.selectCalendarMonth(m);
                },

                nextCalendarMonth() {
                    let m = this.calendarMonth + 1;
                    let y = this.calendarYear;
                    if (m > 12) {
                        m = 1;
                        y++;
                    }
                    this.calendarYear = y;
                    this.selectCalendarMonth(m);
                },

                isSaturdayDate(dateStr) {
                    if (!dateStr) return false;
                    const parts = dateStr.split('-');
                    if (parts.length !== 3) return false;
                    const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
                    return d.getDay() === 6;
                },

                isMatchLive(m) {
                    if (!m) return false;
                    if (m.status === 'LIVE' || m.live) return true;
                    if (this.liveMatches && this.liveMatches.some(lm => (lm.homeTeam === m.homeTeam && lm.awayTeam === m.awayTeam) || (lm.id === m.id))) return true;
                    const t = new Date(m.kickoff).getTime();
                    const now = Date.now();
                    return t <= now && (now - t < 2.5 * 60 * 60 * 1000);
                }
            }
        }
    </script>
</body>
</html>
