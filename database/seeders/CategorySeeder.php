<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * Seeder: Categorias financeiras conforme o protótipo.
 */
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Serviços', 'type' => 'INCOME'],
            ['name' => 'Consultoria', 'type' => 'INCOME'],
            ['name' => 'Produtos', 'type' => 'INCOME'],
            ['name' => 'Despesas fixas', 'type' => 'EXPENSE'],
            ['name' => 'Compras', 'type' => 'EXPENSE'],
            ['name' => 'Impostos', 'type' => 'EXPENSE'],
            ['name' => 'Marketing', 'type' => 'EXPENSE'],
            ['name' => 'Salários', 'type' => 'EXPENSE'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
