<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Import CSV Transactions</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
                    <span class="text-lg">✅</span>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
                    <span class="text-lg">❌</span>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 text-lg mb-1">Upload a CSV File</h3>
                <p class="text-sm text-gray-500 mb-6">
                    The file should contain transaction records. Supported format:
                    <code class="bg-gray-100 rounded px-1">date, amount, merchant, description</code>
                </p>

                <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data" id="import-form" novalidate>
                    @csrf

                    <label for="csv_file" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-indigo-300 rounded-xl bg-indigo-50 hover:bg-indigo-100 cursor-pointer transition group" id="drop-zone">
                        <div class="flex flex-col items-center gap-2 text-indigo-500 group-hover:text-indigo-700 transition">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-sm font-medium" id="drop-label">
                                <span class="underline">Click to browse</span> or drag & drop your CSV here
                            </p>
                            <p class="text-xs text-gray-400">CSV only, max 10 MB</p>
                        </div>
                        <input id="csv_file" name="csv_file" type="file" accept=".csv,.txt" class="hidden" onchange="updateLabel(this)">
                    </label>

                    @error('csv_file')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="mt-5 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition disabled:opacity-50" id="submit-btn">
                            📥 Upload & Process
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Recent Imports</h3>

                @if ($recentImports->isEmpty())
                    <p class="text-sm text-gray-400 italic">No imports yet. Upload your first CSV above.</p>
                @else
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-100">
                                <th class="pb-2 font-medium">Date</th>
                                <th class="pb-2 font-medium text-right">Transactions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($recentImports as $import)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 text-gray-700">{{ \Carbon\Carbon::parse($import->import_date)->format('M j, Y') }}</td>
                                    <td class="py-2 text-right text-indigo-600 font-medium">{{ number_format($import->total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>
    </div>

    <script>
        function updateLabel(input) {
            const label = document.getElementById('drop-label');
            if (input.files && input.files[0]) {
                label.textContent = '📄 ' + input.files[0].name;
            }
        }

        document.getElementById('import-form').addEventListener('submit', function () {
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.textContent = '⏳ Uploading…';
        });

        const zone = document.getElementById('drop-zone');
        zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('border-indigo-500'); });
        zone.addEventListener('dragleave', () => zone.classList.remove('border-indigo-500'));
        zone.addEventListener('drop', e => {
            e.preventDefault();
            zone.classList.remove('border-indigo-500');
            const file = e.dataTransfer.files[0];
            if (file) {
                const input = document.getElementById('csv_file');
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                updateLabel(input);
            }
        });
    </script>
</x-app-layout>