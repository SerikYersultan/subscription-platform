<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Merchants</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            <form method="GET" action="{{ route('merchants.index') }}">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                    <div class="flex gap-3">
                        <div class="relative flex-1">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search merchant name…" class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                            Search
                        </button>
                        @if (request('search'))
                            <a href="{{ route('merchants.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg transition">
                                Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                @if ($isEmpty)
                    <div class="flex flex-col items-center justify-center py-20 text-center px-4">
                        <span class="text-5xl mb-4">🏪</span>
                        <h3 class="text-lg font-semibold text-gray-700">No merchants yet</h3>
                        <p class="text-sm text-gray-400 mt-1 max-w-xs">
                            @if (request('search'))
                                No results for "<strong>{{ request('search') }}</strong>".
                                <a href="{{ route('merchants.index') }}" class="text-indigo-600 underline">Clear search</a>
                            @else
                                Merchants will appear here after you import and process transactions.
                            @endif
                        </p>
                    </div>
                @else
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr class="text-left text-gray-500">
                                <th class="px-5 py-3 font-medium">Canonical Name</th>
                                <th class="px-5 py-3 font-medium">Aliases</th>
                                <th class="px-5 py-3 font-medium text-right">Transactions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($merchants as $merchant)
                                <tr class="hover:bg-indigo-50/30 transition">
                                    <td class="px-5 py-3 font-semibold text-gray-800">
                                        {{ $merchant->canonical_name ?? $merchant->name }}
                                    </td>
                                    <td class="px-5 py-3 text-gray-400 text-xs">
                                        @if ($merchant->name && $merchant->name !== $merchant->canonical_name)
                                            {{ $merchant->name }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <span class="bg-indigo-100 text-indigo-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                            {{ number_format($merchant->transactions_count) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($merchants->hasPages())
                        <div class="px-5 py-4 border-t border-gray-100">
                            {{ $merchants->links() }}
                        </div>
                    @endif
                @endif

            </div>

            @unless ($isEmpty)
                <p class="text-xs text-gray-400 text-right">
                    {{ $merchants->total() }} merchant(s) found
                </p>
            @endunless

        </div>
    </div>
</x-app-layout>
