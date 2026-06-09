<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

/**
 * Policy: TransactionPolicy
 *
 * RF01 — Controle de acesso.
 * RF03 — Bloquear edição de lançamentos pagos.
 */
class TransactionPolicy
{
    /**
     * Qualquer usuário autenticado pode ver lançamentos.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Qualquer usuário autenticado pode ver um lançamento.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    /**
     * Apenas admin pode criar lançamentos.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admin pode editar, mas NÃO se o lançamento estiver pago (RF03).
     */
    public function update(User $user, Transaction $transaction): bool
    {
        if ($transaction->isPaid()) {
            return false;
        }

        return $user->isAdmin() && $user->id === $transaction->user_id;
    }

    /**
     * Deletar depende da permissão do perfil (can_delete_transactions).
     * RF01
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        if (! $user->canDeleteTransactions()) {
            return false;
        }

        return $user->id === $transaction->user_id;
    }

    /**
     * Apenas admin pode marcar como pago.
     */
    public function pay(User $user, Transaction $transaction): bool
    {
        if ($transaction->isPaid()) {
            return false;
        }

        return $user->isAdmin() && $user->id === $transaction->user_id;
    }
}
