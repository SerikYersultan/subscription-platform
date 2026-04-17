<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SubTrack') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
:root { --brand: #1a56db; --brand-dark: #1040b0; }

.auth-wrap { width: 100%; max-width: 400px; padding: 16px; }

.auth-logo { text-align: center; margin-bottom: 28px; }
.logo-mark { display: inline-flex; align-items: center; gap: 10px; }
.logo-icon { width: 36px; height: 36px; background: var(--brand); border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.logo-icon svg { width: 18px; height: 18px; fill: white; }
.logo-text { font-size: 18px; font-weight: 700; color: #111827; }
.logo-sub { font-size: 11px; color: #6b7280; margin-top: 1px; }

        <!-- Scripts -->
        {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

        
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>
            <div>
                <div class="logo-text">SubTrack</div>
                <div class="logo-sub">Intelligence Platform</div>
            </div>
        </div>
    </div>
    <div class="auth-card">
        {{ $slot }}
    </div>
</div>
</body>
</html>
