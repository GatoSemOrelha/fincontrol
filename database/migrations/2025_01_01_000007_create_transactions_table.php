<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Lançamentos financeiros (transações).
 * RF02 — Registrar entradas e saídas.
 * RF03 — Bloquear edição de lançamentos pagos.
 * RF04 — Alerta de saldo negativo.
 * RF05 — Vincular nota fiscal ao lançamento.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('description', 255);
            $table->decimal('amount', 15, 2);
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            $table->enum('transaction_type', ['INCOME', 'EXPENSE']);
            $table->enum('status', ['PENDING', 'PAID'])->default('PENDING');
            $table->boolean('is_recurring')->default(false);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->restrictOnDelete();
            $table->foreignId('credit_card_id')->nullable()->constrained('credit_cards')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('invoice_document_url', 500)->nullable();
            $table->timestamps();

            // Índices para performance em relatórios e filtros
            $table->index(['user_id', 'due_date']);
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'transaction_type']);
            $table->index(['bank_account_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
