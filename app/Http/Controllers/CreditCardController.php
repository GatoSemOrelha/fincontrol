<?php

namespace App\Http\Controllers;

use App\Models\CreditCard;
use App\Services\CreditCardService;
use Illuminate\Http\Request;

/**
 * Controller: CreditCardController
 * RF06, RF08
 */
class CreditCardController extends Controller
{
    public function __construct(
        private CreditCardService $creditCardService,
    ) {}

    public function index(Request $request)
    {
        $cards = $this->creditCardService->listWithTotals($request->user()->id);

        return view('credit-cards.index', compact('cards'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'last_four_digits' => 'required|string|size:4',
            'closing_day' => 'required|integer|between:1,31',
            'due_day' => 'required|integer|between:1,31',
        ]);

        $data['user_id'] = $request->user()->id;
        $this->creditCardService->create($data);

        return redirect()->route('credit-cards.index')
            ->with('success', 'Cartão de crédito cadastrado com sucesso!');
    }

    public function update(Request $request, CreditCard $creditCard)
    {
        if ($creditCard->user_id !== $request->user()->id) {
            abort(403, 'Acesso negado.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'last_four_digits' => 'required|string|size:4',
            'closing_day' => 'required|integer|between:1,31',
            'due_day' => 'required|integer|between:1,31',
        ]);

        $this->creditCardService->update($creditCard, $data);

        return redirect()->route('credit-cards.index')
            ->with('success', 'Cartão atualizado com sucesso!');
    }

    /**
     * Paga a fatura do cartão (RF08).
     */
    public function payInvoice(Request $request, CreditCard $creditCard)
    {
        if ($creditCard->user_id !== $request->user()->id) {
            abort(403, 'Acesso negado.');
        }

        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
        ]);

        $this->creditCardService->payInvoice($creditCard, $request->bank_account_id);

        return redirect()->route('credit-cards.index')
            ->with('success', 'Fatura paga! Parcelas baixadas automaticamente.');
    }
}
