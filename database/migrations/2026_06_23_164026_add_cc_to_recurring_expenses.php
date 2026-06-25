<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recurring_expenses', function (Blueprint $table) {
            $table->foreignId('credit_card_id')->nullable()->constrained('credit_cards')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('recurring_expenses', function (Blueprint $table) {
            $table->dropForeign(['credit_card_id']);
            $table->dropColumn('credit_card_id');
        });
    }
};
