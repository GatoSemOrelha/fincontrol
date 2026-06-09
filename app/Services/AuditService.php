<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Service: AuditService
 *
 * Centraliza o registro de logs de auditoria.
 * Usado tanto pelo trait Auditable (automático) quanto por ações manuais.
 */
class AuditService
{
    /**
     * Registra uma ação de auditoria manualmente.
     */
    public function log(
        string $action,
        string $auditableType,
        int $auditableId,
        ?array $oldValues = null,
        ?array $newValues = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $auditableType,
            'auditable_id' => $auditableId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
        ]);
    }

    /**
     * Retorna os logs de auditoria com filtros opcionais.
     */
    public function getLogs(array $filters = [], int $perPage = 20)
    {
        $query = AuditLog::with('user')
            ->orderBy('created_at', 'desc');

        if (! empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (! empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (! empty($filters['auditable_type'])) {
            $query->where('auditable_type', $filters['auditable_type']);
        }

        if (! empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to'].' 23:59:59');
        }

        return $query->paginate($perPage);
    }
}
