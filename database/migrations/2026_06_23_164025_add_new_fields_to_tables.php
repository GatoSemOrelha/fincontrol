<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->string('pix_key', 100)->nullable();
            $table->string('document', 20)->nullable();
            $table->string('agency', 20)->nullable();
            $table->string('account_number', 50)->nullable();
        });

        Schema::table('credit_cards', function (Blueprint $table) {
            $table->decimal('credit_limit', 15, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropColumn(['pix_key', 'document', 'agency', 'account_number']);
        });

        Schema::table('credit_cards', function (Blueprint $table) {
            $table->dropColumn(['credit_limit']);
        });
    }
};
