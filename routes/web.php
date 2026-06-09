<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CreditCardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RecurringExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — FinControl
|--------------------------------------------------------------------------
*/

// ─── Autenticação ─────────────────────────────────
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── Rotas autenticadas ───────────────────────────
Route::middleware(['auth'])->group(function () {

    // Redirect raiz para dashboard
    Route::get('/', fn () => redirect()->route('dashboard'));

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Troca de Idioma
    Route::get('/lang/{locale}', [\App\Http\Controllers\LanguageController::class, 'switch'])->name('lang.switch');

    // Lançamentos
    Route::resource('transactions', TransactionController::class)->except(['create', 'edit', 'show']);
    Route::post('/transactions/{transaction}/pay', [TransactionController::class, 'pay'])->name('transactions.pay');
    Route::post('/transactions/check-impact', [TransactionController::class, 'checkImpact'])->name('transactions.check-impact');

    // Contas bancárias
    Route::resource('bank-accounts', BankAccountController::class)->except(['create', 'edit', 'show']);

    // Categorias (Apenas Admin)
    Route::resource('categories', CategoryController::class)
        ->except(['create', 'edit', 'show', 'destroy'])
        ->middleware('role:Administrador');

    // Clientes
    Route::resource('clients', ClientController::class)->except(['create', 'edit', 'show']);

    // Cartões de crédito
    Route::resource('credit-cards', CreditCardController::class)->except(['create', 'edit', 'show']);
    Route::post('/credit-cards/{creditCard}/pay-invoice', [CreditCardController::class, 'payInvoice'])->name('credit-cards.pay-invoice');

    // Investimentos
    Route::resource('investments', InvestmentController::class)->except(['create', 'edit', 'show', 'destroy']);

    // Parcelamentos / Invoices (RF07)
    Route::resource('invoices', InvoiceController::class)->only(['index', 'store']);

    // Despesas recorrentes (RF09)
    Route::resource('recurring-expenses', RecurringExpenseController::class)->except(['create', 'edit', 'show']);

    // Relatórios
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::post('/reports/close', [ReportController::class, 'close'])->name('reports.close');
    Route::get('/reports/cash-flow', [ReportController::class, 'cashFlow'])->name('reports.cash-flow');

    // Auditoria
    Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');

    // Usuários (somente admin via Policy e Middleware)
    Route::resource('users', UserController::class)
        ->except(['create', 'edit', 'show', 'destroy'])
        ->middleware('role:Administrador');
});
