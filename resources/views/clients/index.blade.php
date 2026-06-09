@extends('layouts.app')
@section('title', 'Clientes')

@section('content')
<div class="topbar">
    <span class="topbar-title">Clientes</span>
    @if(auth()->user()->isAdmin())
        <button class="btn btn-primary" onclick="openModal('modal-client')"><i class="ti ti-plus"></i>Novo cliente</button>
    @endif
</div>

<div class="content">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Nome</th><th>Lançamentos vinculados</th><th>Ações</th></tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr>
                        <td style="font-weight:500">{{ $client->name }}</td>
                        <td>{{ $client->transactions_count }}</td>
                        <td>
                            <div class="action-cell">
                                @if(auth()->user()->isAdmin())
                                    <i class="ti ti-edit action-icon" title="Editar"></i>
                                    @if($client->transactions_count === 0)
                                        <form method="POST" action="{{ route('clients.destroy', $client) }}" style="display:inline"
                                              onsubmit="return confirm('Excluir este cliente?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" style="background:none;border:none;cursor:pointer;padding:0">
                                                <i class="ti ti-trash action-icon" style="color:var(--color-text-danger)" title="Excluir"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="color:var(--color-text-tertiary);text-align:center">Nenhum cliente cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal: Novo cliente --}}
<div class="modal-overlay" id="modal-client">
    <div class="modal">
        <div class="modal-header">
            <h3>Novo cliente</h3>
            <i class="ti ti-x" style="cursor:pointer;font-size:18px;color:var(--color-text-secondary)" onclick="closeModal('modal-client')"></i>
        </div>
        <form method="POST" action="{{ route('clients.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nome do cliente</label>
                <input type="text" name="name" placeholder="Ex: Empresa Alpha Ltda" required>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:16px">
                <button type="button" class="btn" onclick="closeModal('modal-client')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>
@endsection
