<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request: StoreTransactionRequest
 *
 * Validação server-side para criação/atualização de lançamentos.
 * RF20 / RF21 — Suporte a compras no cartão com parcelas.
 */
class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hasCreditCard = $this->filled('credit_card_id') && $this->input('transaction_type') === 'EXPENSE';

        return [
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => [
                Rule::requiredIf(! $hasCreditCard),
                'nullable',
                'date',
            ],
            'purchase_date' => [
                Rule::requiredIf($hasCreditCard),
                'nullable',
                'date',
            ],
            'installments' => [
                Rule::requiredIf($hasCreditCard),
                'nullable',
                'integer',
                'min:1',
                'max:48',
            ],
            'transaction_type' => 'required|in:INCOME,EXPENSE',
            'status' => 'nullable|in:PENDING,PAID',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'credit_card_id' => 'nullable|exists:credit_cards,id',
            'client_id' => 'nullable|exists:clients,id',
            'category_id' => 'nullable|exists:categories,id',
            'invoice_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'is_recurring' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'A descrição é obrigatória.',
            'amount.required' => 'O valor é obrigatório.',
            'amount.min' => 'O valor deve ser maior que zero.',
            'due_date.required' => 'A data de vencimento é obrigatória.',
            'purchase_date.required' => 'A data da compra é obrigatória para lançamentos no cartão.',
            'installments.required' => 'Informe o número de parcelas.',
            'installments.min' => 'Informe pelo menos 1 parcela.',
            'transaction_type.required' => 'O tipo de lançamento é obrigatório.',
            'bank_account_id.required' => 'A conta bancária é obrigatória.',
            'invoice_document.max' => 'O arquivo da nota fiscal deve ter no máximo 5MB.',
        ];
    }
}
