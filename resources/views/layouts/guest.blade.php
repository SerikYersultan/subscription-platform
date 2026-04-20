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

.auth-card { background: #fff; border-radius: 14px; box-shadow: 0 4px 24px rgba(0,0,0,.08); padding: 28px 28px 24px; }
.auth-title { font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 4px; }
.auth-sub { font-size: 13px; color: #6b7280; margin-bottom: 22px; }
.form-group { margin-bottom: 16px; }
.form-label { display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px; }
.form-input { width: 100%; padding: 9px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; color: #111827; outline: none; transition: border-color .15s; }
.form-input:focus { border-color: var(--brand); }
.form-input.error { border-color: #ef4444; }
.form-error { font-size: 12px; color: #ef4444; margin-top: 4px; }
.form-check { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 13px; color: #374151; }
.btn-primary { width: 100%; padding: 10px; background: var(--brand); color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background .15s; }
.btn-primary:hover { background: var(--brand-dark); }
.auth-footer { text-align: center; font-size: 13px; color: #6b7280; margin-top: 18px; }
.auth-link { color: var(--brand); text-decoration: none; font-size: 12px; }
.auth-link:hover { text-decoration: underline; }
.auth-footer a { color: var(--brand); text-decoration: none; font-weight: 600; }
.status-msg { background: #ecfdf5; border: 1px solid #6ee7b7; border-radius: 8px; padding: 10px 12px; font-size: 13px; color: #065f46; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="auth-wrap">
        <div class="auth-logo">
            <div class="logo-mark">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"/></svg>
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
