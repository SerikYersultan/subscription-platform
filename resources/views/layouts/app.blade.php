<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Subscription Platform') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
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

        a {
            color: #dc2626;
        }

        a:hover {
            color: #991b1b;
        }

        button,
        .btn,
        input[type="submit"] {
            transition: all 0.2s ease;
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
</body>
</html>
