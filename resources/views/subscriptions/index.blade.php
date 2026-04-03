@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-4">My Subscriptions</h2>
                
                @if($subscriptions->count() > 0)
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">Merchant</th>
                                <th class="px-4 py-2 text-left">Amount</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Next Charge Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $subscription)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $subscription->merchant_name }}</td>
                                <td class="px-4 py-2">${{ number_format($subscription->amount, 2) }}</td>
                                <td class="px-4 py-2">{{ $subscription->status }}</td>
                                <td class="px-4 py-2">{{ $subscription->next_charge_date }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500">No subscriptions found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection