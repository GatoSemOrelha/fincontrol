<?php

namespace App\Jobs;

use App\Models\CreditCard;
use App\Services\CreditCardService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job: PayCreditCardInvoice
 *
 * RF08 — Baixar parcelas automaticamente ao pagar a fatura do cartão.
 * Executado via queue para não bloquear a UI.
 */
class PayCreditCardInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $creditCardId,
        private int $bankAccountId,
    ) {}

    public function handle(CreditCardService $creditCardService): void
    {
        $card = CreditCard::findOrFail($this->creditCardId);

        Log::info("PayCreditCardInvoice: Pagando fatura do cartão {$card->name}");

        $creditCardService->payInvoice($card, $this->bankAccountId);

        Log::info("PayCreditCardInvoice: Fatura do cartão {$card->name} paga com sucesso");
    }
}
