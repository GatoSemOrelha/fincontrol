@extends('layouts.app')
@section('title', 'Contas bancárias')

@section('content')
<div class="topbar">
    <span class="topbar-title">Contas bancárias</span>
    @if(auth()->user()->isAdmin())
        <button class="btn btn-primary" onclick="openModal('modal-conta')"><i class="ti ti-plus"></i>Nova conta</button>
    @endif
</div>

<div class="content">
    <div class="grid-3">
        @foreach($accounts as $account)
            <div class="card" style="{{ $account->isNegative() ? 'border-color:var(--color-border-danger)' : '' }}">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                    <div style="width:38px;height:38px;border-radius:var(--border-radius-md);background:{{ $account->isNegative() ? 'var(--color-background-danger)' : 'var(--color-background-info)' }};display:flex;align-items:center;justify-content:center">
                        <i class="ti ti-building-bank" style="color:{{ $account->isNegative() ? 'var(--color-text-danger)' : 'var(--color-text-info)' }};font-size:18px"></i>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:500">{{ $account->name }}</div>
                    </div>
                </div>
                @if($account->isNegative())
                    <div class="alert alert-danger" style="padding:6px 10px;margin-bottom:8px">
                        <i class="ti ti-alert-triangle"></i>Saldo negativo
                    </div>
                @endif
                <div class="divider"></div>
                <div style="font-size:22px;font-weight:500;color:{{ $account->isNegative() ? 'var(--color-text-danger)' : 'var(--color-text-success)' }}">
                    R$ {{ number_format($account->current_balance, 2, ',', '.') }}
                </div>
                <div style="font-size:12px;color:var(--color-text-tertiary)">saldo atual</div>
                <div style="margin-top:12px;display:flex;gap:8px">
                    <a href="{{ route('transactions.index', ['bank_account_id' => $account->id]) }}" class="btn" style="font-size:12px;flex:1;justify-content:center">
                        <i class="ti ti-list"></i>Extrato
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Modal: Nova conta --}}
<div class="modal-overlay" id="modal-conta">
    <div class="modal">
        <div class="modal-header">
            <h3>Nova conta bancária</h3>
            <i class="ti ti-x" style="cursor:pointer;font-size:18px;color:var(--color-text-secondary)" onclick="closeModal('modal-conta')"></i>
        </div>
        <form method="POST" action="{{ route('bank-accounts.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nome da conta</label>
                <input type="text" name="name" placeholder="Ex: Safra Empresa" required>
            </div>
            <div class="form-group">
                <label class="form-label">Saldo inicial (R$)</label>
                <input type="number" name="initial_balance" step="0.01" placeholder="0,00" required>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:16px">
                <button type="button" class="btn" onclick="closeModal('modal-conta')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection
