<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model: CreditCard (Cartão de crédito)
 *
 * @property int $id
 * @property string $name
 * @property string $last_four_digits
 * @property int $closing_day
 * @property int $due_day
 * @property int $user_id
 */
class CreditCard extends Model
{
    protected $fillable = [
        'name',
        'last_four_digits',
        'closing_day',
        'due_day',
        'user_id',
        'credit_limit',
    ];

    protected $casts = [
        'closing_day' => 'integer',
        'due_day' => 'integer',
        'credit_limit' => 'decimal:2',
    ];

    // ─── Relacionamentos ──────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // ─── Helpers ──────────────────────────────────

    /**
     * Retorna as transações pendentes do cartão (fatura aberta).
     */
    public function pendingTransactions()
    {
        return $this->transactions()->where('status', 'PENDING');
    }

    /**
     * Calcula o total da fatura aberta (pendente).
     */
    public function getOpenInvoiceTotal(): float
    {
        return (float) $this->pendingTransactions()->sum('amount');
    }

    /**
     * Retorna label formatado: "Nome •••• 1234"
     */
    public function displayName(): string
    {
        return "{$this->name} •••• {$this->last_four_digits}";
    }

    /**
     * Calcula o limite disponível (limite total - faturas pendentes).
     */
    public function getAvailableLimit(): float
    {
        if ($this->credit_limit <= 0) {
            return 0; // Se não tem limite cadastrado
        }

        $used = $this->getOpenInvoiceTotal(); // Ou sum de todas as compras pendentes
        return max(0, $this->credit_limit - $used);
    }
}
