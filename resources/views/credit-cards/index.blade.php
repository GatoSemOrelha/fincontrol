@extends('layouts.app')
@section('title', 'Cartões de crédito')

@section('content')
<div class="topbar">
    <span class="topbar-title">{{ __('Cartões de crédito') }}</span>
    @if(auth()->user()->isAdmin())
        <button class="btn btn-primary" onclick="openModal('modal-card')"><i class="ti ti-plus"></i>{{ __('Novo cartão') }}</button>
    @endif
</div>

<div class="content">
    <div class="grid-3">
        @foreach($cards as $card)
            <div class="card">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                    <div style="width:38px;height:38px;border-radius:var(--border-radius-md);background:var(--color-background-warning);display:flex;align-items:center;justify-content:center">
                        <i class="ti ti-credit-card" style="color:var(--color-text-warning);font-size:18px"></i>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:500">{{ $card->name }}</div>
                        <div style="font-size:12px;color:var(--color-text-secondary)">•••• {{ $card->last_four_digits }}</div>
                    </div>
                </div>
                <div class="divider"></div>
                <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--color-text-secondary);margin-bottom:8px">
                    <span>{{ __('Fecha dia') }} {{ $card->closing_day }}</span>
                    <span>{{ __('Vence dia') }} {{ $card->due_day }}</span>
                </div>
                <div style="font-size:11px;color:var(--color-text-tertiary);margin-bottom:4px">{{ __('Fatura aberta (RF06)') }}</div>
                <div style="font-size:20px;font-weight:500;color:{{ $card->open_invoice_total > 0 ? 'var(--color-text-warning)' : 'var(--color-text-success)' }}">
                    R$ {{ number_format($card->open_invoice_total, 2, ',', '.') }}
                </div>
                <div style="font-size:11px;color:var(--color-text-tertiary)">{{ $card->pending_count }} {{ __('lançamentos pendentes') }}</div>

                @if($card->open_invoice_total > 0 && auth()->user()->isAdmin())
                    <div style="margin-top:12px">
                        <button class="btn btn-sm btn-success" onclick="openModal('modal-pay-{{ $card->id }}')" style="width:100%;justify-content:center">
                            <i class="ti ti-check"></i>{{ __('Pagar fatura (RF08)') }}
                        </button>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

{{-- Modal: Novo cartão --}}
<div class="modal-overlay" id="modal-card">
    <div class="modal">
        <div class="modal-header">
            <h3>{{ __('Novo cartão de crédito') }}</h3>
            <i class="ti ti-x" style="cursor:pointer;font-size:18px;color:var(--color-text-secondary)" onclick="closeModal('modal-card')"></i>
        </div>
        <form method="POST" action="{{ route('credit-cards.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">{{ __('Nome do cartão') }}</label>
                <input type="text" name="name" placeholder="{{ __('Ex: Visa Empresarial') }}" required>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">{{ __('4 últimos dígitos') }}</label>
                    <input type="text" name="last_four_digits" maxlength="4" placeholder="1234" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Dia fechamento') }}</label>
                    <input type="number" name="closing_day" min="1" max="31" required>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('Dia vencimento') }}</label>
                    <input type="number" name="due_day" min="1" max="31" required>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:16px">
                <button type="button" class="btn" onclick="closeModal('modal-card')">{{ __('Cancelar') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Salvar') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
