<?php

namespace App\Services;

use App\Models\CreditCard;
use Illuminate\Support\Facades\DB;

/**
 * Service: CreditCardService
 *
 * RF06 — Cadastrar cartões de crédito e exibir valor total da fatura.
 * RF08 — Baixar parcelas automaticamente ao pagar a fatura.
 */
class CreditCardService
{
    public function __construct(
        private TransactionService $transactionService,
    ) {}

    /**
     * Cria um novo cartão de crédito.
     */
    public function create(array $data): CreditCard
    {
        return CreditCard::create($data);
    }

    /**
     * Atualiza um cartão de crédito.
     */
    public function update(CreditCard $card, array $data): CreditCard
    {
        $card->update($data);

        return $card->fresh();
    }

    /**
     * Calcula o total da fatura aberta de um cartão.
     * RF06 — Exibir valor total antes da cobrança chegar.
     */
    public function getOpenInvoiceTotal(CreditCard $card): float
    {
        return (float) $card->transactions()
            ->where('status', 'PENDING')
            ->where('transaction_type', 'EXPENSE')
            ->sum('amount');
    }

    /**
     * Paga a fatura do cartão: marca todas as transações pendentes como pagas.
     * RF08 — Baixar parcelas automaticamente ao pagar a fatura.
     */
    public function payInvoice(CreditCard $card, int $bankAccountId): void
    {
        DB::transaction(function () use ($card, $bankAccountId) {
            $pendingTransactions = $card->transactions()
                ->where('status', 'PENDING')
                ->where('transaction_type', 'EXPENSE')
                ->get();

            foreach ($pendingTransactions as $transaction) {
                // Atualiza a conta bancária usada para pagar a fatura
                $transaction->update(['bank_account_id' => $bankAccountId]);
                $this->transactionService->markAsPaid($transaction);
            }
        });
    }

    /**
     * Lista cartões de crédito de um usuário com totais das faturas.
     */
    public function listWithTotals(int $userId)
    {
        $cards = CreditCard::where('user_id', $userId)->get();

        return $cards->map(function ($card) {
            $card->open_invoice_total = $this->getOpenInvoiceTotal($card);
            $card->pending_count = $card->transactions()
                ->where('status', 'PENDING')
                ->count();

            return $card;
        });
    }
}
