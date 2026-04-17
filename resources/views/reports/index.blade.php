@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-6">Reports & Analytics</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-blue-100 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold">Total Subscriptions</h3>
                        <p class="text-3xl font-bold">{{ $totalSubscriptions }}</p>
                    </div>
                    
                    <div class="bg-green-100 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold">Active Subscriptions</h3>
                        <p class="text-3xl font-bold">{{ $activeSubscriptions }}</p>
                    </div>
                    
                    <div class="bg-yellow-100 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold">Monthly Spend</h3>
                        <p class="text-3xl font-bold">${{ number_format($totalMonthlySpend, 2) }}</p>
                    </div>
                    
                    <div class="bg-red-100 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold">Unread Alerts</h3>
                        <p class="text-3xl font-bold">{{ $unreadAlerts }}</p>
                    </div>
                </div>
                
                @if($recentAlerts->count() > 0)
                <div class="mt-8">
                    <h3 class="text-xl font-bold mb-4">Recent Alerts</h3>
                    <table class="min-w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left">Type</th>
                                <th class="px-4 py-2 text-left">Message</th>
                                <th class="px-4 py-2 text-left">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAlerts as $alert)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $alert->type ?? 'Info' }}</td>
                                <td class="px-4 py-2">{{ $alert->message }}</td>
                                <td class="px-4 py-2">{{ $alert->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    能得到
                </div>
                @endif
                
                <div class="mt-8 flex gap-4">
                    <a href="{{ route('reports.pdf') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Download Subscriptions PDF
                    </a>
                    <a href="{{ route('reports.alerts.pdf') }}" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                        Download Alerts PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection