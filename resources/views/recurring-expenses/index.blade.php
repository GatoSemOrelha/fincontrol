@extends('layouts.app')
@section('title', 'Despesas recorrentes')

@section('content')
<div class="topbar">
    <span class="topbar-title">Despesas fixas recorrentes (RF09)</span>
    @if(auth()->user()->isAdmin())
        <button class="btn btn-primary" onclick="openModal('modal-recurring')"><i class="ti ti-plus"></i>Nova despesa fixa</button>
    @endif
</div>

<div class="content">
    <div class="alert alert-info">
        <i class="ti ti-info-circle"></i>
        Despesas fixas são recriadas automaticamente no dia 1 de cada mês como lançamentos pendentes.
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Descrição</th><th>Valor</th><th>Dia do mês</th><th>Conta</th><th>Categoria</th><th>Status</th><th>Ações</th></tr>
            </thead>
            <tbody>
                @forelse($expenses as $exp)
                    <tr>
                        <td style="font-weight:500">{{ $exp->description }}</td>
                        <td style="color:var(--color-text-danger);font-weight:500">R$ {{ number_format($exp->amount, 2, ',', '.') }}</td>
                        <td>Dia {{ $exp->day_of_month }}</td>
                        <td>{{ $exp->bankAccount->name }}</td>
                        <td>{{ $exp->category->name ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $exp->is_active ? 'badge-success' : 'badge-warning' }}">
                                {{ $exp->is_active ? 'Ativa' : 'Inativa' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-cell">
                                @if(auth()->user()->isAdmin())
                                    <form method="POST" action="{{ route('recurring-expenses.destroy', $exp) }}" style="display:inline"
                                          onsubmit="return confirm('Excluir esta despesa recorrente?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" style="background:none;border:none;cursor:pointer;padding:0">
                                            <i class="ti ti-trash action-icon" style="color:var(--color-text-danger)" title="Excluir"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="color:var(--color-text-tertiary);text-align:center">Nenhuma despesa recorrente cadastrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal: Nova despesa recorrente --}}
<div class="modal-overlay" id="modal-recurring">
    <div class="modal">
        <div class="modal-header">
            <h3>Nova despesa fixa recorrente</h3>
            <i class="ti ti-x" style="cursor:pointer;font-size:18px;color:var(--color-text-secondary)" onclick="closeModal('modal-recurring')"></i>
        </div>
        <form method="POST" action="{{ route('recurring-expenses.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Descrição</label>
                <input type="text" name="description" placeholder="Ex: Aluguel sala comercial" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Valor (R$)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" placeholder="0,00" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Dia do mês</label>
                    <input type="number" name="day_of_month" min="1" max="31" value="1" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Conta bancária</label>
                    <select name="bank_account_id" required>
                        @foreach($bankAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Categoria</label>
                    <select name="category_id">
                        <option value="">— Selecione —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:16px">
                <button type="button" class="btn" onclick="closeModal('modal-recurring')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection
