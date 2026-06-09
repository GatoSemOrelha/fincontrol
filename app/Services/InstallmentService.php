<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Installment;
use App\Models\Invoice;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Service: InstallmentService
 *
 * RF07 — Registrar compras parceladas e controlar quantas parcelas faltam.
 */
class InstallmentService
{
    /**
     * Cria uma compra parcelada: gera N transações (uma por parcela) e vincula a uma invoice.
     *
     * @param  array  $baseData  Dados base da transação (description, bank_account_id, etc.)
     * @param  float  $totalAmount  Valor total da compra
     * @param  int  $numberOfInstallments  Número de parcelas
     * @param  Carbon  $firstDueDate  Data de vencimento da primeira parcela
     */
    public function createInstallmentPurchase(
        array $baseData,
        float $totalAmount,
        int $numberOfInstallments,
        Carbon $firstDueDate
    ): Invoice {
        return DB::transaction(function () use ($baseData, $totalAmount, $numberOfInstallments, $firstDueDate) {
            // Criar a invoice
            $invoice = Invoice::create([
                'invoice_number' => 'PARC-'.now()->format('YmdHis'),
                'description' => $baseData['description']." ({$numberOfInstallments}x)",
                'total_amount' => $totalAmount,
                'due_date' => $firstDueDate,
                'status' => InvoiceStatus::PENDING,
                'user_id' => $baseData['user_id'],
            ]);

            $installmentAmount = round($totalAmount / $numberOfInstallments, 2);

            // Ajustar a última parcela para fechar o valor total exato
            $lastInstallmentAmount = $totalAmount - ($installmentAmount * ($numberOfInstallments - 1));

            for ($i = 1; $i <= $numberOfInstallments; $i++) {
                $dueDate = (clone $firstDueDate)->addMonths($i - 1);
                $amount = ($i === $numberOfInstallments) ? $lastInstallmentAmount : $installmentAmount;

                // Criar a transação da parcela
                $transaction = Transaction::create(array_merge($baseData, [
                    'description' => $baseData['description']." ({$i}/{$numberOfInstallments})",
                    'amount' => $amount,
                    'due_date' => $dueDate,
                    'status' => 'PENDING',
                ]));

                // Vincular parcela à invoice
                Installment::create([
                    'invoice_id' => $invoice->id,
                    'transaction_id' => $transaction->id,
                    'installment_number' => $i,
                ]);
            }

            return $invoice;
        });
    }

    /**
     * Retorna o número de parcelas restantes (pendentes) de uma invoice.
     */
    public function getRemainingInstallments(Invoice $invoice): int
    {
        return $invoice->installments()
            ->whereHas('transaction', function ($q) {
                $q->where('status', 'PENDING');
            })
            ->count();
    }

    /**
     * Retorna o total pago de uma invoice.
     */
    public function getPaidTotal(Invoice $invoice): float
    {
        return (float) $invoice->installments()
            ->whereHas('transaction', function ($q) {
                $q->where('status', 'PAID');
            })
            ->with('transaction')
            ->get()
            ->sum('transaction.amount');
    }

    /**
     * Atualiza o status da invoice baseado nas parcelas pagas.
     */
    public function updateInvoiceStatus(Invoice $invoice): void
    {
        $total = $invoice->installments()->count();
        $paid = $invoice->installments()
            ->whereHas('transaction', function ($q) {
                $q->where('status', 'PAID');
            })
            ->count();

        if ($paid === 0) {
            $invoice->update(['status' => InvoiceStatus::PENDING]);
        } elseif ($paid < $total) {
            $invoice->update(['status' => InvoiceStatus::PARTIALLY_PAID]);
        } else {
            $invoice->update(['status' => InvoiceStatus::PAID]);
        }
    }
}
