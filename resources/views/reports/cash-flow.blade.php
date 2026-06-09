@extends('layouts.app')
@section('title', 'Fluxo de Caixa')

@section('content')
<div class="topbar">
    <span class="topbar-title">{{ __('Projeção de fluxo de caixa — 6 meses') }}</span>
</div>

<div class="content">
    <div class="metrics-row" style="margin-bottom:20px">
        <div class="metric-card">
            <div class="metric-label">{{ __('Saldo atual (contas)') }}</div>
            <div class="metric-value" style="color:var(--color-text-success)">
                R$ {{ number_format($projection['current_balance'], 2, ',', '.') }}
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-label">{{ __('Total em CDB') }}</div>
            <div class="metric-value" style="color:var(--color-text-info)">
                R$ {{ number_format($projection['cdb_total'], 2, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="card">
        <div class="section-title">{{ __('Projeção mensal (RF11)') }}</div>
        <div class="table-wrap" style="border:none">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Mês') }}</th>
                        <th>{{ __('Receita projetada') }}</th>
                        <th>{{ __('Despesa projetada') }}</th>
                        <th>{{ __('Resultado') }}</th>
                        <th>{{ __('Rendimento CDB') }}</th>
                        <th>{{ __('Saldo projetado') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projection['months'] as $m)
                        <tr>
                            <td style="font-weight:500">{{ $m['label'] }}</td>
                            <td style="color:var(--color-text-success)">R$ {{ number_format($m['projected_income'], 2, ',', '.') }}</td>
                            <td style="color:var(--color-text-danger)">R$ {{ number_format($m['projected_expense'], 2, ',', '.') }}</td>
                            <td style="color:{{ $m['net'] >= 0 ? 'var(--color-text-success)' : 'var(--color-text-danger)' }}">
                                R$ {{ number_format($m['net'], 2, ',', '.') }}
                            </td>
                            <td style="color:var(--color-text-info)">R$ {{ number_format($m['cdb_yield'], 2, ',', '.') }}</td>
                            <td style="font-weight:500;color:{{ $m['total_with_investments'] >= 0 ? 'var(--color-text-success)' : 'var(--color-text-danger)' }}">
                                R$ {{ number_format($m['total_with_investments'], 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
