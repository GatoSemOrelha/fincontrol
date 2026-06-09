<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder: Contas bancárias iniciais conforme o protótipo.
 */
class BankAccountSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'joao@empresa.com.br')->first();

        BankAccount::create([
            'name' => 'Itaú PJ',
            'initial_balance' => 30000.00,
            'current_balance' => 32500.00,
            'user_id' => $admin->id,
        ]);

        BankAccount::create([
            'name' => 'Nubank Empresa',
            'initial_balance' => 15000.00,
            'current_balance' => 17060.00,
            'user_id' => $admin->id,
        ]);

        BankAccount::create([
            'name' => 'Bradesco PJ',
            'initial_balance' => 5000.00,
            'current_balance' => -1240.00,
            'user_id' => $admin->id,
        ]);
    }
}
