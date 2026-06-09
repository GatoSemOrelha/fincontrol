<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder: Clientes conforme o protótipo.
 */
class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'joao@empresa.com.br')->first();

        $clients = [
            'Empresa Alpha Ltda',
            'Beta Soluções S/A',
            'Gama Indústria',
            'Delta Tech',
        ];

        foreach ($clients as $name) {
            Client::create([
                'name' => $name,
                'user_id' => $admin->id,
            ]);
        }
    }
}
