<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = collect();
        $isEmpty = true;

        try {
            $query = Transaction::where('user_id', auth()->id())->with('merchant');

            // Поиск по мерчанту или описанию
            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('merchant_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Фильтры по дате
            if ($dateFrom = $request->input('date_from')) {
                $query->whereDate('transaction_date', '>=', $dateFrom);
            }
            if ($dateTo = $request->input('date_to')) {
                $query->whereDate('transaction_date', '<=', $dateTo);
            }

            // Фильтры по сумме
            if ($amountMin = $request->input('amount_min')) {
                $query->where('amount', '>=', $amountMin);
            }
            if ($amountMax = $request->input('amount_max')) {
                $query->where('amount', '<=', $amountMax);
            }

            $transactions = $query->orderByDesc('transaction_date')->paginate(20)->withQueryString();
            $isEmpty = $transactions->isEmpty();

        } catch (\Throwable $e) {
            // Если таблиц еще нет, показываем пустую страницу
            $isEmpty = true;
        }

        return view('transactions.index', compact('transactions', 'isEmpty'));
    }
}
