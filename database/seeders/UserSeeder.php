<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder: Usuários iniciais conforme o protótipo.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Administrador')->first();
        $viewerRole = Role::where('name', 'Visualizador')->first();

        User::create([
            'username' => 'João Admin',
            'email' => 'joao@empresa.com.br',
            'password_hash' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        User::create([
            'username' => 'Maria Viewer',
            'email' => 'maria@empresa.com.br',
            'password_hash' => Hash::make('viewer123'),
            'role_id' => $viewerRole->id,
            'is_active' => true,
        ]);

        User::create([
            'username' => 'Carlos Silva',
            'email' => 'carlos@empresa.com.br',
            'password_hash' => Hash::make('viewer123'),
            'role_id' => $viewerRole->id,
            'is_active' => false,
        ]);
    }
}
