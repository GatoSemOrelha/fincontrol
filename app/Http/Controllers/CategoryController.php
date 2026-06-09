<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Controller: CategoryController
 */
class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $categories = Category::withCount(['transactions' => function ($q) use ($user, $year, $month) {
            $q->where('user_id', $user->id)
                ->whereYear('due_date', $year)
                ->whereMonth('due_date', $month);
        }])
            ->withSum(['transactions as monthly_total' => function ($q) use ($user, $year, $month) {
                $q->where('user_id', $user->id)
                    ->whereYear('due_date', $year)
                    ->whereMonth('due_date', $month);
            }], 'amount')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:INCOME,EXPENSE',
        ]);

        Category::create($data);

        Cache::forget('categories.all');
        Cache::forget('categories.expense');

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:INCOME,EXPENSE',
        ]);

        $category->update($data);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }
}
