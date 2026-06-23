<?php

namespace App\Console\Commands;

use App\Models\CreditCard;
use App\Models\Transaction;
use App\Models\User;
use App\Services\CreditCardDemoDataService;
use App\Services\CreditCardService;
use Illuminate\Console\Command;

/**
 * Gera compras fictícias no cartão para validar RF19–RF21.
 *
 * Uso: php artisan demo:credit-card-purchases
 */
class SeedDemoCreditCardPurchases extends Command
{
    protected $signature = 'demo:credit-card-purchases';

    protected $description = 'Cria compras parceladas fictícias nos cartões de crédito para testes';

    public function handle(
        CreditCardService $service,
        CreditCardDemoDataService $demoData,
    ): int {
        $this->info('Gerando cartões e compras de demonstração...');

        $created = $demoData->seed();

        $admin = User::where('email', 'joao@empresa.com.br')->first();
        $cards = CreditCard::where('user_id', $admin->id)->get();
        $demoCount = Transaction::where('user_id', $admin->id)
            ->where('description', 'like', '%'.CreditCardDemoDataService::DEMO_TAG.'%')
            ->count();

        $this->newLine();
        $this->info("Parcelas criadas nesta execução: {$created}");
        $this->info("Total de lançamentos demo no cartão: {$demoCount}");
        $this->newLine();

        $month = (int) now()->month;
        $year = (int) now()->year;

        foreach ($cards as $card) {
            $summary = $service->getInvoiceSummary($card, $month, $year);
            $nextMonth = $month === 12 ? 1 : $month + 1;
            $nextYear = $month === 12 ? $year + 1 : $year;
            $next = $service->getInvoiceSummary($card, $nextMonth, $nextYear);

            $this->line("<fg=cyan>{$card->name}</> •••• {$card->last_four_digits}");
            $this->line("  Fatura {$month}/{$year}: ".money($summary['total'])." ({$summary['count']} lanç.)");
            $this->line("  Período: {$summary['period_start']->format('d/m/Y')} → {$summary['period_end']->format('d/m/Y')}");
            $this->line("  Fatura {$nextMonth}/{$nextYear}: ".money($next['total'])." ({$next['count']} lanç.)");

            $future = Transaction::where('credit_card_id', $card->id)
                ->where('status', 'PENDING')
                ->where('due_date', '>', now()->toDateString())
                ->orderBy('due_date')
                ->get(['description', 'due_date', 'amount']);

            if ($future->isNotEmpty()) {
                $this->line('  Próximas parcelas:');
                foreach ($future->take(4) as $tx) {
                    $this->line("    · {$tx->due_date->format('d/m/Y')} — {$tx->description} — ".money($tx->amount));
                }
                if ($future->count() > 4) {
                    $this->line('    · ... +'.($future->count() - 4).' parcelas');
                }
            }
            $this->newLine();
        }

        $this->info('Pronto! Veja em /credit-cards e /transactions (login: joao@empresa.com.br)');

        return self::SUCCESS;
    }
}
