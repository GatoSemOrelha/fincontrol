<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder principal: executa todos os seeders na ordem correta.
 *
 * Uso: php artisan db:seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            BankAccountSeeder::class,
            CreditCardSeeder::class,
            CategorySeeder::class,
            ClientSeeder::class,
            TransactionSeeder::class,
            CreditCardPurchaseSeeder::class,
        ]);
    }
}
 