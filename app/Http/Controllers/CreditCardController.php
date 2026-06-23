<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayCreditCardInvoiceRequest;
use App\Http\Requests\StoreCreditCardInstallmentRequest;
use App\Http\Requests\StoreCreditCardRequest;
use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Client;
use App\Models\CreditCard;
use App\Services\CreditCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Controller: CreditCardController
 *
 * RF06, RF08, RF19, RF20, RF21
 */
class CreditCardController extends Controller
{
    public function __construct(
        private CreditCardService $creditCardService,
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $cards = $this->creditCardService->listWithTotals($user->id, $month, $year);
        $bankAccounts = BankAccount::where('user_id', $user->id)->get();
        $categories = Cache::remember('categories.all', 86400, fn () => Category::all());
        $clients = Client::where('user_id', $user->id)->get();

        return view('credit-cards.index', compact(
            'cards', 'bankAccounts', 'categories', 'clients', 'year', 'month'
        ));
    }

    public function store(StoreCreditCardRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $this->creditCardService->create($data);

        return redirect()->route('credit-cards.index')
            ->with('success', 'Cartão de crédito cadastrado com sucesso!');
    }

    public function update(StoreCreditCardRequest $request, CreditCard $creditCard)
    {
        $this->ensureOwnsCard($request, $creditCard);

        $this->creditCardService->update($creditCard, $request->validated());

        return redirect()->route('credit-cards.index', [
            'year' => $request->get('year', now()->year),
            'month' => $request->get('month', now()->month),
        ])->with('success', 'Cartão atualizado com sucesso!');
    }

    /**
     * RF20 / RF21 — Compra parcelada no cartão (somente transactions).
     */
    public function storeInstallment(StoreCreditCardInstallmentRequest $request, CreditCard $creditCard)
    {
        $this->ensureOwnsCard($request, $creditCard);

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['credit_card_id'] = $creditCard->id;

        $this->assertBankAccountBelongsToUser($request->user()->id, (int) $data['bank_account_id']);

        $created = $this->creditCardService->processInstallmentPurchase($data);

        $message = count($created) > 1
            ? count($created).' parcelas registradas no cartão com sucesso!'
            : 'Compra registrada no cartão com sucesso!';

        return redirect()->route('credit-cards.index', [
            'year' => $request->get('year', now()->year),
            'month' => $request->get('month', now()->month),
        ])->with('success', $message);
    }

    /**
     * Paga a fatura virtual do período (RF08 + RF19).
     */
    public function payInvoice(PayCreditCardInvoiceRequest $request, CreditCard $creditCard)
    {
        $this->ensureOwnsCard($request, $creditCard);

        $data = $request->validated();
        $this->assertBankAccountBelongsToUser($request->user()->id, (int) $data['bank_account_id']);

        $month = isset($data['month']) ? (int) $data['month'] : (int) now()->month;
        $year = isset($data['year']) ? (int) $data['year'] : (int) now()->year;

        $summary = $this->creditCardService->getInvoiceSummary($creditCard, $month, $year);

        if ($summary['count'] === 0) {
            return back()->with('error', 'Não há lançamentos pendentes na fatura deste período.');
        }

        $this->creditCardService->payInvoice($creditCard, (int) $data['bank_account_id'], $month, $year);

        return redirect()->route('credit-cards.index', compact('year', 'month'))
            ->with('success', 'Fatura paga! Lançamentos do período baixados automaticamente.');
    }

    private function ensureOwnsCard(Request $request, CreditCard $creditCard): void
    {
        if ($creditCard->user_id !== $request->user()->id) {
            abort(403, 'Acesso negado.');
        }
    }

    private function assertBankAccountBelongsToUser(int $userId, int $bankAccountId): void
    {
        $owns = BankAccount::where('id', $bankAccountId)->where('user_id', $userId)->exists();

        if (! $owns) {
            abort(403, 'Conta bancária inválida para este usuário.');
        }
    }
}
