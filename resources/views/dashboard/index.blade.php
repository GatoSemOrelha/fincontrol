@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="topbar">
    <span class="topbar-title">Dashboard</span>
    <div class="topbar-actions">
        <form method="GET" action="{{ route('dashboard') }}" class="filter-row" style="margin-bottom:0;">
            <select name="month" style="width:auto;font-size:12px;padding:5px 8px" onchange="this.form.submit()">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
            <select name="year" style="width:auto;font-size:12px;padding:5px 8px" onchange="this.form.submit()">
                @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </form>
    </div>
</div>

<div class="content">
    {{-- Alerta de saldo negativo --}}
    @foreach($negativeAccounts as $account)
        <div class="alert alert-danger pulse-danger">
            <i class="ti ti-alert-circle"></i>
            <div>
                {{ __('Atenção: A conta') }} <strong>{{ $account->name }}</strong> {{ __('encontra-se negativa com saldo de') }} <strong>{{ money($account->current_balance) }}</strong>
            </div>
        </div>
    @endforeach

    {{-- Métricas do mês Premium --}}
    <div class="metrics-row">
        <div class="metric-card">
            <div class="metric-header">
                <span class="metric-title">{{ __('Saldo Consolidado') }}</span>
                <div class="metric-icon-wrap icon-balance"><i class="ti ti-wallet"></i></div>
            </div>
            <div class="metric-value-large" style="color:{{ $consolidatedBalance >= 0 ? 'var(--color-text-success)' : 'var(--color-text-danger)' }}">
                {{ money($consolidatedBalance) }}
            </div>
            <div class="metric-footer">
                <i class="ti ti-building-bank"></i> {{ __('Soma de todas as contas') }}
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-header">
                <span class="metric-title">{{ __('Receitas no mês') }}</span>
                <div class="metric-icon-wrap icon-income"><i class="ti ti-trending-up"></i></div>
            </div>
            <div class="metric-value-large" style="color:var(--color-text-primary)">
                {{ money($totals['total_income'] ?? 0) }}
            </div>
            <div class="metric-footer">
                @if(isset($variations['income']))
                    <span class="badge badge-{{ $variations['income'] >= 0 ? 'success' : 'danger' }}">
                        <i class="ti {{ $variations['income'] >= 0 ? 'ti-arrow-up-right' : 'ti-arrow-down-right' }}"></i>
                        {{ number_format(abs($variations['income']), 1, ',', '.') }}%
                    </span>
                    {{ __('vs. mês anterior') }}
                @else
                    {{ __('Sem histórico anterior') }}
                @endif
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-header">
                <span class="metric-title">{{ __('Despesas no mês') }}</span>
                <div class="metric-icon-wrap icon-expense"><i class="ti ti-trending-down"></i></div>
            </div>
            <div class="metric-value-large" style="color:var(--color-text-primary)">
                {{ money($totals['total_expense'] ?? 0) }}
            </div>
            <div class="metric-footer">
                @if(isset($variations['expense']))
                    <span class="badge badge-{{ $variations['expense'] <= 0 ? 'success' : 'danger' }}">
                        <i class="ti {{ $variations['expense'] <= 0 ? 'ti-arrow-down-right' : 'ti-arrow-up-right' }}"></i>
                        {{ number_format(abs($variations['expense']), 1, ',', '.') }}%
                    </span>
                    {{ __('vs. mês anterior') }}
                @else
                    {{ __('Sem histórico anterior') }}
                @endif
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-header">
                <span class="metric-title">{{ __('Contas Bancárias') }}</span>
                <div class="metric-icon-wrap icon-card"><i class="ti ti-credit-card"></i></div>
            </div>
            <div class="metric-value-large" style="color:var(--color-text-primary)">
                {{ count($accounts) }}
            </div>
            <div class="metric-footer">
                <i class="ti ti-calendar-event"></i> {{ __('Contas cadastradas ativas') }}
            </div>
        </div>
    </div>

    {{-- Restante do Dashboard - Gráficos e Tabelas --}}
    <div class="metrics-row">
        {{-- Receitas por categoria --}}
        <div class="card">
            <div class="section-title">{{ __('Receitas por categoria') }}</div>
            @foreach($incomeByCategory as $cat)
                <div class="bar-row">
                    <div class="bar-label">
                        <span>{{ $cat['category_name'] }}</span>
                        <span style="font-weight: 600">{{ money($cat['total']) }}</span>
                    </div>
                    <div class="progress-bg">
                        <div class="progress-fill animate-bar" style="width:{{ $cat['percentage'] }}%;background:var(--color-background-success);"></div>
                    </div>
                </div>
            @endforeach
            @if(empty($incomeByCategory))
                <div style="font-size:13px;color:var(--color-text-tertiary);text-align:center;padding:20px 0">{{ __('Nenhuma receita no período.') }}</div>
            @endif
        </div>
    </div>

    {{-- Receitas por cliente --}}
    <div class="card" style="margin-top: 24px;">
        <div class="section-title" style="display:flex;justify-content:space-between;align-items:center;padding-bottom:16px">
            {{ __('Receitas por cliente') }}
            <a href="{{ route('reports.export-pdf', ['year' => $year, 'month' => $month]) }}" class="btn" data-turbo="false" target="_blank">
                <i class="ti ti-download"></i>{{ __('Exportar PDF') }}
            </a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('Cliente') }}</th>
                        <th>{{ __('Receita') }}</th>
                        <th>{{ __('Lançamentos') }}</th>
                        <th>{{ __('Participação') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomeByClient as $client)
                        <tr>
                            <td>{{ $client['client_name'] }}</td>
                            <td style="color:var(--color-text-success); font-weight:600">{{ money($client['total']) }}</td>
                            <td>
                                <span class="badge badge-info">{{ $client['count'] }}</span>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <span style="font-size:13px;font-weight:600;width:35px">{{ $client['percentage'] }}%</span>
                                    <div class="progress-bg" style="flex:1;">
                                        <div class="progress-fill" style="width:{{ $client['percentage'] }}%;background:var(--color-text-info);"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="color:var(--color-text-tertiary);text-align:center;padding:24px">{{ __('Nenhuma receita por cliente no período.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
