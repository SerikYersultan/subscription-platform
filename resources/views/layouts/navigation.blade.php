<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-indigo-600">
                        Subscription Platform
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('subscriptions.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Subscriptions
                    </a>
                    <a href="{{ route('import.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('import.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Import CSV
                    </a>
                    <a href="{{ route('transactions.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('transactions.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Transactions
                    </a>
                    <a href="{{ route('merchants.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('merchants.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Merchants
                    </a>
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('reports.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Reports
                    </a>
                </div>
            </div>

            <!-- User Menu -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="flex items-center space-x-3">

                    <!-- Dark / Light toggle button -->
                    <button
                        onclick="toggleTheme()"
                        title="Toggle dark mode"
                        style="
                            display: flex;
                            align-items: center;
                            gap: 6px;
                            padding: 6px 12px;
                            border-radius: 20px;
                            border: 1.5px solid #e5e7eb;
                            background: transparent;
                            cursor: pointer;
                            font-size: 13px;
                            font-weight: 500;
                            color: #6b7280;
                            transition: all 0.2s ease;
                        "
                        onmouseenter="this.style.borderColor='#dc2626'; this.style.color='#dc2626';"
                        onmouseleave="this.style.borderColor='#e5e7eb'; this.style.color='#6b7280';"
                        id="theme-btn"
                    >
                        <!-- Moon icon — visible in light mode -->
                        <svg id="icon-moon" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <!-- Sun icon — visible in dark mode -->
                        <svg id="icon-sun" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span id="theme-label">Dark</span>
                    </button>

                    <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    <a href="{{ route('profile.edit') }}" class="text-sm text-gray-500 hover:text-gray-700">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">Log Out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    // Sync button icon + label to current theme on every page load
    (function () {
        const isDark = document.documentElement.classList.contains('dark');
        const btn    = document.getElementById('theme-btn');
        const moon   = document.getElementById('icon-moon');
        const sun    = document.getElementById('icon-sun');
        const label  = document.getElementById('theme-label');

        if (isDark) {
            moon.style.display  = 'none';
            sun.style.display   = 'block';
            label.textContent   = 'Light';
            btn.style.borderColor = '#475569';
            btn.style.color       = '#94a3b8';
            btn.onmouseenter = function() { this.style.borderColor='#f87171'; this.style.color='#f87171'; };
            btn.onmouseleave = function() { this.style.borderColor='#475569'; this.style.color='#94a3b8'; };
        }
    })();

    // Override toggleTheme to also update the button
    const _originalToggle = window.toggleTheme;
    window.toggleTheme = function () {
        _originalToggle();
        const isDark = document.documentElement.classList.contains('dark');
        const btn    = document.getElementById('theme-btn');
        const moon   = document.getElementById('icon-moon');
        const sun    = document.getElementById('icon-sun');
        const label  = document.getElementById('theme-label');

        moon.style.display  = isDark ? 'none'  : 'block';
        sun.style.display   = isDark ? 'block' : 'none';
        label.textContent   = isDark ? 'Light' : 'Dark';

        if (isDark) {
            btn.style.borderColor = '#475569';
            btn.style.color       = '#94a3b8';
            btn.onmouseenter = function() { this.style.borderColor='#f87171'; this.style.color='#f87171'; };
            btn.onmouseleave = function() { this.style.borderColor='#475569'; this.style.color='#94a3b8'; };
        } else {
            btn.style.borderColor = '#e5e7eb';
            btn.style.color       = '#6b7280';
            btn.onmouseenter = function() { this.style.borderColor='#dc2626'; this.style.color='#dc2626'; };
            btn.onmouseleave = function() { this.style.borderColor='#e5e7eb'; this.style.color='#6b7280'; };
        }
    };
</script>
