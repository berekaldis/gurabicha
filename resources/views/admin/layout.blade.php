<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') — Gura Bicha Sports Bar</title>
    <meta name="theme-color" content="#050505">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">

    <!-- Immediate Theme Initialization (prevents flash of dark/light theme) -->
    <script>
        (function() {
            const savedAdminTheme = localStorage.getItem('GB_ADMIN_THEME') || localStorage.getItem('GB_THEME') || 'dark';
            document.documentElement.classList.remove('dark', 'light');
            document.documentElement.classList.add(savedAdminTheme);
        })();
    </script>

    <!-- Fonts: Oswald & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
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
                            glow: 'rgba(245, 189, 25, 0.2)',
                        },
                        admin: {
                            bg: '#08080a',
                            sidebar: '#0e0e12',
                            card: '#141418',
                            panel: '#1b1b22',
                            border: '#27272f',
                        }
                    },
                    fontFamily: {
                        display: ['Oswald', 'sans-serif'],
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        body {
            background-color: #08080a;
            color: #f4f4f5;
            font-family: 'Inter', sans-serif;
        }
        .font-display {
            font-family: 'Oswald', sans-serif;
            letter-spacing: 0.03em;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #09090b;
        }
        ::-webkit-scrollbar-thumb {
            background: #27272a;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #f5bd19;
        }

        /* ------------------------------------------------------------- */
        /* HIGH-CONTRAST LIGHT MODE ARCHITECTURE FOR ADMIN PANEL         */
        /* ------------------------------------------------------------- */
        html.light {
            color-scheme: light;
        }
        html.light body {
            background-color: #f1f5f9 !important; /* Soft slate-100 canvas */
            color: #0f172a !important; /* Deep slate-900 high contrast */
        }
        html.light .bg-admin-bg {
            background-color: #f1f5f9 !important;
        }
        html.light .bg-admin-sidebar,
        html.light [class*="bg-admin-sidebar"] {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.03) !important;
        }
        html.light header,
        html.light header.sticky {
            background-color: rgba(255, 255, 255, 0.96) !important;
            border-bottom: 1px solid #e2e8f0 !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04) !important;
        }
        html.light header h2 {
            color: #0f172a !important; /* Bold Slate-900 */
        }
        html.light header span.text-slate-500,
        html.light header span.text-zinc-400,
        html.light header span {
            color: #475569 !important; /* Clear Slate-600 */
        }
        html.light header #addisClock {
            color: #b45309 !important; /* Rich Amber */
        }
        html.light .bg-admin-card,
        html.light [class*="bg-admin-card"] {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.06) !important;
        }
        html.light .bg-admin-panel,
        html.light [class*="bg-admin-panel"] {
            background-color: #f8fafc !important; /* Slate-50 */
            border-color: #e2e8f0 !important;
        }
        html.light .border-admin-border {
            border-color: #e2e8f0 !important;
        }

        /* High-contrast typography */
        html.light .text-white:not(button *):not(.bg-gold *):not(.bg-red-600 *):not(.bg-red-700 *):not(.bg-emerald-600 *):not(.bg-blue-600 *):not(.bg-purple-600 *):not(.bg-sky-600 *):not(.bg-black *):not([class*="bg-black/"] *):not(.bg-zinc-900 *):not(.bg-zinc-950 *) {
            color: #0f172a !important; /* Slate 900 */
        }
        html.light .text-zinc-100:not(button *):not([class*="bg-black/"] *):not(.bg-zinc-900 *):not(.bg-zinc-950 *) {
            color: #0f172a !important;
        }
        html.light .text-zinc-200:not(button *):not([class*="bg-black/"] *):not(.bg-zinc-900 *):not(.bg-zinc-950 *) {
            color: #1e293b !important; /* Slate 800 */
        }
        html.light .text-zinc-300:not([class*="bg-black/"] *):not(.bg-zinc-900 *):not(.bg-zinc-950 *) {
            color: #334155 !important; /* Slate 700 */
        }
        html.light .text-zinc-400:not([class*="bg-black/"] *):not(.bg-zinc-900 *):not(.bg-zinc-950 *) {
            color: #475569 !important; /* Slate 600 high legibility */
        }
        html.light .text-zinc-500:not([class*="bg-black/"] *):not(.bg-zinc-900 *):not(.bg-zinc-950 *) {
            color: #64748b !important; /* Slate 500 */
        }

        /* Rich Golden Amber in Light Mode (replaces hard-to-read bright yellow) */
        html.light .text-gold {
            color: #b45309 !important; /* Amber-700 */
        }
        html.light .text-gold-light {
            color: #92400e !important; /* Amber-800 */
        }

        /* High-contrast accent colors on white cards */
        html.light .text-emerald-400,
        html.light .text-emerald-300 {
            color: #047857 !important; /* Emerald-700 */
        }
        html.light .text-blue-400,
        html.light .text-blue-300 {
            color: #1d4ed8 !important; /* Blue-700 */
        }
        html.light .text-purple-400,
        html.light .text-purple-300 {
            color: #6b21a8 !important; /* Purple-800 */
        }
        html.light .text-cyan-400,
        html.light .text-cyan-300,
        html.light .text-sky-400,
        html.light .text-sky-300 {
            color: #0369a1 !important; /* Sky-700 */
        }
        html.light .text-amber-400,
        html.light .text-amber-300 {
            color: #b45309 !important; /* Amber-700 */
        }
        html.light .text-red-400,
        html.light .text-red-300 {
            color: #b91c1c !important; /* Red-700 */
        }

        /* ------------------------------------------------------------- */
        /* CRITICAL PROTECTION FOR DARK BADGES, OVERLAYS & PHOTO PILLS   */
        /* (Prevents text from becoming invisible black-on-black)        */
        /* ------------------------------------------------------------- */
        html.light .bg-black,
        html.light [class*="bg-black/"],
        html.light .bg-zinc-900,
        html.light .bg-zinc-950 {
            color: #ffffff !important;
        }

        html.light .bg-black .text-white,
        html.light [class*="bg-black/"] .text-white,
        html.light .bg-zinc-900 .text-white,
        html.light .bg-zinc-950 .text-white,
        html.light .bg-red-600,
        html.light .bg-red-600 *,
        html.light button.bg-red-600,
        html.light button.bg-red-600 *,
        html.light .bg-red-700,
        html.light .bg-red-700 *,
        html.light .bg-emerald-600,
        html.light .bg-emerald-600 *,
        html.light button.bg-emerald-600,
        html.light button.bg-emerald-600 *,
        html.light .bg-blue-600,
        html.light .bg-blue-600 *,
        html.light button.bg-blue-600,
        html.light button.bg-blue-600 *,
        html.light .bg-purple-600,
        html.light .bg-purple-600 *,
        html.light button.bg-purple-600,
        html.light button.bg-purple-600 *,
        html.light .bg-sky-600,
        html.light .bg-sky-600 *,
        html.light button.bg-sky-600,
        html.light button.bg-sky-600 * {
            color: #ffffff !important;
        }

        html.light .bg-gold,
        html.light .bg-gold *,
        html.light button.bg-gold,
        html.light button.bg-gold *,
        html.light a.bg-gold,
        html.light a.bg-gold * {
            color: #000000 !important;
            font-weight: 700;
        }

        html.light .bg-admin-panel:not(button):not(a),
        html.light .bg-slate-100:not(button):not(a) {
            color: #0f172a !important;
        }

        html.light button.bg-admin-panel,
        html.light a.bg-admin-panel {
            background-color: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #1e293b !important;
        }
        html.light button.bg-admin-panel:hover,
        html.light a.bg-admin-panel:hover {
            background-color: #f1f5f9 !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }
        html.light button.bg-admin-panel.text-gold,
        html.light button.bg-admin-panel .text-gold,
        html.light a.bg-admin-panel.text-gold,
        html.light a.bg-admin-panel .text-gold {
            color: #b45309 !important;
            font-weight: 700;
        }

        /* Gold text on dark image overlays & badges stays vibrant gold (#f5bd19), NOT dark amber */
        html.light .bg-black .text-gold,
        html.light [class*="bg-black/"] .text-gold,
        html.light .bg-zinc-900 .text-gold,
        html.light .bg-zinc-950 .text-gold,
        html.light button.bg-gold span.bg-black {
            color: #f5bd19 !important;
        }

        /* Muted text on dark photo overlays stays light slate-300 */
        html.light .bg-black .text-zinc-400,
        html.light [class*="bg-black/"] .text-zinc-400,
        html.light .bg-zinc-900 .text-zinc-400 {
            color: #cbd5e1 !important;
        }

        /* ------------------------------------------------------------- */
        /* GRADIENTS & BANNERS IN LIGHT MODE                             */
        /* ------------------------------------------------------------- */
        /* Quick operations toolbar gradient */
        html.light .bg-gradient-to-r.from-admin-card {
            background-image: linear-gradient(to right, #ffffff, #f8fafc, #ffffff) !important;
            border-color: #fcd34d !important;
        }

        /* Event card bottom image fade to white */
        html.light .from-admin-card {
            --tw-gradient-from: #ffffff var(--tw-gradient-from-position) !important;
            --tw-gradient-to: rgb(255 255 255 / 0) var(--tw-gradient-to-position) !important;
            --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to) !important;
        }
        html.light .via-admin-card\/40 {
            --tw-gradient-to: rgb(255 255 255 / 0) var(--tw-gradient-to-position) !important;
            --tw-gradient-stops: var(--tw-gradient-from), rgba(255, 255, 255, 0.4) var(--tw-gradient-via-position), var(--tw-gradient-to) !important;
        }

        /* Saturday Acoustic Live stage banner */
        html.light .from-amber-950\/40 {
            background-image: linear-gradient(to right, #fef3c7, #fde68a, #fef3c7) !important;
            border-color: #fcd34d !important;
            color: #92400e !important;
        }
        html.light .from-amber-950\/40 span {
            color: #92400e !important;
        }

        /* Live internet sync notice banner */
        html.light .bg-purple-950\/30 {
            background-color: #faf5ff !important;
            border-color: #d8b4fe !important;
            color: #581c87 !important;
        }
        html.light .bg-purple-950\/30 strong,
        html.light .bg-purple-950\/30 span {
            color: #581c87 !important;
        }
        html.light .bg-purple-950\/30 .bg-purple-900\/60 {
            background-color: #ede9fe !important;
            border-color: #c4b5fd !important;
            color: #6b21a8 !important;
        }

        /* ------------------------------------------------------------- */
        /* BADGES & STATUS PILLS IN LIGHT MODE                           */
        /* ------------------------------------------------------------- */
        /* Premier League & Champions League badges */
        html.light .bg-purple-900\/60 {
            background-color: #f3e8ff !important;
            border: 1px solid #d8b4fe !important;
            color: #6b21a8 !important;
        }
        html.light .bg-cyan-900\/60 {
            background-color: #e0f2fe !important;
            border: 1px solid #7dd3fc !important;
            color: #0369a1 !important;
        }

        /* Scheduled / Live / Finished match status pills */
        html.light .bg-blue-900\/60 {
            background-color: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            color: #1d4ed8 !important;
        }
        html.light .bg-amber-900\/60 {
            background-color: #fef3c7 !important;
            border: 1px solid #fde68a !important;
            color: #92400e !important;
        }
        html.light .bg-red-900\/60 {
            background-color: #fee2e2 !important;
            border: 1px solid #fca5a5 !important;
            color: #991b1b !important;
        }
        html.light .bg-zinc-800.text-zinc-300,
        html.light span.bg-zinc-800 {
            background-color: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
        }

        /* Floor table booked/free indicators */
        html.light .bg-red-900\/80 {
            background-color: #fee2e2 !important;
            color: #991b1b !important;
        }
        html.light .bg-emerald-900\/80 {
            background-color: #dcfce7 !important;
            color: #166534 !important;
        }
        html.light .border-red-900\/60.bg-red-950\/20 {
            background-color: #fff1f2 !important;
            border-color: #fecdd3 !important;
        }
        html.light .border-emerald-900\/60.bg-emerald-950\/20 {
            background-color: #f0fdf4 !important;
            border-color: #bbf7d0 !important;
        }
        html.light .border-red-900\/40 {
            border-color: #fecdd3 !important;
        }
        html.light .border-emerald-900\/40 {
            border-color: #bbf7d0 !important;
        }

        /* Supported Club Badges in Fan Club Registry */
        html.light .bg-zinc-800.text-gold,
        html.light span.bg-zinc-800.text-gold {
            background-color: #fffbeb !important;
            color: #92400e !important;
            border-color: #fcd34d !important;
        }

        /* Other standard badges */
        html.light .bg-emerald-950\/60,
        html.light .bg-emerald-950\/40,
        html.light .bg-emerald-950 {
            background-color: #ecfdf5 !important;
            color: #065f46 !important;
            border-color: #a7f3d0 !important;
        }
        html.light .bg-blue-950\/60,
        html.light .bg-blue-950\/40,
        html.light .bg-blue-950 {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            border-color: #bfdbfe !important;
        }
        html.light .bg-red-950\/60,
        html.light .bg-red-950\/40,
        html.light .bg-red-950 {
            background-color: #fef2f2 !important;
            color: #991b1b !important;
            border-color: #fecaca !important;
        }
        html.light .bg-amber-950\/60,
        html.light .bg-amber-950\/40,
        html.light .bg-amber-950,
        html.light .bg-amber-500\/20 {
            background-color: #fffbeb !important;
            color: #92400e !important;
            border-color: #fde68a !important;
        }
        html.light .bg-sky-950\/60,
        html.light .bg-sky-950\/40,
        html.light .bg-sky-950\/20,
        html.light .bg-sky-900\/40,
        html.light .bg-sky-950 {
            background-color: #f0f9ff !important;
            color: #075985 !important;
            border-color: #bae6fd !important;
        }
        html.light .bg-purple-950\/60,
        html.light .bg-purple-950\/40,
        html.light .bg-purple-950 {
            background-color: #faf5ff !important;
            color: #6b21a8 !important;
            border-color: #e9d5ff !important;
        }

        /* Secondary Button Hover States in Light Mode */
        html.light .hover\:bg-zinc-800:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }
        html.light .hover\:text-white:hover {
            color: #0f172a !important;
        }

        /* Form Inputs & Controls */
        html.light input,
        html.light select,
        html.light textarea {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }
        html.light input:focus,
        html.light select:focus,
        html.light textarea:focus {
            border-color: #b45309 !important;
            outline: none;
            box-shadow: 0 0 0 2px rgba(180, 83, 9, 0.2) !important;
        }
        html.light input::placeholder,
        html.light textarea::placeholder {
            color: #64748b !important;
        }

        /* Date picker calendar indicator */
        html.light input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(30%) sepia(90%) saturate(1500%) hue-rotate(10deg);
            opacity: 0.9 !important;
            cursor: pointer;
        }

        /* Navigation Links Hover & Active in Sidebar */
        html.light .hover\:bg-admin-panel:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }
        html.light a.bg-gold\/15 {
            background-color: #fef3c7 !important; /* Amber-100 */
            color: #92400e !important; /* Amber-800 */
            border-color: #fcd34d !important; /* Amber-300 */
        }

        /* Tables in Light Mode */
        html.light th {
            background-color: #f8fafc !important;
            color: #0f172a !important;
            border-bottom: 2px solid #e2e8f0 !important;
            font-weight: 700 !important;
        }
        html.light td {
            color: #1e293b !important;
            border-color: #f1f5f9 !important;
        }
        html.light tr:hover td {
            background-color: #f8fafc !important;
        }

        /* Modals in Light Mode */
        html.light [x-show*="Modal"] .bg-admin-card {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        }
        html.light [x-show*="Modal"] label {
            color: #1e293b !important;
            font-weight: 600 !important;
        }

        /* Retain High-Energy Gold Action Buttons */
        html.light .bg-gold,
        html.light button.bg-gold,
        html.light a.bg-gold {
            background-color: #f5bd19 !important;
            color: #000000 !important;
        }
        html.light .bg-gold:hover,
        html.light button.bg-gold:hover,
        html.light a.bg-gold:hover {
            background-color: #ffd34d !important;
            color: #000000 !important;
        }
        html.light .shadow-gold\/20,
        html.light .shadow-gold\/25 {
            box-shadow: 0 4px 14px 0 rgba(245, 189, 25, 0.35) !important;
        }

        /* Scrollbar in Light Mode */
        html.light ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        html.light ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
        }
        html.light ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="min-h-screen bg-admin-bg flex transition-colors duration-150" x-data="{
    sidebarOpen: false,
    sidebarCollapsed: localStorage.getItem('GB_ADMIN_SIDEBAR_COLLAPSED') === 'true',
    toggleSidebar() {
        if (window.innerWidth < 768) {
            this.sidebarOpen = !this.sidebarOpen;
        } else {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('GB_ADMIN_SIDEBAR_COLLAPSED', this.sidebarCollapsed);
        }
    },
    theme: localStorage.getItem('GB_ADMIN_THEME') || localStorage.getItem('GB_THEME') || 'dark',
    toggleTheme() {
        this.theme = this.theme === 'dark' ? 'light' : 'dark';
        localStorage.setItem('GB_ADMIN_THEME', this.theme);
        localStorage.setItem('GB_THEME', this.theme);
        document.documentElement.classList.remove('dark', 'light');
        document.documentElement.classList.add(this.theme);
    }
}">

    <!-- Mobile Backdrop for Sidebar Drawer -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm md:hidden" x-cloak>
    </div>

    <!-- SIDEBAR -->
    <aside :class="[
            sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
            sidebarCollapsed ? 'md:w-20' : 'md:w-64'
        ]"
        class="fixed md:static inset-y-0 left-0 z-50 w-64 bg-admin-sidebar border-r border-admin-border flex flex-col transition-all duration-300 ease-in-out shrink-0">
        <!-- Brand Header (Clean brand layout, no duplicate toggle button) -->
        <div class="border-b border-admin-border flex items-center transition-all duration-300"
             :class="sidebarCollapsed ? 'p-3 justify-center' : 'p-5 justify-between'">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 overflow-hidden">
                <img src="{{ asset('images/logo.png') }}" alt="Gura Bicha Logo" class="w-9 h-9 rounded-xl object-contain shadow-md shadow-gold/20 shrink-0 border border-gold/40 bg-black p-0.5">
                <div x-show="!sidebarCollapsed" class="transition-opacity duration-200">
                    <h1 class="font-display font-bold text-white text-base tracking-wider leading-none">GURA BICHA</h1>
                    <span class="text-[10px] text-gold uppercase tracking-widest font-semibold block mt-0.5">Management Console</span>
                </div>
            </a>

            <!-- Mobile Close Button (only inside mobile drawer) -->
            <button @click="sidebarOpen = false" class="md:hidden text-zinc-400 hover:text-white p-1 text-sm" title="Close Drawer">✕</button>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 py-4 space-y-4 overflow-y-auto">
            <!-- GROUP: OPERATIONS -->
            <div class="space-y-1">
                <span x-show="!sidebarCollapsed" class="px-3 text-[10px] font-bold uppercase tracking-wider text-zinc-500 block">Venue Operations</span>
                <div x-show="sidebarCollapsed" class="hidden md:block border-t border-admin-border/50 my-2 mx-1"></div>
                
                <a href="{{ route('admin.dashboard') }}"
                   :title="sidebarCollapsed ? 'Dashboard' : ''"
                   :class="sidebarCollapsed ? 'md:justify-center md:px-2' : 'justify-between px-3'"
                   class="flex items-center py-2.5 rounded-xl text-sm font-medium transition relative {{ request()->routeIs('admin.dashboard') ? 'bg-gold/15 text-gold border border-gold/40 shadow-sm' : 'text-zinc-400 hover:text-white hover:bg-admin-panel' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-200">Dashboard</span>
                    </div>
                </a>

                <a href="{{ route('admin.reservations.index') }}"
                   :title="sidebarCollapsed ? 'Reservations' : ''"
                   :class="sidebarCollapsed ? 'md:justify-center md:px-2' : 'justify-between px-3'"
                   class="flex items-center py-2.5 rounded-xl text-sm font-medium transition relative {{ request()->routeIs('admin.reservations.*') ? 'bg-gold/15 text-gold border border-gold/40 shadow-sm' : 'text-zinc-400 hover:text-white hover:bg-admin-panel' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-200">Reservations</span>
                    </div>
                    @if(isset($sidebarCounts['reservations']) && $sidebarCounts['reservations'] > 0)
                        <span x-show="!sidebarCollapsed" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gold/20 text-gold">{{ $sidebarCounts['reservations'] }}</span>
                        <span x-show="sidebarCollapsed" class="hidden md:block absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-gold"></span>
                    @endif
                </a>

                <a href="{{ route('admin.tables.index') }}"
                   :title="sidebarCollapsed ? 'Venue Tables (T1–T6)' : ''"
                   :class="sidebarCollapsed ? 'md:justify-center md:px-2' : 'justify-between px-3'"
                   class="flex items-center py-2.5 rounded-xl text-sm font-medium transition relative {{ request()->routeIs('admin.tables.*') ? 'bg-gold/15 text-gold border border-gold/40 shadow-sm' : 'text-zinc-400 hover:text-white hover:bg-admin-panel' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-200">Venue Tables</span>
                    </div>
                    <span x-show="!sidebarCollapsed" class="text-[10px] px-2 py-0.5 rounded bg-admin-panel text-zinc-400">T1–T6</span>
                </a>
            </div>

            <!-- GROUP: ENTERTAINMENT & FOOD -->
            <div class="space-y-1">
                <span x-show="!sidebarCollapsed" class="px-3 text-[10px] font-bold uppercase tracking-wider text-zinc-500 block">Live Stage & Dining</span>
                <div x-show="sidebarCollapsed" class="hidden md:block border-t border-admin-border/50 my-2 mx-1"></div>

                <a href="{{ route('admin.events.index') }}"
                   :title="sidebarCollapsed ? 'Saturday Acoustic' : ''"
                   :class="sidebarCollapsed ? 'md:justify-center md:px-2' : 'justify-between px-3'"
                   class="flex items-center py-2.5 rounded-xl text-sm font-medium transition relative {{ request()->routeIs('admin.events.*') ? 'bg-gold/15 text-gold border border-gold/40 shadow-sm' : 'text-zinc-400 hover:text-white hover:bg-admin-panel' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-200">Saturday Acoustic</span>
                    </div>
                    <span x-show="!sidebarCollapsed" class="text-[9px] px-1.5 py-0.5 rounded font-bold uppercase bg-amber-500/20 text-amber-300">LIVE</span>
                </a>

                <a href="{{ route('admin.matches.index') }}"
                   :title="sidebarCollapsed ? 'Football Fixtures' : ''"
                   :class="sidebarCollapsed ? 'md:justify-center md:px-2' : 'justify-between px-3'"
                   class="flex items-center py-2.5 rounded-xl text-sm font-medium transition relative {{ request()->routeIs('admin.matches.*') ? 'bg-gold/15 text-gold border border-gold/40 shadow-sm' : 'text-zinc-400 hover:text-white hover:bg-admin-panel' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-200">Football Fixtures</span>
                    </div>
                    @if(isset($sidebarCounts['matches']) && $sidebarCounts['matches'] > 0)
                        <span x-show="!sidebarCollapsed" class="text-[10px] px-2 py-0.5 rounded bg-admin-panel text-zinc-400">{{ $sidebarCounts['matches'] }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.menu.index') }}"
                   :title="sidebarCollapsed ? 'Food & Bar Menu' : ''"
                   :class="sidebarCollapsed ? 'md:justify-center md:px-2' : 'justify-between px-3'"
                   class="flex items-center py-2.5 rounded-xl text-sm font-medium transition relative {{ request()->routeIs('admin.menu.*') ? 'bg-gold/15 text-gold border border-gold/40 shadow-sm' : 'text-zinc-400 hover:text-white hover:bg-admin-panel' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-200">Food & Bar Menu</span>
                    </div>
                    @if(isset($sidebarCounts['menu']) && $sidebarCounts['menu'] > 0)
                        <span x-show="!sidebarCollapsed" class="text-[10px] px-2 py-0.5 rounded bg-admin-panel text-zinc-400">{{ $sidebarCounts['menu'] }} items</span>
                    @endif
                </a>
            </div>

            <!-- GROUP: AUDIENCE & SETTINGS -->
            <div class="space-y-1">
                <span x-show="!sidebarCollapsed" class="px-3 text-[10px] font-bold uppercase tracking-wider text-zinc-500 block">Patrons & System</span>
                <div x-show="sidebarCollapsed" class="hidden md:block border-t border-admin-border/50 my-2 mx-1"></div>

                <a href="{{ route('admin.customers.index') }}"
                   :title="sidebarCollapsed ? 'Fan Club & Alerts' : ''"
                   :class="sidebarCollapsed ? 'md:justify-center md:px-2' : 'justify-between px-3'"
                   class="flex items-center py-2.5 rounded-xl text-sm font-medium transition relative {{ request()->routeIs('admin.customers.*') ? 'bg-gold/15 text-gold border border-gold/40 shadow-sm' : 'text-zinc-400 hover:text-white hover:bg-admin-panel' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-200">Fan Club & Alerts</span>
                    </div>
                    @if(isset($sidebarCounts['customers']) && $sidebarCounts['customers'] > 0)
                        <span x-show="!sidebarCollapsed" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300">{{ $sidebarCounts['customers'] }} fans</span>
                        <span x-show="sidebarCollapsed" class="hidden md:block absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-emerald-400"></span>
                    @endif
                </a>

                <a href="{{ route('admin.feedbacks.index') }}"
                   :title="sidebarCollapsed ? 'Feedback & Suggestions' : ''"
                   :class="sidebarCollapsed ? 'md:justify-center md:px-2' : 'justify-between px-3'"
                   class="flex items-center py-2.5 rounded-xl text-sm font-medium transition relative {{ request()->routeIs('admin.feedbacks.*') ? 'bg-gold/15 text-gold border border-gold/40 shadow-sm' : 'text-zinc-400 hover:text-white hover:bg-admin-panel' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-200">Feedback & Ideas</span>
                    </div>
                    @php
                        $newFeedbackCount = \App\Models\Feedback::where('status', 'NEW')->count();
                    @endphp
                    @if($newFeedbackCount > 0)
                        <span x-show="!sidebarCollapsed" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">{{ $newFeedbackCount }} new</span>
                        <span x-show="sidebarCollapsed" class="hidden md:block absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    @endif
                </a>

                <a href="{{ route('admin.telegram.index') }}"
                   :title="sidebarCollapsed ? 'Telegram Config' : ''"
                   :class="sidebarCollapsed ? 'md:justify-center md:px-2' : 'justify-between px-3'"
                   class="flex items-center py-2.5 rounded-xl text-sm font-medium transition relative {{ request()->routeIs('admin.telegram.*') ? 'bg-gold/15 text-gold border border-gold/40 shadow-sm' : 'text-zinc-400 hover:text-white hover:bg-admin-panel' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18.716-.967 4.144-1.365 5.836-.168.716-.434.956-.69 0.98-.557.051-.98-.369-1.52-.723-.845-.554-1.323-.899-2.143-1.44-.948-.624-.333-.967.207-1.528.141-.147 2.598-2.383 2.645-2.586.006-.025.011-.122-.047-.174-.058-.052-.144-.034-.206-.02-.088.02-1.488.948-4.202 2.781-.397.272-.757.406-1.079.399-.356-.008-1.041-.202-1.551-.368-.625-.203-1.121-.311-1.078-.656.022-.18.271-.364.747-.553 2.923-1.274 4.873-2.115 5.85-2.524 2.785-1.163 3.364-1.365 3.743-1.371.083-.001.27.02.39.119.102.083.13.195.143.274.013.08.029.262.016.406z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-200">Telegram Config</span>
                    </div>
                    <span x-show="!sidebarCollapsed" class="text-[9px] px-1.5 py-0.5 rounded font-bold uppercase bg-sky-500/20 text-sky-400">BOT</span>
                </a>

                <a href="{{ route('admin.settings.index') }}"
                   :title="sidebarCollapsed ? 'Venue Settings & QR' : ''"
                   :class="sidebarCollapsed ? 'md:justify-center md:px-2' : 'justify-between px-3'"
                   class="flex items-center py-2.5 rounded-xl text-sm font-medium transition relative {{ request()->routeIs('admin.settings.*') ? 'bg-gold/15 text-gold border border-gold/40 shadow-sm' : 'text-zinc-400 hover:text-white hover:bg-admin-panel' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-200">Venue Settings & QR</span>
                    </div>
                </a>

                @if(Auth::user() && Auth::user()->canManageUsers())
                    <a href="{{ route('admin.users.index') }}"
                       :title="sidebarCollapsed ? 'Users & Roles' : ''"
                       :class="sidebarCollapsed ? 'md:justify-center md:px-2' : 'justify-between px-3'"
                       class="flex items-center py-2.5 rounded-xl text-sm font-medium transition relative {{ request()->routeIs('admin.users.*') ? 'bg-gold/15 text-gold border border-gold/40 shadow-sm' : 'text-zinc-400 hover:text-white hover:bg-admin-panel' }}">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-200">Users & Roles</span>
                        </div>
                        <span x-show="!sidebarCollapsed" class="text-[9px] px-1.5 py-0.5 rounded font-bold uppercase bg-gold/20 text-gold">SUPER</span>
                    </a>
                @endif
            </div>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-3 border-t border-admin-border space-y-2.5 transition-all duration-300">
            <a href="{{ url('/') }}" target="_blank"
               :title="sidebarCollapsed ? 'View Public Site' : ''"
               :class="sidebarCollapsed ? 'md:justify-center md:p-2.5' : 'justify-between px-3 py-2'"
               class="flex items-center rounded-lg bg-admin-panel text-xs text-zinc-300 hover:text-gold transition">
                <span x-show="!sidebarCollapsed">View Public Site</span>
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>

            <div class="flex items-center pt-2 border-t border-admin-border/50"
                 :class="sidebarCollapsed ? 'md:flex-col md:space-y-2 md:pt-2' : 'justify-between'">
                <div class="flex items-center space-x-2" :title="sidebarCollapsed ? '{{ Auth::user()->name ?? 'Admin' }} ({{ Auth::user()->role ?? 'ADMIN' }})' : ''">
                    <div class="w-7 h-7 rounded-full bg-gold/20 border border-gold/40 text-gold flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div x-show="!sidebarCollapsed" class="truncate max-w-[110px]">
                        <span class="text-xs font-semibold text-white block truncate">{{ Auth::user()->name ?? 'Admin' }}</span>
                        <div class="flex items-center space-x-1">
                            <span class="text-[9px] px-1.5 py-0.2 rounded bg-gold/20 text-gold font-bold uppercase tracking-wider">{{ str_replace('_', ' ', Auth::user()->role ?? 'ADMIN') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-1" :class="sidebarCollapsed ? 'md:flex-col md:space-x-0 md:space-y-1' : ''">
                    <button type="button" @click="toggleTheme()" :title="theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode'" class="text-zinc-400 hover:text-gold transition p-1.5 rounded-lg hover:bg-admin-panel">
                        <svg x-show="theme === 'dark'" class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <svg x-show="theme === 'light'" class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                    </button>
                    <a href="{{ route('admin.password.change') }}" title="Change Password" class="text-zinc-400 hover:text-gold transition p-1.5 rounded-lg hover:bg-admin-panel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" title="Logout" class="text-zinc-400 hover:text-red-400 transition p-1.5 rounded-lg hover:bg-admin-panel">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Navbar -->
        <header class="bg-white/95 dark:bg-[#0e0e12]/95 backdrop-blur-md border-b border-slate-200 dark:border-admin-border px-4 py-3 sticky top-0 z-30 flex items-center justify-between transition-colors duration-150">
            <div class="flex items-center space-x-3">
                <!-- ONLY 1 SINGLE UNIVERSAL SIDEBAR TOGGLE IN ENTIRE UI (Dock layout icon, NOT a hamburger) -->
                <button type="button" @click="toggleSidebar()"
                        :title="sidebarCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'"
                        class="flex items-center justify-center p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-admin-panel dark:hover:bg-zinc-800 border border-slate-200 dark:border-admin-border text-slate-700 dark:text-zinc-300 hover:text-amber-700 dark:hover:text-gold transition shadow-sm">
                    <!-- Icon when sidebar is expanded (collapse arrow) -->
                    <svg x-show="!sidebarCollapsed" class="w-5 h-5 text-slate-600 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.8"/>
                        <path d="M9 4v16" stroke-width="1.8"/>
                        <path d="M14 9l-3 3 3 3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <!-- Icon when sidebar is collapsed (expand arrow) -->
                    <svg x-show="sidebarCollapsed" class="w-5 h-5 text-amber-700 dark:text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                        <rect x="3" y="4" width="18" height="16" rx="2" stroke-width="1.8"/>
                        <path d="M9 4v16" stroke-width="1.8"/>
                        <path d="M12 9l3 3-3 3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <div>
                    <h2 class="font-display font-bold text-lg text-slate-900 dark:text-white uppercase tracking-wider leading-none">
                        @yield('header_title', 'Management Console')
                    </h2>
                    <span class="text-[11px] text-slate-500 dark:text-zinc-400 font-medium hidden sm:inline block mt-0.5">
                        Gura Bicha Sports Bar & Acoustic Lounge · Addis Ababa
                    </span>
                </div>
            </div>

            <div class="flex items-center space-x-2 sm:space-x-3 text-xs">
                <!-- Live Server Pill -->
                <div class="hidden lg:flex items-center space-x-1.5 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-[11px] font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Live Console</span>
                </div>

                <!-- View Public Website -->
                <a href="{{ url('/') }}" target="_blank" class="hidden sm:flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-admin-panel dark:hover:bg-zinc-800 border border-slate-200 dark:border-admin-border text-slate-700 dark:text-zinc-300 hover:text-amber-700 dark:hover:text-gold transition">
                    <svg class="w-3.5 h-3.5 text-amber-600 dark:text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span>Public App</span>
                </a>

                <!-- Theme Switcher: Light / Dark Mode Toggle -->
                <button type="button" @click="toggleTheme()" :title="theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode'" class="flex items-center space-x-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-admin-panel dark:hover:bg-zinc-800 border border-slate-200 dark:border-admin-border text-amber-700 dark:text-gold hover:border-amber-400 dark:hover:border-gold/60 transition shadow-sm font-semibold text-xs">
                    <svg x-show="theme === 'dark'" class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg x-show="theme === 'light'" class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span class="hidden sm:inline" x-text="theme === 'dark' ? 'Light' : 'Dark'"></span>
                </button>

                <!-- Addis Ababa Clock -->
                <div class="flex items-center space-x-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-admin-panel border border-slate-200 dark:border-admin-border text-slate-600 dark:text-zinc-400">
                    <svg class="w-3.5 h-3.5 text-amber-600 dark:text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-amber-700 dark:text-gold font-bold font-mono text-xs" id="addisClock">--:-- EAT</span>
                </div>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="flex-1 p-4 md:p-6 max-w-7xl w-full mx-auto space-y-6">

            <!-- FLASH ALERTS -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-800 text-emerald-200 text-sm flex items-center justify-between shadow-lg">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-950/60 border border-red-800 text-red-200 text-sm flex items-center justify-between shadow-lg">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-200">✕</button>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-xl bg-red-950/60 border border-red-800 text-red-200 text-sm space-y-1">
                    <div class="font-bold">Please correct the following errors:</div>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function updateAddisClock() {
            const el = document.getElementById('addisClock');
            if (!el) return;
            const now = new Date();
            el.textContent = now.toLocaleTimeString('en-GB', { timeZone: 'Africa/Addis_Ababa', hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' EAT';
        }
        setInterval(updateAddisClock, 1000);
        updateAddisClock();
    </script>
</body>
</html>
