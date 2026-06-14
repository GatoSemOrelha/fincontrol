<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FinControl - O Controle Financeiro que a sua Empresa Merece</title>
    <meta name="description" content="FinControl é um sistema completo para gestão de fluxo de caixa, cartões e categorias em tempo real.">

    <!-- Estilo Isolado da Landing Page -->
    <link href="{{ asset('css/landing.css') }}" rel="stylesheet">
    
    <!-- Ícones Lucide -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>

    <!-- Navegação -->
    <header class="navbar glass-header">
        <div class="container">
            <a href="#" class="nav-brand">
                <i data-lucide="wallet" style="color: var(--color-brand-accent)"></i>
                FinControl
            </a>
            
            <nav class="nav-links">
                <a href="#inicio">Início</a>
                <a href="#funcionalidades">Funcionalidades</a>
                <a href="#sobre">Sobre o Projeto</a>
            </nav>

            <div class="nav-actions">
                <a href="{{ route('login') }}" class="btn-primary btn-large" style="padding: 10px 24px; font-size: 0.95rem;">Acessar Sistema</a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="inicio" class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="badge">
                    <i data-lucide="trending-up" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle; margin-right: 4px;"></i>
                    Gestão Inteligente & Automática
                </div>
                <h1>O Controle Financeiro que a sua Empresa Merece</h1>
                <p>Abandone planilhas confusas. Tenha visão total do seu fluxo de caixa, despesas com cartões e faturas recorrentes em um único painel intuitivo e em tempo real.</p>
                
                <div class="hero-buttons">
                    <a href="{{ route('login') }}" class="btn-primary btn-large">
                        Entrar no Dashboard
                        <i data-lucide="arrow-right" style="width: 20px; height: 20px; vertical-align: middle; margin-left: 8px;"></i>
                    </a>
                    <a href="#funcionalidades" class="btn-secondary btn-large">
                        Conhecer os Recursos
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Funcionalidades -->
    <section id="funcionalidades" class="features">
        <div class="container">
            <div class="section-header">
                <h2>Tudo o que você precisa</h2>
                <p>Ferramentas corporativas projetadas para entregar agilidade, segurança e relatórios precisos.</p>
            </div>

            <div class="grid">
                <!-- Card 1 -->
                <div class="card glass-panel">
                    <div class="card-icon">
                        <i data-lucide="bar-chart-2"></i>
                    </div>
                    <h3>Fluxo de Caixa Consolidado</h3>
                    <p>Monitore entradas e saídas de múltiplas contas bancárias em um dashboard unificado, com projeções e alertas de saldo.</p>
                </div>

                <!-- Card 2 -->
                <div class="card glass-panel">
                    <div class="card-icon">
                        <i data-lucide="credit-card"></i>
                    </div>
                    <h3>Gestão de Cartões de Crédito</h3>
                    <p>Cadastre cartões, acompanhe limites em tempo real e consolide os lançamentos em faturas fechadas automaticamente.</p>
                </div>

                <!-- Card 3 -->
                <div class="card glass-panel">
                    <div class="card-icon">
                        <i data-lucide="refresh-cw"></i>
                    </div>
                    <h3>Despesas Recorrentes</h3>
                    <p>Automatize o registro de assinaturas, aluguéis e contas fixas para nunca mais perder um vencimento.</p>
                </div>

                <!-- Card 4 -->
                <div class="card glass-panel">
                    <div class="card-icon">
                        <i data-lucide="users"></i>
                    </div>
                    <h3>Controle de Acessos</h3>
                    <p>Gerencie quem pode visualizar ou editar lançamentos com perfis rigorosos de Administrador e Visualizador.</p>
                </div>

                <!-- Card 5 -->
                <div class="card glass-panel">
                    <div class="card-icon">
                        <i data-lucide="shield-check"></i>
                    </div>
                    <h3>Auditoria Total (Logs)</h3>
                    <p>Tenha rastreabilidade corporativa. Saiba exatamente quem criou, alterou ou excluiu cada movimentação financeira.</p>
                </div>

                <!-- Card 6 -->
                <div class="card glass-panel">
                    <div class="card-icon">
                        <i data-lucide="pie-chart"></i>
                    </div>
                    <h3>Relatórios em PDF</h3>
                    <p>Gere extratos robustos, relatórios categorizados e fechamentos de mês em PDF com um único clique para o seu contador.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="sobre" class="footer">
        <div class="container">
            <i data-lucide="shield" style="width: 32px; height: 32px; color: var(--color-brand-accent); margin-bottom: 16px;"></i>
            <h3 style="margin-bottom: 8px; font-weight: 600;">Projeto FinControl</h3>
            <p>Construído com obsessão por qualidade de software e performance.</p>
            <p style="margin-top: 24px; font-size: 0.85rem; opacity: 0.7;">&copy; {{ date('Y') }} Felipe de Assumpção Amaral Ponte. Feito com 💙 e muito ☕</p>
        </div>
    </footer>

    <!-- Inicializa os ícones -->
    <script>
        lucide.createIcons();
        
        // Efeito Navbar Scroll
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('.navbar');
            if(window.scrollY > 50) {
                nav.style.background = 'rgba(0, 0, 0, 0.8)';
            } else {
                nav.style.background = 'rgba(0, 0, 0, 0.6)';
            }
        });
    </script>
</body>
</html>
