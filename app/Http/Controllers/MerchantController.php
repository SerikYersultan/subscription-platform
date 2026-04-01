<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchant;

class MerchantController extends Controller
{
    public function index(Request $request)
    {
        $merchants = collect();
        $isEmpty = true;

        try {
            $query = Merchant::query();

            // Поиск по имени
            if ($search = $request->input('search')) {
                $query->where('canonical_name', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%");
            }

            $merchants = $query->withCount('transactions')
                               ->orderBy('canonical_name')
                               ->paginate(20)
                               ->withQueryString();

            $isEmpty = $merchants->isEmpty();

        } catch (\Throwable $e) {
            // Если таблиц еще нет, показываем пустую страницу
            $isEmpty = true;
        }

        return view('merchants.index', compact('merchants', 'isEmpty'));
    }
}