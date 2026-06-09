<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório Mensal — FinControl</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1a1a19; line-height: 1.5; }

        .header { background: #185fa5; color: white; padding: 24px 30px; margin-bottom: 20px; }
        .header h1 { font-size: 18px; font-weight: 600; }
        .header p { font-size: 11px; opacity: 0.85; margin-top: 4px; }

        .section { margin: 0 30px 20px; }
        .section-title { font-size: 13px; font-weight: 600; color: #185fa5; border-bottom: 1px solid #ddd; padding-bottom: 6px; margin-bottom: 10px; }

        .metrics { display: flex; gap: 12px; margin: 0 30px 20px; }
        .metric-box { flex: 1; background: #f5f5f4; border-radius: 6px; padding: 12px; text-align: center; }
        .metric-label { font-size: 10px; color: #5f5e5a; margin-bottom: 4px; }
        .metric-value { font-size: 18px; font-weight: 600; }
        .text-success { color: #3b6d11; }
        .text-danger { color: #a32d2d; }

        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th { background: #f5f5f4; padding: 6px 8px; text-align: left; font-weight: 600; font-size: 9px; color: #5f5e5a; border-bottom: 1px solid #ddd; text-transform: uppercase; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) td { background: #fafafa; }

        .bar-container { margin-bottom: 8px; }
        .bar-label { display: flex; justify-content: space-between; font-size: 10px; margin-bottom: 2px; }
        .bar-bg { background: #eee; border-radius: 3px; height: 5px; }
        .bar-fill { border-radius: 3px; height: 5px; }
        .bar-success { background: #97c459; }
        .bar-danger { background: #f09595; }

        .footer { margin-top: 30px; padding: 12px 30px; border-top: 1px solid #ddd; font-size: 9px; color: #888; text-align: center; }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>FinControl — Relatório Mensal</h1>
        <p>{{ $report->periodLabel() }} · Gerado em {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    {{-- Métricas --}}
    <table style="width:90%;margin:0 auto 20px;border:none">
        <tr>
            <td style="border:none;text-align:center;background:#eaf3de;border-radius:6px;padding:12px">
                <div style="font-size:10px;color:#3b6d11">Receitas</div>
                <div style="font-size:16px;font-weight:600;color:#3b6d11">R$ {{ number_format($report->total_income, 2, ',', '.') }}</div>
            </td>
            <td style="border:none;width:10px"></td>
            <td style="border:none;text-align:center;background:#fcebeb;border-radius:6px;padding:12px">
                <div style="font-size:10px;color:#a32d2d">Despesas</div>
                <div style="font-size:16px;font-weight:600;color:#a32d2d">R$ {{ number_format($report->total_expense, 2, ',', '.') }}</div>
            </td>
            <td style="border:none;width:10px"></td>
            <td style="border:none;text-align:center;background:#e6f1fb;border-radius:6px;padding:12px">
                <div style="font-size:10px;color:#185fa5">Resultado líquido</div>
                <div style="font-size:16px;font-weight:600;color:#185fa5">R$ {{ number_format($report->net_result, 2, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    {{-- Receitas por categoria --}}
    @if(!empty($data['income_by_category']))
    <div class="section">
        <div class="section-title">Receitas por categoria</div>
        <table>
            <thead><tr><th>Categoria</th><th>Total</th><th>Participação</th></tr></thead>
            <tbody>
                @foreach($data['income_by_category'] as $cat)
                    <tr>
                        <td>{{ $cat['category_name'] }}</td>
                        <td style="color:#3b6d11;font-weight:600">R$ {{ number_format($cat['total'], 2, ',', '.') }}</td>
                        <td>{{ $cat['percentage'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Despesas por categoria --}}
    @if(!empty($data['expenses_by_category']))
    <div class="section">
        <div class="section-title">Despesas por categoria</div>
        <table>
            <thead><tr><th>Categoria</th><th>Total</th><th>Participação</th></tr></thead>
            <tbody>
                @foreach($data['expenses_by_category'] as $cat)
                    <tr>
                        <td>{{ $cat['category_name'] }}</td>
                        <td style="color:#a32d2d;font-weight:600">R$ {{ number_format($cat['total'], 2, ',', '.') }}</td>
                        <td>{{ $cat['percentage'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Receitas por cliente --}}
    @if(!empty($data['income_by_client']))
    <div class="section">
        <div class="section-title">Receitas por cliente</div>
        <table>
            <thead><tr><th>Cliente</th><th>Total</th><th>Lançamentos</th><th>Participação</th></tr></thead>
            <tbody>
                @foreach($data['income_by_client'] as $client)
                    <tr>
                        <td>{{ $client['client_name'] }}</td>
                        <td style="color:#3b6d11;font-weight:600">R$ {{ number_format($client['total'], 2, ',', '.') }}</td>
                        <td>{{ $client['count'] }}</td>
                        <td>{{ $client['percentage'] }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Lista de transações --}}
    @if(!empty($data['transactions']))
    <div class="page-break"></div>
    <div class="header">
        <h1>Extrato detalhado</h1>
        <p>{{ $report->periodLabel() }}</p>
    </div>
    <div class="section">
        <table>
            <thead>
                <tr><th>Data</th><th>Descrição</th><th>Categoria</th><th>Conta</th><th>Tipo</th><th>Valor</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach($data['transactions'] as $tx)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($tx['due_date'])->format('d/m') }}</td>
                        <td>{{ $tx['description'] }}</td>
                        <td>{{ $tx['category']['name'] ?? '—' }}</td>
                        <td>{{ $tx['bank_account']['name'] ?? '—' }}</td>
                        <td>{{ $tx['transaction_type'] === 'INCOME' ? 'Entrada' : 'Saída' }}</td>
                        <td style="color:{{ $tx['transaction_type'] === 'INCOME' ? '#3b6d11' : '#a32d2d' }};font-weight:600">
                            R$ {{ number_format($tx['amount'], 2, ',', '.') }}
                        </td>
                        <td>{{ $tx['status'] === 'PAID' ? 'Pago' : 'Em aberto' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        FinControl — Sistema de Gestão Financeira · Relatório gerado automaticamente · {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
