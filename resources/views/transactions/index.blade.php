<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transactions</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <form method="GET" action="{{ route('transactions.index') }}" id="filter-form">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

                        <div class="lg:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Search merchant / description</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="e.g. Netflix, Spotify…" class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Date from</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Date to</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 rounded-lg transition">Apply</button>
                            @if (request()->hasAny(['search','date_from','date_to','amount_min','amount_max']))
                                <a href="{{ route('transactions.index') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium py-2 rounded-lg transition">Clear</a>
                            @endif
                        </div>

                    </div>
                </div>
            </form>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if ($isEmpty)
                    <div class="flex flex-col items-center justify-center py-20 text-center px-4">
                        <span class="text-5xl mb-4">📂</span>
                        <h3 class="text-lg font-semibold text-gray-700">No transactions found</h3>
                        <p class="text-sm text-gray-400 mt-1 max-w-xs">
                            @if (request()->hasAny(['search','date_from','date_to']))
                                Try adjusting your filters or <a href="{{ route('transactions.index') }}" class="text-indigo-600 underline">clear them</a>.
                            @else
                                Import a CSV file to get started.
                            @endif
                        </p>
                        @unless(request()->hasAny(['search','date_from','date_to']))
                            <a href="{{ route('import.index') }}" class="mt-4 inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                                📥 Import CSV
                            </a>
                        @endunless
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr class="text-left text-gray-500">
                                    <th class="px-5 py-3 font-medium">Date</th>
                                    <th class="px-5 py-3 font-medium">Merchant</th>
                                    <th class="px-5 py-3 font-medium">Description</th>
                                    <th class="px-5 py-3 font-medium text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($transactions as $tx)
                                    <tr class="hover:bg-indigo-50/30 transition">
                                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($tx->transaction_date)->format('M j, Y') }}</td>
                                        <td class="px-5 py-3 font-medium text-gray-800">{{ $tx->merchant_name ?? '—' }}</td>
                                        <td class="px-5 py-3 text-gray-500 max-w-xs truncate">{{ $tx->description ?? '—' }}</td>
                                        <td class="px-5 py-3 text-right font-semibold {{ $tx->amount < 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ number_format(abs($tx->amount), 2) }}
                                            <span class="text-xs font-normal text-gray-400">{{ $tx->currency ?? '' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($transactions->hasPages())
                        <div class="px-5 py-4 border-t border-gray-100">{{ $transactions->links() }}</div>
                    @endif
                @endif
            </div>

            @unless ($isEmpty)
                <p class="text-xs text-gray-400 text-right">
                    Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }} transactions
                </p>
            @endunless

        </div>
    </div>
</x-app-layout>