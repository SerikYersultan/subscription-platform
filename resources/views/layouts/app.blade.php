<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Simple CSS -->
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f3f4f6; }
        .container { max-width: 1280px; margin: 0 auto; padding: 1rem; }
        .card { background: white; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .btn { background: #4f46e5; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; text-decoration: none; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; }
    </style>
</head>
<body>
    <div class="container">
        @include('layouts.navigation')
        
        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>