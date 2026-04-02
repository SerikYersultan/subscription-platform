<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class ImportController extends Controller
{
    public function index()
    {
        $recentImports = collect();

        // достать последние загрузки, если база уже создан
        try {
            $recentImports = Transaction::where('user_id', auth()->id())
                ->selectRaw('DATE(created_at) as import_date, COUNT(*) as total')
                ->groupBy('import_date')
                ->orderByDesc('import_date')
                ->limit(5)
                ->get();
        } catch (\Throwable $e) {
            // Если таблиц еще не, игнорируем ошибк
        }

        return view('import.index', compact('recentImports'));
    }

    public function store(Request $request)
    {
        // Строгая валидация входящего файл
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ], [
            'csv_file.required' => 'Please select a CSV file to upload.',
            'csv_file.mimes' => 'Only CSV files are allowed.',
            'csv_file.max' => 'The file must not exceed 10 MB.',
        ]);

        // Сохраняем файл
        $path = $request->file('csv_file')->store('imports');

        // O: Раскомментировать
        // \App\Jobs\CsvImportJob::dispatch(auth()->id(), $path);

        return redirect()->route('import.index')
            ->with('success', 'File uploaded successfully! Processing will begin shortly.');
    }
}