<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('source_hash', 64)->nullable()->after('currency');
            $table->unique(['user_id', 'source_hash'], 'tx_user_hash_unique');
            $table->index(['user_id', 'transaction_date'], 'tx_user_date_idx');
            $table->index('transaction_date', 'tx_date_idx');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique('tx_user_hash_unique');
            $table->dropIndex('tx_user_date_idx');
            $table->dropIndex('tx_date_idx');
            $table->dropColumn('source_hash');
        });
    }
};
