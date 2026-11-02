<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Gura Bicha Sports Bar</title>
    <meta name="theme-color" content="#050505">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo.svg') }}">

    <!-- Immediate Theme Initialization -->
    <script>
        (function() {
            const savedAdminTheme = localStorage.getItem('GB_ADMIN_THEME') || localStorage.getItem('GB_THEME') || 'dark';
            document.documentElement.classList.remove('dark', 'light');
            document.documentElement.classList.add(savedAdminTheme);
        })();
    </script>

    <!-- Fonts -->
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
                            glow: 'rgba(245, 189, 25, 0.25)',
                        },
                        admin: {
                            bg: '#08080a',
                            card: '#121216',
                            panel: '#1a1a20',
                            border: '#272730',
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

    <style>
        /* Light Mode Overrides for Login */
        html.light body {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }
        html.light .bg-admin-bg {
            background-color: #f1f5f9 !important;
        }
        html.light .bg-admin-card {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08) !important;
        }
        html.light .bg-admin-panel {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }
        html.light .border-admin-border {
            border-color: #e2e8f0 !important;
        }
        html.light .text-white {
            color: #0f172a !important;
        }
        html.light .text-zinc-300 {
            color: #1e293b !important;
        }
        html.light .text-zinc-400 {
            color: #475569 !important;
        }
        html.light .text-zinc-500 {
            color: #64748b !important;
        }
        html.light .text-gold {
            color: #b45309 !important;
        }
        html.light input[type="email"],
        html.light input[type="password"] {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }
        html.light input:focus {
            border-color: #b45309 !important;
            box-shadow: 0 0 0 2px rgba(180, 83, 9, 0.2) !important;
        }
        html.light .bg-gold {
            background-color: #f5bd19 !important;
            color: #000000 !important;
        }
        html.light .bg-gold:hover {
            background-color: #ffd34d !important;
        }
    </style>
</head>
<body class="min-h-screen bg-admin-bg flex items-center justify-center p-4 relative transition-colors duration-150">

    <!-- FLOATING LIGHT/DARK TOGGLE -->
    <div class="fixed top-4 right-4 z-50">
        <button type="button" onclick="toggleAdminTheme()" id="themeToggleBtn" class="flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-admin-card border border-admin-border text-gold hover:border-gold/60 transition shadow-sm font-semibold text-xs">
            <span id="themeToggleIcon">☀️</span>
            <span id="themeToggleText" class="hidden sm:inline">Light Mode</span>
        </button>
    </div>

    <div class="max-w-md w-full space-y-6">

        <!-- LOGO & BRAND -->
        <div class="text-center space-y-2">
            <img src="{{ asset('images/logo.png') }}" alt="Gura Bicha Sport Bar Logo" class="w-20 h-20 rounded-2xl object-contain mx-auto shadow-2xl shadow-gold/25 border border-gold/40 bg-black p-1">
            <h1 class="font-display font-bold text-2xl tracking-wider text-white uppercase">
                Gura Bicha Management
            </h1>
            <p class="text-xs text-zinc-400">Sign in to manage bookings, tables, matches & menu</p>
        </div>

        <!-- LOGIN CARD -->
        <div class="bg-admin-card border border-admin-border rounded-2xl p-6 sm:p-8 shadow-2xl space-y-5">

            @if(session('info'))
                <div class="p-3.5 rounded-xl bg-blue-950/60 border border-blue-800 text-blue-200 text-xs">
                    {{ session('info') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-red-950/60 border border-red-800 text-red-200 text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1.5">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus class="w-full bg-admin-panel border border-admin-border rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-gold transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-300 mb-1.5">Password</label>
                    <input type="password" id="password" name="password" required class="w-full bg-admin-panel border border-admin-border rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-gold transition">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center space-x-2 text-zinc-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-admin-border text-gold focus:ring-gold bg-admin-panel">
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-gold hover:bg-gold-light text-black font-semibold text-sm transition shadow-lg shadow-gold/20 flex items-center justify-center space-x-2">
                    <span>Sign In to Console</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

        </div>

        <div class="text-center">
            <a href="{{ url('/') }}" class="text-xs text-zinc-500 hover:text-gold transition">
                ← Return to Public Website
            </a>
        </div>

    </div>

    <script>
        function updateToggleBtn() {
            const isLight = document.documentElement.classList.contains('light');
            const icon = document.getElementById('themeToggleIcon');
            const text = document.getElementById('themeToggleText');
            if (icon && text) {
                icon.textContent = isLight ? '🌙' : '☀️';
                text.textContent = isLight ? 'Dark Mode' : 'Light Mode';
            }
        }

        function toggleAdminTheme() {
            const isLight = document.documentElement.classList.contains('light');
            const nextTheme = isLight ? 'dark' : 'light';
            document.documentElement.classList.remove('dark', 'light');
            document.documentElement.classList.add(nextTheme);
            localStorage.setItem('GB_ADMIN_THEME', nextTheme);
            localStorage.setItem('GB_THEME', nextTheme);
            updateToggleBtn();
        }

        document.addEventListener('DOMContentLoaded', updateToggleBtn);
    </script>
</body>
</html>
