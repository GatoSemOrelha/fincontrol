@extends('layouts.app')
@section('title', 'Categorias')

@section('content')
<div class="topbar">
    <span class="topbar-title">Categorias financeiras</span>
    @if(auth()->user()->isAdmin())
        <button class="btn btn-primary" onclick="openModal('modal-cat')"><i class="ti ti-plus"></i>Nova categoria</button>
    @endif
</div>

<div class="content">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Nome</th><th>Tipo</th><th>Lançamentos</th><th>Total no mês</th><th>Ações</th></tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                    <tr>
                        <td>{{ $cat->name }}</td>
                        <td><span class="tag {{ $cat->type === 'INCOME' ? 'tag-in' : 'tag-out' }}">{{ $cat->type === 'INCOME' ? 'Entrada' : 'Saída' }}</span></td>
                        <td>{{ $cat->transactions_count }}</td>
                        <td style="color:{{ $cat->type === 'INCOME' ? 'var(--color-text-success)' : 'var(--color-text-danger)' }}">
                            R$ {{ number_format($cat->monthly_total, 2, ',', '.') }}
                        </td>
                        <td>
                            @if(auth()->user()->isAdmin())
                                <i class="ti ti-edit action-icon" title="Editar"></i>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal-overlay" id="modal-cat">
    <div class="modal">
        <div class="modal-header">
            <h3>Nova categoria</h3>
            <i class="ti ti-x" style="cursor:pointer;font-size:18px;color:var(--color-text-secondary)" onclick="closeModal('modal-cat')"></i>
        </div>
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nome</label>
                <input type="text" name="name" placeholder="Ex: Marketing" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tipo</label>
                <select name="type" required>
                    <option value="INCOME">Entrada</option>
                    <option value="EXPENSE">Saída</option>
                </select>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:16px">
                <button type="button" class="btn" onclick="closeModal('modal-cat')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection
