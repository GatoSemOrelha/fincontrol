<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Client;
use App\Models\CreditCard;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Controller: TransactionController
 *
 * CRUD completo de lançamentos financeiros.
 */
class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
    ) {}

    /**
     * Lista lançamentos com filtros.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $filters = $request->only(['status', 'transaction_type', 'bank_account_id', 'category_id', 'client_id', 'date_from', 'date_to']);

        $transactions = $this->transactionService->list($user->id, $filters);
        $bankAccounts = BankAccount::where('user_id', $user->id)->get();
        $categories = Cache::remember('categories.all', 86400, function () {
            return Category::all();
        });
        $clients = Client::where('user_id', $user->id)->get();
        $creditCards = CreditCard::where('user_id', $user->id)->get();

        return view('transactions.index', compact(
            'transactions', 'bankAccounts', 'categories', 'clients', 'creditCards', 'filters'
        ));
    }

    /**
     * Armazena um novo lançamento.
     */
    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $invoiceFile = $request->file('invoice_document');

        $transaction = $this->transactionService->create($data, $invoiceFile);

        return redirect()->route('transactions.index')
            ->with('success', 'Lançamento criado com sucesso!');
    }

    /**
     * Atualiza um lançamento existente.
     */
    public function update(StoreTransactionRequest $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        try {
            $data = $request->validated();
            $invoiceFile = $request->file('invoice_document');

            $this->transactionService->update($transaction, $data, $invoiceFile);

            return redirect()->route('transactions.index')
                ->with('success', 'Lançamento atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Marca um lançamento como pago.
     */
    public function pay(Transaction $transaction)
    {
        $this->authorize('pay', $transaction);

        try {
            $this->transactionService->markAsPaid($transaction);

            return redirect()->route('transactions.index')
                ->with('success', 'Lançamento marcado como pago!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove um lançamento.
     */
    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        $this->transactionService->delete($transaction);

        return redirect()->route('transactions.index')
            ->with('success', 'Lançamento excluído com sucesso!');
    }

    /**
     * Verifica o impacto no saldo da conta (AJAX).
     * RF04 — Alerta de saldo negativo.
     */
    public function checkImpact(Request $request)
    {
        $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_type' => 'required|in:INCOME,EXPENSE',
        ]);

        $impact = $this->transactionService->checkBalanceImpact(
            $request->bank_account_id,
            $request->amount,
            $request->transaction_type
        );

        return response()->json($impact);
    }
}
