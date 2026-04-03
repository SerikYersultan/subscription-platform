<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="p-4 bg-white shadow rounded border">
                            <h2 class="font-semibold">Total Monthly Spend</h2>
                            <p class="text-xl">${{ number_format($totalMonthlySpend, 2) }}</p>
                        </div>

                        <div class="p-4 bg-white shadow rounded border">
                            <h2 class="font-semibold">Active Subscriptions</h2>
                            <p class="text-xl">{{ $activeSubscriptionsCount }}</p>
                        </div>

                        <div class="p-4 bg-white shadow rounded border">
                            <h2 class="font-semibold">Upcoming Charges</h2>
                            <p class="text-xl">{{ $upcomingCharges->count() }}</p>
                        </div>
                    </div>

                    <div class="bg-white shadow rounded p-4">
                        <h2 class="text-lg font-semibold mb-3">Recent Alerts</h2>

                        @forelse($alerts as $alert)
                            <div class="border-b py-2">
                                <p>{{ $alert->message }}</p>
                                <small class="text-gray-500">{{ $alert->created_at->format('d.m.Y H:i') }}</small>
                            </div>
                        @empty
                            <p>No alerts yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>