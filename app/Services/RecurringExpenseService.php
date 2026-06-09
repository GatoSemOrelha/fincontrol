<?php

namespace App\Services;

use App\Models\RecurringExpense;
use App\Models\Transaction;
use Carbon\Carbon;

/**
 * Service: RecurringExpenseService
 *
 * RF09 — Automatizar recriação de despesas fixas mensais.
 */
class RecurringExpenseService
{
    /**
     * Recria todas as despesas fixas ativas para um determinado mês.
     * Chamado pelo Command RecreateRecurringExpenses.
     *
     * @return int Número de lançamentos criados
     */
    public function recreateForMonth(int $year, int $month): int
    {
        $expenses = RecurringExpense::active()->with('user')->get();
        $created = 0;

        foreach ($expenses as $expense) {
            // Verificar se já existe um lançamento recorrente para este mês
            $exists = Transaction::where('user_id', $expense->user_id)
                ->where('description', $expense->description)
                ->where('is_recurring', true)
                ->whereYear('due_date', $year)
                ->whereMonth('due_date', $month)
                ->exists();

            if ($exists) {
                continue;
            }

            // Determinar o dia de vencimento (ajustar para meses com menos dias)
            $maxDay = Carbon::create($year, $month)->daysInMonth;
            $day = min($expense->day_of_month, $maxDay);

            Transaction::create([
                'description' => $expense->description,
                'amount' => $expense->amount,
                'due_date' => Carbon::create($year, $month, $day),
                'transaction_type' => 'EXPENSE',
                'status' => 'PENDING',
                'is_recurring' => true,
                'user_id' => $expense->user_id,
                'bank_account_id' => $expense->bank_account_id,
                'category_id' => $expense->category_id,
            ]);

            $created++;
        }

        return $created;
    }

    /**
     * Lista despesas recorrentes de um usuário.
     */
    public function listForUser(int $userId)
    {
        return RecurringExpense::where('user_id', $userId)
            ->with(['bankAccount', 'category'])
            ->orderBy('description')
            ->get();
    }
}
