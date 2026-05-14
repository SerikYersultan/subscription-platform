<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Recurly') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <link href="https://fonts.cdnfonts.com/css/glacial-indifference" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --brand: #dc2626;
            --brand-dark: #991b1b;
            --green: #16a34a;
        }

        body {
            font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f9fafb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }


        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f9fafb;
            z-index: 9999;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-bottom: 15vh;
        }
        .loader-overlay.active { display: flex; }

        .loader-logo {
            height: 380px;
            width: auto;
            animation: elegantFadeIn 1s ease-out forwards;
        }

        .slogan-container {
            margin-top: -120px;
            display: flex;
            gap: 10px;
            font-family: 'Glacial Indifference', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: #4b5563;
            letter-spacing: 0.5px;
            transform: scale(0.7);
        }

        .slogan-word {
            opacity: 0;
            transform: translateX(-15px);
        }

        /*
           Aligned Timing:
           The logo starts appearing at 0s.
           The words start shifting right away to move with the logo.
        */
        .active .word-1 { animation: shiftRight 0.5s ease forwards 0.1s; }
        .active .word-2 { animation: shiftRight 0.5s ease forwards 0.3s; }
        .active .word-3 { animation: shiftRight 0.5s ease forwards 0.5s; }
        .active .word-4 { animation: shiftRight 0.5s ease forwards 0.7s; }

        @keyframes elegantFadeIn {
            0% { transform: scale(0.95); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes shiftRight {
            to { opacity: 1; transform: translateX(0); }
        }


        .auth-wrap {
            width: 100%;
            max-width: 420px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            transform: translateY(-100px);
        }

        .auth-logo {
            text-align: center;
            position: relative;
            top: 85px;
            z-index: 2;
        }

        .auth-custom-logo {
            height: 250px;
            width: auto;
            display: block;
            margin: 0 auto;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.05));
        }

        .auth-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #fee2e2;
            box-shadow: 0 4px 24px rgba(0,0,0,.04);
            padding: 32px;
            position: relative;
            z-index: 1;
        }

        .auth-title { font-size: 22px; font-weight: 700; color: #111827; margin-bottom: 6px; }
        .auth-sub { font-size: 14px; color: #6b7280; margin-bottom: 24px; }

        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px; }

        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            color: #111827;
            outline: none;
            transition: all .15s;
        }

        .form-input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #ef4444, var(--brand));
            transform: translateY(-1px);
        }

        .auth-footer { text-align: center; font-size: 14px; color: #6b7280; margin-top: 20px; }
        .auth-link { color: var(--brand); text-decoration: none; font-size: 13px; }
        .auth-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div id="loader-overlay" class="loader-overlay">
    <img src="{{ asset('images/transparent.png') }}" alt="Loading Logo" class="loader-logo">

    <div class="slogan-container">
        <span class="slogan-word word-1">Financial</span>
        <span class="slogan-word word-2">Clarity</span>
        <span class="slogan-word word-3">On</span>
        <span class="slogan-word word-4">Repeat.</span>
    </div>
</div>

<div class="auth-wrap">
    <div class="auth-logo">
        <a href="/" class="logo-mark">
            <img src="{{ asset('images/transparent.png') }}" alt="Recurly Logo" class="auth-custom-logo">
        </a>
    </div>

    <div class="auth-card">
        {{ $slot }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const overlay = document.getElementById('loader-overlay');

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                overlay.classList.add('active');

                setTimeout(() => {
                    form.submit();
                }, 2000);
            });
        }
    });
</script>

</body>
</html>
