<?php

namespace App\Policies;

use App\Models\MonthlyReport;
use App\Models\User;

/**
 * Policy: ReportPolicy
 *
 * Ambos os perfis podem visualizar relatórios.
 * Somente admin pode fechar/exportar.
 */
class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MonthlyReport $report): bool
    {
        return $user->id === $report->user_id;
    }

    public function export(User $user): bool
    {
        return $user->isAdmin();
    }

    public function close(User $user, MonthlyReport $report): bool
    {
        return $user->isAdmin() && $user->id === $report->user_id;
    }
}
