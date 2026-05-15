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
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --brand: #111827;
            --brand-dark: #111827;
            --green: #111827;
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

        /* =========================
           LOADER
        ========================= */

        .loader-overlay {
            position: fixed;
            inset: 0;
            background: #f9fafb;
            z-index: 9999;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .loader-overlay.active {
            display: flex;
        }

        .recurly-loader {
            display: flex;
            align-items: center;
            gap: 2px;
            font-family: 'Glacial Indifference', sans-serif;
            font-size: 92px;
            font-weight: 900;
            letter-spacing: -5px;
        }

        .letter {
            color: var(--green);
            opacity: 0;
            transform: translateY(40px) scale(0.2) rotate(-18deg);
            animation: recurlyPop 0.75s cubic-bezier(.2, 1.6, .4, 1) forwards;
        }


        .letter:nth-child(1) { animation-delay: 0s; }
        .letter:nth-child(2) { animation-delay: .08s; }
        .letter:nth-child(3) { animation-delay: .16s; }
        .letter:nth-child(4) { animation-delay: .24s; }
        .letter:nth-child(5) { animation-delay: .32s; }
        .letter:nth-child(6) { animation-delay: .40s; }
        .letter:nth-child(7) { animation-delay: .48s; }

        @keyframes recurlyPop {
            0% {
                opacity: 0;
                transform: translateY(45px) scale(0.1) rotate(-20deg);
                filter: blur(8px);
            }

            60% {
                opacity: 1;
                transform: translateY(-12px) scale(1.25) rotate(5deg);
                filter: blur(0);
            }

            80% {
                transform: translateY(5px) scale(0.92) rotate(-2deg);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1) rotate(0);
            }
        }



        /* =========================
           AUTH
        ========================= */

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

        .auth-title {
            font-size: 22px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }

        .auth-sub {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

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
            background: linear-gradient(135deg, #111827, #000000);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s ease;
            box-shadow: 0 4px 14px rgba(0,0,0,0.12);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1f2937, #111827);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.18);
        }

        .auth-footer {
            text-align: center;
            font-size: 14px;
            color: #6b7280;
            margin-top: 20px;
        }

        .auth-link {
            color: var(--brand);
            text-decoration: none;
            font-size: 13px;
        }

        .auth-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div id="loader-overlay" class="loader-overlay">

    <div class="recurly-loader">
        <span class="letter">R</span>
        <span class="letter">e</span>
        <span class="letter">c</span>
        <span class="letter">u</span>
        <span class="letter">r</span>
        <span class="letter">l</span>
        <span class="letter">y</span>
    </div>

</div>

<div class="auth-wrap">



    <div class="auth-card">
        {{ $slot }}
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const form = document.querySelector('form');
        const overlay = document.getElementById('loader-overlay');

        if (form) {

            form.addEventListener('submit', function (e) {

                e.preventDefault();

                overlay.classList.add('active');

                setTimeout(() => {
                    form.submit();
                }, 2200);

            });

        }

    });
</script>

</body>
</html>
