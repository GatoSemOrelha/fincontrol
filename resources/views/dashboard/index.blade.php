@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="topbar">
    <span class="topbar-title">Dashboard</span>
    <div class="topbar-actions">
        <form method="GET" action="{{ route('dashboard') }}" style="display:flex;gap:8px;align-items:center">
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
    {{-- Alerta de saldo negativo (RF04) --}}
    @foreach($negativeAccounts as $account)
        <div class="alert alert-danger">
            <i class="ti ti-alert-triangle"></i>
            Conta <strong>{{ $account->name }}</strong> está negativa — saldo: R$ {{ number_format($account->current_balance, 2, ',', '.') }}
        </div>
    @endforeach

    {{-- Métricas do mês --}}
    <div class="metrics-row">
        <div class="metric-card">
            <div class="metric-label">Saldo consolidado</div>
            <div class="metric-value" style="color:{{ $consolidatedBalance >= 0 ? 'var(--color-text-success)' : 'var(--color-text-danger)' }}">
                R$ {{ number_format($consolidatedBalance, 2, ',', '.') }}
            </div>
            <div class="metric-sub">todas as contas</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Receitas no mês</div>
            <div class="metric-value" style="color:var(--color-text-success)">
                R$ {{ number_format($totals['total_income'], 2, ',', '.') }}
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Despesas no mês</div>
            <div class="metric-value" style="color:var(--color-text-danger)">
                R$ {{ number_format($totals['total_expense'], 2, ',', '.') }}
            </div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Em aberto</div>
            <div class="metric-value" style="color:var(--color-text-warning)">
                R$ {{ number_format($totals['pending_amount'], 2, ',', '.') }}
            </div>
            <div class="metric-sub">{{ $totals['pending_count'] }} lançamentos</div>
        </div>
    </div>

    <div class="grid-2">
        {{-- Saldo por conta --}}
        <div class="card">
            <div class="section-title">Saldo por conta</div>
            @foreach($accounts as $account)
                <div class="bar-row" style="cursor:pointer" onclick="location.href='{{ route('bank-accounts.index') }}'">
                    <div class="bar-label">
                        <span>{{ $account->name }}</span>
                        <span style="color:{{ $account->current_balance >= 0 ? 'var(--color-text-success)' : 'var(--color-text-danger)' }};font-weight:500">
                            R$ {{ number_format($account->current_balance, 2, ',', '.') }}
                        </span>
                    </div>
                    @php
                        $maxBalance = $accounts->max('current_balance');
                        $pct = $maxBalance > 0 ? max(5, ($account->current_balance / $maxBalance) * 100) : 5;
                    @endphp
                    <div class="progress-bg">
                        <div class="progress-fill" style="width:{{ $pct }}%;background:{{ $account->current_balance >= 0 ? 'var(--color-background-success)' : 'var(--color-background-danger)' }}"></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Receitas por categoria --}}
        <div class="card">
            <div class="section-title">Receitas por categoria</div>
            @foreach($incomeByCategory as $cat)
                <div class="bar-row">
                    <div class="bar-label">
                        <span>{{ $cat['category_name'] }}</span>
                        <span>R$ {{ number_format($cat['total'], 2, ',', '.') }}</span>
                    </div>
                    <div class="progress-bg">
                        <div class="progress-fill" style="width:{{ $cat['percentage'] }}%;background:var(--color-background-info)"></div>
                    </div>
                </div>
            @endforeach
            @if(empty($incomeByCategory))
                <div style="font-size:12px;color:var(--color-text-tertiary)">Nenhuma receita no período.</div>
            @endif
        </div>
    </div>

    {{-- Receitas por cliente --}}
    <div class="card">
        <div class="section-title" style="display:flex;justify-content:space-between;align-items:center">
            Receitas por cliente
            <a href="{{ route('reports.export-pdf', ['year' => $year, 'month' => $month]) }}" class="btn" style="font-size:12px">
                <i class="ti ti-download"></i>Exportar PDF
            </a>
        </div>
        <div class="table-wrap" style="border:none;border-radius:0">
            <table>
                <thead>
                    <tr><th>Cliente</th><th>Receita</th><th>Lançamentos</th><th>Participação</th></tr>
                </thead>
                <tbody>
                    @forelse($incomeByClient as $client)
                        <tr>
                            <td>{{ $client['client_name'] }}</td>
                            <td style="color:var(--color-text-success)">R$ {{ number_format($client['total'], 2, ',', '.') }}</td>
                            <td>{{ $client['count'] }}</td>
                            <td>{{ $client['percentage'] }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="color:var(--color-text-tertiary)">Nenhuma receita por cliente no período.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
