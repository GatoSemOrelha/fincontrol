@extends('layouts.app')
@section('title', 'Auditoria')

@section('content')
<div class="topbar"><span class="topbar-title">{{ __('Auditoria — histórico de alterações') }}</span></div>

<div class="content">
    <div class="filter-row" style="margin-bottom:14px">
        <form method="GET" action="{{ route('audit.index') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" style="width:auto;font-size:12px;padding:5px 8px" placeholder="{{ __('De') }}">
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" style="width:auto;font-size:12px;padding:5px 8px" placeholder="{{ __('Até') }}">
            <select name="action" style="width:auto;font-size:12px;padding:5px 8px">
                <option value="">{{ __('Todas as ações') }}</option>
                <option value="created" {{ ($filters['action'] ?? '') === 'created' ? 'selected' : '' }}>{{ __('Criado') }}</option>
                <option value="updated" {{ ($filters['action'] ?? '') === 'updated' ? 'selected' : '' }}>{{ __('Editado') }}</option>
                <option value="deleted" {{ ($filters['action'] ?? '') === 'deleted' ? 'selected' : '' }}>{{ __('Excluído') }}</option>
                <option value="paid" {{ ($filters['action'] ?? '') === 'paid' ? 'selected' : '' }}>{{ __('Pago') }}</option>
            </select>
            <button type="submit" class="btn btn-sm">{{ __('Filtrar') }}</button>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>{{ __('Data/hora') }}</th><th>{{ __('Usuário') }}</th><th>{{ __('Ação') }}</th><th>{{ __('Entidade') }}</th><th>{{ __('Antes') }}</th><th>{{ __('Depois') }}</th></tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td style="font-size:12px">{{ $log->created_at->format('d/m H:i') }}</td>
                        <td>{{ $log->user->username ?? __('Sistema') }}</td>
                        <td><span class="badge {{ $log->actionBadgeClass() }}">{{ $log->actionLabel() }}</span></td>
                        <td>{{ $log->entityName() }} #{{ $log->auditable_id }}</td>
                        <td style="font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis">
                            @if($log->old_values)
                                @foreach(array_slice($log->old_values, 0, 3) as $key => $val)
                                    <span style="color:var(--color-text-tertiary)">{{ $key }}:</span> {{ is_array($val) ? json_encode($val) : $val }}<br>
                                @endforeach
                            @else
                                —
                            @endif
                        </td>
                        <td style="font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis">
                            @if($log->new_values)
                                @foreach(array_slice($log->new_values, 0, 3) as $key => $val)
                                    <span style="color:var(--color-text-tertiary)">{{ $key }}:</span> {{ is_array($val) ? json_encode($val) : $val }}<br>
                                @endforeach
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="color:var(--color-text-tertiary);text-align:center">{{ __('Nenhum registro de auditoria encontrado.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        {{ $logs->appends($filters)->links() }}
    @endif
</div>
@endsection
