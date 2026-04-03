@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-4">Alerts</h2>
                
                @if($alerts->count() > 0)
                    <table class="min-w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 text-left">Type</th>
                                <th class="px-4 py-2 text-left">Message</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alerts as $alert)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $alert->type }}</td>
                                <td class="px-4 py-2">{{ $alert->message }}</td>
                                <td class="px-4 py-2">{{ $alert->status }}</td>
                                <td class="px-4 py-2">{{ $alert->created_at }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500">No alerts found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection