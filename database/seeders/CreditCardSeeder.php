<?php

namespace Database\Seeders;

use App\Models\CreditCard;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder: Cartões de crédito de demonstração (RF19).
 */
class CreditCardSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'joao@empresa.com.br')->first();

        if (! $admin) {
            return;
        }

        $cards = [
            [
                'name' => 'Visa Empresarial',
                'last_four_digits' => '4532',
                'closing_day' => 5,
                'due_day' => 10,
            ],
            [
                'name' => 'Nubank Corporate',
                'last_four_digits' => '8891',
                'closing_day' => 15,
                'due_day' => 22,
            ],
            [
                'name' => 'Mastercard Bradesco',
                'last_four_digits' => '7720',
                'closing_day' => 28,
                'due_day' => 5,
            ],
        ];

        foreach ($cards as $card) {
            CreditCard::firstOrCreate(
                [
                    'user_id' => $admin->id,
                    'last_four_digits' => $card['last_four_digits'],
                ],
                array_merge($card, ['user_id' => $admin->id])
            );
        }
    }
}
