<?php

namespace App\Jobs;

use App\Models\MonthlyReport;
use App\Services\MonthlyReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job: GenerateMonthlyReportPdf
 *
 * RF12 — Gerar o PDF do relatório mensal de forma assíncrona.
 */
class GenerateMonthlyReportPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $reportId,
    ) {}

    public function handle(MonthlyReportService $reportService): void
    {
        $report = MonthlyReport::findOrFail($this->reportId);

        Log::info("GenerateMonthlyReportPdf: Gerando PDF do relatório {$report->periodLabel()}");

        $path = $reportService->generatePdf($report);

        Log::info("GenerateMonthlyReportPdf: PDF gerado em {$path}");
    }
}
