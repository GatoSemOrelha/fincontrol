@extends('layouts.app')
@section('title', 'Categorias')

@section('content')
<div class="topbar">
    <span class="topbar-title">{{ __('Categorias financeiras') }}</span>
    @if(auth()->user()->isAdmin())
        <button class="btn btn-primary" onclick="openModal('modal-cat')"><i class="ti ti-plus"></i>{{ __('Nova categoria') }}</button>
    @endif
</div>

<div class="content">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>{{ __('Nome') }}</th><th>{{ __('Tipo') }}</th><th>{{ __('Lançamentos') }}</th><th>{{ __('Total no mês') }}</th><th>{{ __('Ações') }}</th></tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                    <tr>
                        <td>{{ $cat->name }}</td>
                        <td><span class="tag {{ $cat->type === 'INCOME' ? 'tag-in' : 'tag-out' }}">{{ $cat->type === 'INCOME' ? __('Entrada') : __('Saída') }}</span></td>
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
            <h3>{{ __('Nova categoria') }}</h3>
            <i class="ti ti-x" style="cursor:pointer;font-size:18px;color:var(--color-text-secondary)" onclick="closeModal('modal-cat')"></i>
        </div>
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">{{ __('Nome') }}</label>
                <input type="text" name="name" placeholder="{{ __('Ex: Marketing') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">{{ __('Tipo') }}</label>
                <select name="type" required>
                    <option value="INCOME">{{ __('Entrada') }}</option>
                    <option value="EXPENSE">{{ __('Saída') }}</option>
                </select>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:16px">
                <button type="button" class="btn" onclick="closeModal('modal-cat')">{{ __('Cancelar') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('Salvar') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
