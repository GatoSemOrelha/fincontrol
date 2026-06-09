<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Services\BankAccountService;
use Illuminate\Http\Request;

/**
 * Controller: BankAccountController
 */
class BankAccountController extends Controller
{
    public function __construct(
        private BankAccountService $bankAccountService,
    ) {}

    public function index(Request $request)
    {
        $accounts = BankAccount::where('user_id', $request->user()->id)->get();

        return view('bank-accounts.index', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'initial_balance' => 'required|numeric',
        ], [
            'name.required' => 'O nome da conta é obrigatório.',
        ]);

        $data['user_id'] = $request->user()->id;
        $this->bankAccountService->create($data);

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Conta bancária criada com sucesso!');
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        if ($bankAccount->user_id !== $request->user()->id) {
            abort(403, 'Acesso negado.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $this->bankAccountService->update($bankAccount, $data);

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Conta bancária atualizada com sucesso!');
    }

    public function destroy(Request $request, BankAccount $bankAccount)
    {
        if ($bankAccount->user_id !== $request->user()->id) {
            abort(403, 'Acesso negado.');
        }

        if ($bankAccount->transactions()->exists()) {
            return back()->with('error', 'Não é possível excluir uma conta com lançamentos.');
        }

        $bankAccount->delete();

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Conta bancária excluída com sucesso!');
    }
}
