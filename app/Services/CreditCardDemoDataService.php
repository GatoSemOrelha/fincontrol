<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\Category;
use App\Models\CreditCard;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

/**
 * Dados de demonstração: compras parceladas no cartão (RF20 / RF21).
 */
class CreditCardDemoDataService
{
    public const DEMO_TAG = '[DEMO CC]';

    public function __construct(
        private CreditCardService $creditCardService,
    ) {}

    public function seed(): int
    {
        $admin = User::where('email', 'joao@empresa.com.br')->first();

        if (! $admin) {
            return 0;
        }

        $this->ensureCards($admin->id);

        $account = BankAccount::where('user_id', $admin->id)->where('name', 'Itaú PJ')->first()
            ?? BankAccount::where('user_id', $admin->id)->first();
        $category = Category::where('name', 'Compras')->first();

        $visa = CreditCard::where('user_id', $admin->id)->where('last_four_digits', '4532')->first();
        $nubank = CreditCard::where('user_id', $admin->id)->where('last_four_digits', '8891')->first();
        $master = CreditCard::where('user_id', $admin->id)->where('last_four_digits', '7720')->first();

        if (! $account || ! $visa || ! $nubank || ! $master) {
            return 0;
        }

        Transaction::where('user_id', $admin->id)
            ->where('description', 'like', '%'.self::DEMO_TAG.'%')
            ->delete();

        $now = Carbon::now();
        $base = [
            'user_id' => $admin->id,
            'bank_account_id' => $account->id,
            'category_id' => $category?->id,
        ];

        $purchases = [
            ['credit_card_id' => $visa->id, 'description' => 'Notebook Dell Latitude '.self::DEMO_TAG, 'amount' => 2500.00, 'installments' => 4, 'purchase_date' => $now->copy()->day(min(3, $now->daysInMonth))->toDateString()],
            ['credit_card_id' => $visa->id, 'description' => 'Mobiliário escritório '.self::DEMO_TAG, 'amount' => 1100.00, 'installments' => 6, 'purchase_date' => $now->copy()->day(min(12, $now->daysInMonth))->toDateString()],
            ['credit_card_id' => $visa->id, 'description' => 'Licença Adobe anual '.self::DEMO_TAG, 'amount' => 299.90, 'installments' => 1, 'purchase_date' => $now->copy()->day(min(2, $now->daysInMonth))->toDateString()],
            ['credit_card_id' => $nubank->id, 'description' => 'Software SaaS corporativo '.self::DEMO_TAG, 'amount' => 1078.80, 'installments' => 12, 'purchase_date' => $now->copy()->day(min(8, $now->daysInMonth))->toDateString()],
            ['credit_card_id' => $nubank->id, 'description' => 'Evento corporativo '.self::DEMO_TAG, 'amount' => 1350.00, 'installments' => 3, 'purchase_date' => $now->copy()->day(min(20, $now->daysInMonth))->toDateString()],
            ['credit_card_id' => $master->id, 'description' => 'Equipamentos de TI '.self::DEMO_TAG, 'amount' => 1600.00, 'installments' => 5, 'purchase_date' => $now->copy()->day(min(20, $now->daysInMonth))->toDateString()],
            ['credit_card_id' => $visa->id, 'description' => 'Consultoria externa '.self::DEMO_TAG, 'amount' => 2400.00, 'installments' => 8, 'purchase_date' => $now->copy()->subMonths(3)->day(4)->toDateString()],
        ];

        $total = 0;
        foreach ($purchases as $purchase) {
            $created = $this->creditCardService->processInstallmentPurchase(array_merge($base, $purchase));
            $total += count($created);
        }

        return $total;
    }

    private function ensureCards(int $userId): void
    {
        $cards = [
            ['name' => 'Visa Empresarial', 'last_four_digits' => '4532', 'closing_day' => 5, 'due_day' => 10],
            ['name' => 'Nubank Corporate', 'last_four_digits' => '8891', 'closing_day' => 15, 'due_day' => 22],
            ['name' => 'Mastercard Bradesco', 'last_four_digits' => '7720', 'closing_day' => 28, 'due_day' => 5],
        ];

        foreach ($cards as $card) {
            CreditCard::firstOrCreate(
                ['user_id' => $userId, 'last_four_digits' => $card['last_four_digits']],
                array_merge($card, ['user_id' => $userId])
            );
        }
    }
}
