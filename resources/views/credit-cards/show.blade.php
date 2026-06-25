@extends('layouts.app')
@section('title', 'Lançamentos do Cartão: ' . $creditCard->name)

@section('content')
<div class="topbar">
    <div style="display:flex;align-items:center;gap:16px">
        <a href="{{ route('credit-cards.index') }}" style="color:var(--color-text-secondary);text-decoration:none">
            <i class="ti ti-arrow-left" style="font-size:24px"></i>
        </a>
        <span class="topbar-title">{{ __('Cartão:') }} {{ $creditCard->displayName() }}</span>
    </div>
    <div class="topbar-actions" style="display:flex;gap:12px;align-items:center">
        <form method="GET" action="{{ route('credit-cards.show', $creditCard) }}" style="display:flex;gap:12px;align-items:center" id="filter-form">
            <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--color-text-secondary);cursor:pointer;">
                <input type="checkbox" name="future_only" value="1" {{ $futureOnly ? 'checked' : '' }} onchange="document.getElementById('filter-form').submit()">
                {{ __('Apenas parcelas futuras') }}
            </label>
            @if(!$futureOnly)
                <select name="month" style="width:auto;font-size:12px;padding:5px 8px" onchange="document.getElementById('filter-form').submit()">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
                <select name="year" style="width:auto;font-size:12px;padding:5px 8px" onchange="document.getElementById('filter-form').submit()">
                    @for($y = now()->year - 2; $y <= now()->year + 2; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            @endif
        </form>
    </div>
</div>

<div class="content">
    <div class="card" style="margin-bottom:20px;display:flex;gap:20px;align-items:center">
        <div style="flex:1">
            <div style="font-size:12px;color:var(--color-text-secondary)">{{ __('Limite Total') }}</div>
            <div style="font-size:18px;font-weight:500;color:var(--color-text-primary)">
                {{ money($creditCard->credit_limit) }}
            </div>
        </div>
        <div style="width:1px;height:40px;background:var(--color-border)"></div>
        <div style="flex:1">
            <div style="font-size:12px;color:var(--color-text-secondary)">{{ __('Limite Disponível') }}</div>
            <div style="font-size:18px;font-weight:500;color:var(--color-text-success)">
                {{ money($creditCard->getAvailableLimit()) }}
            </div>
        </div>
        <div style="width:1px;height:40px;background:var(--color-border)"></div>
        <div style="flex:1">
            <div style="font-size:12px;color:var(--color-text-secondary)">{{ __('Faturas Pendentes') }}</div>
            <div style="font-size:18px;font-weight:500;color:var(--color-text-warning)">
                {{ money($creditCard->getOpenInvoiceTotal()) }}
            </div>
        </div>
    </div>

    @if($transactions->isEmpty())
        <div style="text-align:center;padding:40px 20px;">
            <i class="ti ti-receipt-off" style="font-size:32px;color:var(--color-text-tertiary);margin-bottom:16px;display:block"></i>
            <h3 style="margin-bottom:8px">{{ __('Nenhum lançamento') }}</h3>
            <p style="color:var(--color-text-secondary);font-size:14px">
                {{ __('Ainda não há compras registradas para este cartão.') }}
            </p>
        </div>
    @else
        <div class="table-wrap">
            <table class="android-list-table">
                <thead>
                    <tr>
                        <th>{{ __('Vencimento') }}</th>
                        <th>{{ __('Descrição') }}</th>
                        <th>{{ __('Categoria') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th style="text-align:right">{{ __('Valor') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                        <tr>
                            <td data-label="{{ __('Vencimento') }}" style="font-size:12px;white-space:nowrap">{{ $tx->due_date->format('d/m/Y') }}</td>
                            <td data-label="{{ __('Descrição') }}">{{ $tx->description }}</td>
                            <td data-label="{{ __('Categoria') }}" style="font-size:12px;color:var(--color-text-secondary)">{{ $tx->category->name ?? '—' }}</td>
                            <td data-label="{{ __('Status') }}">
                                @if($tx->status->value === 'PAID')
                                    <span style="color:var(--color-text-success);font-size:12px;font-weight:500"><i class="ti ti-check"></i> {{ __('Pago') }}</span>
                                @else
                                    <span style="color:var(--color-text-warning);font-size:12px;font-weight:500"><i class="ti ti-clock"></i> {{ __('Pendente') }}</span>
                                @endif
                            </td>
                            <td data-label="{{ __('Valor') }}" style="text-align:right;font-weight:500;color:var(--color-text-danger)">{{ money($tx->amount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:20px">
            {{ $transactions->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
