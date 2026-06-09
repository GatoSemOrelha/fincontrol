<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Investimentos.
 * RF11 — Projeção de fluxo de caixa incluindo CDB.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->enum('type', ['CDB', 'LCI', 'LCA', 'TESOURO_DIRETO', 'ACAO', 'FUNDO_IMOBILIARIO', 'OUTRO']);
            $table->decimal('initial_amount', 15, 2);
            $table->decimal('current_amount', 15, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('interest_rate', 8, 4)->default(0);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
