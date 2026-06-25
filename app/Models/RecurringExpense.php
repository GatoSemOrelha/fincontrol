<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model: RecurringExpense (Despesa fixa recorrente)
 * RF09 — Template para recriação automática mensal.
 */
class RecurringExpense extends Model
{
    protected $fillable = [
        'description',
        'amount',
        'day_of_month',
        'bank_account_id',
        'category_id',
        'user_id',
        'is_active',
        'credit_card_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'day_of_month' => 'integer',
        'is_active' => 'boolean',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
