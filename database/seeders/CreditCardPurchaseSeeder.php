<?php

namespace Database\Seeders;

use App\Services\CreditCardDemoDataService;
use Illuminate\Database\Seeder;

/**
 * Seeder: Compras fictícias no cartão com parcelas futuras (RF20 / RF21).
 */
class CreditCardPurchaseSeeder extends Seeder
{
    public function run(): void
    {
        app(CreditCardDemoDataService::class)->seed();
    }
}
