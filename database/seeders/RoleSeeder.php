<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

/**
 * Seeder: Perfis de acesso iniciais.
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'name' => 'Administrador',
            'description' => 'Acesso total ao sistema',
            'can_delete_transactions' => true,
        ]);

        Role::create([
            'name' => 'Visualizador',
            'description' => 'Pode visualizar dados e relatórios, mas não pode deletar lançamentos',
            'can_delete_transactions' => false,
        ]);
    }
}
