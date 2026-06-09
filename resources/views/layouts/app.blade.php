<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FinControl') — Gestão Financeira</title>
    <meta name="description" content="Sistema de gestão financeira empresarial">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
<div class="app" id="app">

    {{-- SIDEBAR --}}
    <div class="sidebar">
        <div class="sidebar-logo">
            <i class="ti ti-building-bank"></i>
            FinControl
        </div>
        <div class="sidebar-inner">
            <div class="nav-section">
                <div class="nav-label">Principal</div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="ti ti-layout-dashboard"></i>Dashboard
                </a>
                <a href="{{ route('transactions.index') }}" class="nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                    <i class="ti ti-list"></i>Lançamentos
                </a>
                <a href="{{ route('bank-accounts.index') }}" class="nav-item {{ request()->routeIs('bank-accounts.*') ? 'active' : '' }}">
                    <i class="ti ti-credit-card"></i>Contas bancárias
                </a>
                <a href="{{ route('categories.index') }}" class="nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="ti ti-tag"></i>Categorias
                </a>
                <a href="{{ route('credit-cards.index') }}" class="nav-item {{ request()->routeIs('credit-cards.*') ? 'active' : '' }}">
                    <i class="ti ti-credit-card"></i>Cartões de crédito
                </a>
                <a href="{{ route('investments.index') }}" class="nav-item {{ request()->routeIs('investments.*') ? 'active' : '' }}">
                    <i class="ti ti-chart-line"></i>Investimentos
                </a>
            </div>
            <div class="nav-section">
                <div class="nav-label">Cadastros</div>
                <a href="{{ route('clients.index') }}" class="nav-item {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                    <i class="ti ti-briefcase"></i>Clientes
                </a>
                <a href="{{ route('invoices.index') }}" class="nav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                    <i class="ti ti-receipt-2"></i>Parcelamentos
                </a>
                <a href="{{ route('recurring-expenses.index') }}" class="nav-item {{ request()->routeIs('recurring-expenses.*') ? 'active' : '' }}">
                    <i class="ti ti-repeat"></i>Despesas fixas
                </a>
            </div>
            <div class="nav-section">
                <div class="nav-label">Relatórios</div>
                <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                    <i class="ti ti-chart-bar"></i>Relatório mensal
                </a>
                <a href="{{ route('reports.cash-flow') }}" class="nav-item {{ request()->routeIs('reports.cash-flow') ? 'active' : '' }}">
                    <i class="ti ti-trending-up"></i>Fluxo de caixa
                </a>
                <a href="{{ route('audit.index') }}" class="nav-item {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                    <i class="ti ti-history"></i>Auditoria
                </a>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="nav-section">
                <div class="nav-label">Configurações</div>
                <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="ti ti-users"></i>Usuários
                </a>
            </div>
            @endif
        </div>
        <div class="user-pill">
            <div class="avatar">{{ auth()->user()->initials() }}</div>
            <div style="flex:1">
                <div style="font-size:12px;font-weight:500">{{ auth()->user()->username }}</div>
                <div style="font-size:11px;color:var(--color-text-tertiary)">{{ auth()->user()->role->name }}</div>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" style="background:none;border:none;cursor:pointer;padding:0">
                    <i class="ti ti-logout" style="font-size:16px;color:var(--color-text-tertiary)" title="Sair"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="main">
        @yield('content')
    </div>

</div>

{{-- Toast notifications --}}
<div class="toast" id="toast">
    <i class="ti ti-circle-check"></i>
    <span id="toast-msg"></span>
</div>

<script>
    // ─── Toast ────────────────────────────────────
    let toastTimer;
    function showToast(msg, type = 'success') {
        const toast = document.getElementById('toast');
        document.getElementById('toast-msg').textContent = msg;
        toast.className = 'toast' + (type === 'error' ? ' toast-error' : '');
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // ─── Modal ────────────────────────────────────
    function openModal(id) {
        document.getElementById(id).classList.add('open');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    // Fechar modal com ESC
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
        }
    });

    // Fechar modal clicando fora
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', e => {
            if (e.target === overlay) overlay.classList.remove('open');
        });
    });

</script>

    {{-- Flash Messages Invisíveis (Para o JS ler sem gerar erro no VS Code) --}}
    @if(session('success'))
        <div id="flash-success" data-message="{{ session('success') }}" style="display:none;"></div>
    @endif
    @if(session('error'))
        <div id="flash-error" data-message="{{ session('error') }}" style="display:none;"></div>
    @endif

<script>
    // ─── Flash Messages ────────────────────────────
    const flashSuccess = document.getElementById('flash-success');
    if (flashSuccess) {
        showToast(flashSuccess.getAttribute('data-message'));
    }

    const flashError = document.getElementById('flash-error');
    if (flashError) {
        showToast(flashError.getAttribute('data-message'), 'error');
    }
</script>

{{-- Botão Menu Mobile --}}
<button class="mobile-menu-btn" id="mobile-menu-btn" onclick="toggleMobileMenu()">
    <i class="ti ti-menu-2" style="font-size:24px;"></i>
</button>

<script>
    function toggleMobileMenu() {
        document.querySelector('.sidebar').classList.toggle('open');
    }
    
    // Fechar menu ao clicar fora dele na versão mobile
    document.addEventListener('click', function(e) {
        const sidebar = document.querySelector('.sidebar');
        const btn = document.getElementById('mobile-menu-btn');
        if (sidebar && sidebar.classList.contains('open') && !sidebar.contains(e.target) && !btn.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });
</script>

@stack('scripts')
</body>
</html>
