<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Добавьте это
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Добавьте это

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'merchant_name',
        'amount',
        'status',
        'next_charge_date',
    ];

    protected $casts = [
        'next_charge_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Оставляем ТОЛЬКО этот метод (с типизацией)
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }
}