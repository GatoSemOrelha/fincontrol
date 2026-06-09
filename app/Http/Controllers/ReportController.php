<?php

namespace App\Http\Controllers;

use App\Services\CashFlowProjectionService;
use App\Services\MonthlyReportService;
use App\Services\ReportService;
use Illuminate\Http\Request;

/**
 * Controller: ReportController
 *
 * RF10, RF11, RF12
 */
class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService,
        private MonthlyReportService $monthlyReportService,
        private CashFlowProjectionService $cashFlowService,
    ) {}

    /**
     * Relatório mensal.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $report = $this->monthlyReportService->generate($year, $month, $user->id);
        $incomeByCategory = $this->reportService->getIncomeByCategory($user->id, $year, $month);
        $expensesByCategory = $this->reportService->getExpensesByCategory($user->id, $year, $month);
        $incomeByClient = $this->reportService->getIncomeByClient($user->id, $year, $month);

        return view('reports.index', compact(
            'report', 'incomeByCategory', 'expensesByCategory', 'incomeByClient', 'year', 'month'
        ));
    }

    /**
     * Exporta o relatório mensal em PDF (RF12).
     */
    public function exportPdf(Request $request)
    {
        $user = $request->user();
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $report = $this->monthlyReportService->generate($year, $month, $user->id);

        return $this->monthlyReportService->downloadPdf($report);
    }

    /**
     * Fecha o relatório mensal (imutável).
     */
    public function close(Request $request)
    {
        $user = $request->user();
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $report = $this->monthlyReportService->generate($year, $month, $user->id);

        try {
            $this->monthlyReportService->close($report, $user->id);

            return redirect()->route('reports.index', compact('year', 'month'))
                ->with('success', 'Relatório fechado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Projeção de fluxo de caixa (RF11).
     */
    public function cashFlow(Request $request)
    {
        $user = $request->user();
        $projection = $this->cashFlowService->project($user->id);

        return view('reports.cash-flow', compact('projection'));
    }
}
