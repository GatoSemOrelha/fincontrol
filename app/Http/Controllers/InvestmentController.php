<?php

namespace App\Http\Controllers;

use App\Enums\InvestmentType;
use App\Models\Investment;
use Illuminate\Http\Request;

/**
 * Controller: InvestmentController
 */
class InvestmentController extends Controller
{
    public function index(Request $request)
    {
        $investments = Investment::where('user_id', $request->user()->id)
            ->orderBy('start_date', 'desc')
            ->get();

        $types = InvestmentType::cases();

        return view('investments.index', compact('investments', 'types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:CDB,LCI,LCA,TESOURO_DIRETO,ACAO,FUNDO_IMOBILIARIO,OUTRO',
            'initial_amount' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'interest_rate' => 'required|numeric|min:0',
        ]);

        $data['user_id'] = $request->user()->id;
        $data['current_amount'] = $data['initial_amount'];

        Investment::create($data);

        return redirect()->route('investments.index')
            ->with('success', 'Investimento cadastrado com sucesso!');
    }

    public function update(Request $request, Investment $investment)
    {
        if ($investment->user_id !== $request->user()->id) {
            abort(403, 'Acesso negado.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'current_amount' => 'required|numeric|min:0',
            'end_date' => 'nullable|date',
            'interest_rate' => 'required|numeric|min:0',
        ]);

        $investment->update($data);

        return redirect()->route('investments.index')
            ->with('success', 'Investimento atualizado com sucesso!');
    }
}
