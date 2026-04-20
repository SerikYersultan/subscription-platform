<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Добавьте это
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Добавьте это

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'merchant_id',
        'name',
        'amount',
        'currency',
        'billing_cycle',
        'status',
        'confidence_score',
        'next_billing_date',
        'detected_at',
    ];

    protected $casts = [
        'next_billing_date' => 'date',
        'detected_at' => 'datetime',
        'amount' => 'decimal:2',
        'confidence_score' => 'decimal:2',
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