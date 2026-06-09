<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Service: ReportService
 *
 * RF10 — Receitas por cliente e despesas por categoria.
 */
class ReportService
{
    /**
     * Receitas agrupadas por cliente no período.
     * RF10
     */
    public function getIncomeByClient(int $userId, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $results = Transaction::where('user_id', $userId)
            ->where('transaction_type', 'INCOME')
            ->whereBetween('due_date', [$startDate, $endDate])
            ->whereNotNull('client_id')
            ->select('client_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('client_id')
            ->with('client')
            ->orderByDesc('total')
            ->get();

        $grandTotal = $results->sum('total');

        return $results->map(function ($row) use ($grandTotal) {
            return [
                'client_name' => $row->client->name ?? 'Sem cliente',
                'total' => (float) $row->total,
                'count' => $row->count,
                'percentage' => $grandTotal > 0 ? round(($row->total / $grandTotal) * 100) : 0,
            ];
        })->toArray();
    }

    /**
     * Despesas agrupadas por categoria no período.
     * RF10
     */
    public function getExpensesByCategory(int $userId, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $results = Transaction::where('user_id', $userId)
            ->where('transaction_type', 'EXPENSE')
            ->whereBetween('due_date', [$startDate, $endDate])
            ->select('category_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('category_id')
            ->with('category')
            ->orderByDesc('total')
            ->get();

        $grandTotal = $results->sum('total');

        return $results->map(function ($row) use ($grandTotal) {
            return [
                'category_name' => $row->category->name ?? 'Sem categoria',
                'total' => (float) $row->total,
                'count' => $row->count,
                'percentage' => $grandTotal > 0 ? round(($row->total / $grandTotal) * 100) : 0,
            ];
        })->toArray();
    }

    /**
     * Receitas agrupadas por categoria no período (para gráficos).
     */
    public function getIncomeByCategory(int $userId, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        $results = Transaction::where('user_id', $userId)
            ->where('transaction_type', 'INCOME')
            ->whereBetween('due_date', [$startDate, $endDate])
            ->select('category_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('category_id')
            ->with('category')
            ->orderByDesc('total')
            ->get();

        $grandTotal = $results->sum('total');

        return $results->map(function ($row) use ($grandTotal) {
            return [
                'category_name' => $row->category->name ?? 'Sem categoria',
                'total' => (float) $row->total,
                'count' => $row->count,
                'percentage' => $grandTotal > 0 ? round(($row->total / $grandTotal) * 100) : 0,
            ];
        })->toArray();
    }
}
