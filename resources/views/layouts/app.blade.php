<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Subscription Platform') }}</title>

    <!-- Apply saved theme before first paint — prevents white flash on reload -->
    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>

    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        /* ─── LIGHT MODE (original, untouched) ──────────────────────── */

        body {
            font-family: 'Figtree', sans-serif;
            background-color: #fffafa;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image:
                radial-gradient(circle at 15% 20%, rgba(220, 38, 38, 0.09) 0 90px, transparent 91px),
                radial-gradient(circle at 85% 18%, rgba(22, 163, 74, 0.08) 0 100px, transparent 101px),
                radial-gradient(circle at 20% 75%, rgba(248, 113, 113, 0.08) 0 120px, transparent 121px),
                radial-gradient(circle at 75% 78%, rgba(34, 197, 94, 0.07) 0 110px, transparent 111px);
        }

        body::after {
            content:
                '✧        ♡        ✦        ✧        ♡        ✦'
                '\A'
                '     ʚ♡ɞ          ✧          ʚ♡ɞ'
                '\A'
                '✦        ✧        ♡        ✦        ✧'
                '\A'
                '        ʚ♡ɞ              ʚ♡ɞ'
                '\A'
                '♡        ✦        ✧        ♡        ✦';
            white-space: pre;
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            color: rgba(220, 38, 38, 0.12);
            font-size: 42px;
            line-height: 150px;
            letter-spacing: 38px;
            padding: 40px 80px;
            opacity: 0.5;
        }

        .page-layer {
            position: relative;
            z-index: 1;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        a { color: #dc2626; }
        a:hover { color: #991b1b; }

        button,
        .btn,
        input[type="submit"] {
            transition: all 0.2s ease;
        }

        /* ─── DARK MODE ──────────────────────────────────────────────── */

        .dark body {
            background-color: #0f172a;
        }

        .dark body::before { opacity: 0.18; }
        .dark body::after  { opacity: 0.06; }

        .dark .glass-panel {
            background: rgba(15, 23, 42, 0.95);
        }

        .dark main a         { color: #f87171; }
        .dark main a:hover   { color: #fca5a5; }

        /* Generic Tailwind bg/text overrides so every page goes dark
           without touching individual blade files */
        .dark .bg-white    { background-color: #1e293b !important; }
        .dark .bg-gray-50  { background-color: #1e293b !important; }
        .dark .bg-gray-100 { background-color: #334155 !important; }
        .dark .bg-gray-200 { background-color: #334155 !important; }

        .dark .text-gray-900 { color: #f1f5f9 !important; }
        .dark .text-gray-800 { color: #e2e8f0 !important; }
        .dark .text-gray-700 { color: #cbd5e1 !important; }
        .dark .text-gray-600 { color: #94a3b8 !important; }
        .dark .text-gray-500 { color: #94a3b8 !important; }

        .dark .border,
        .dark [class*="border-gray"],
        .dark [class*="border-red-1"] { border-color: #334155 !important; }
        .dark .border-t { border-top-color:    #334155 !important; }
        .dark .border-b { border-bottom-color: #334155 !important; }

        .dark .shadow,
        .dark .shadow-sm {
            box-shadow: 0 1px 3px rgba(0,0,0,0.5) !important;
        }

        .dark nav {
            background-color: #0f172a !important;
            border-bottom-color: #1e293b !important;
        }

        .dark .text-red-700  { color: #fca5a5 !important; }
        .dark .border-red-100 { border-color: #334155 !important; }

        /* Smooth transitions on theme switch */
        body, nav, .glass-panel,
        .bg-white, .bg-gray-50, .bg-gray-100 {
            transition: background-color 0.25s ease, border-color 0.25s ease, color 0.25s ease;
        }
    </style>
</head>

<body>
<div class="min-h-screen page-layer">
    @include('layouts.navigation')

    @isset($header)
        <header class="glass-panel border-b border-red-100">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-red-700 leading-tight">
                    {{ $header }}
                </h2>
            </div>
        </header>
    @endisset

    <main>
        @yield('content')
    </main>
</div>

<script>
    function toggleTheme() {
        const html = document.documentElement;
        const isDark = html.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    }
</script>
</body>
</html>
