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

.auth-card { background: #ffffff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }

.auth-title { font-size: 17px; font-weight: 600; color: #111827; margin-bottom: 4px; }
.auth-sub { font-size: 13px; color: #6b7280; margin-bottom: 22px; }

.form-group { margin-bottom: 16px; }
.form-label { display: block; font-size: 12px; font-weight: 500; color: #374151; margin-bottom: 5px; }
.form-input { width: 100%; padding: 9px 12px; border: 1px solid #d1d5db; border-radius: 7px; font-size: 13px; color: #111827; outline: none; transition: border-color 0.15s, box-shadow 0.15s; background: #fff; }
.form-input:focus { border-color: var(--brand); box-shadow: 0 0 0 3px rgba(26,86,219,0.12); }
.form-input.error { border-color: #ef4444; }
.form-error { font-size: 11px; color: #ef4444; margin-top: 4px; }

.form-check { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }
.form-check input { width: 15px; height: 15px; accent-color: var(--brand); cursor: pointer; }
.form-check label { font-size: 12px; color: #6b7280; cursor: pointer; }

.btn-primary { width: 100%; padding: 10px; background: var(--brand); color: white; border: none; border-radius: 7px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.15s; }
.btn-primary:hover { background: var(--brand-dark); }

.auth-footer { margin-top: 18px; text-align: center; font-size: 12px; color: #6b7280; }
.auth-footer a { color: var(--brand); text-decoration: none; font-weight: 500; }
.auth-footer a:hover { text-decoration: underline; }

.auth-link { font-size: 12px; color: var(--brand); text-decoration: none; }
.auth-link:hover { text-decoration: underline; }

.status-msg { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 7px; padding: 10px 12px; font-size: 12px; color: #166534; margin-bottom: 16px; }
    </style>
</head>
<body>
<div class="auth-wrap">
    <div class="auth-logo">
        <div class="logo-mark">
            <div class="logo-icon">
                <svg viewBox="0 0 16 16"><path d="M8 2C4.7 2 2 4.7 2 8s2.7 6 6 6 6-2.7 6-6-2.7-6-6-6zm0 2c.6 0 1 .4 1 1s-.4 1-1 1-1-.4-1-1 .4-1 1-1zm0 8c-1.7 0-3.1-.8-4-2 .1-1.3 2.7-2 4-2s3.9.7 4 2c-.9 1.2-2.3 2-4 2z"/></svg>
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
