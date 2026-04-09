<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Schema improvements for the transactions table:
 *
 *  - source_hash  MD5 fingerprint (user_id|date|amount|merchant) used for deduplication.
 *                 Nullable so existing rows without a hash are not affected.
 *  - Unique index on (user_id, source_hash) — prevents re-importing the same row.
 *                 NULL values do not violate uniqueness, so legacy rows are safe.
 *  - Composite index on (user_id, transaction_date) — speeds up dashboard / filter queries.
 *  - Index on transaction_date alone — used by date-range filters.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('source_hash', 64)->nullable()->after('currency');

            // Deduplication: one unique hash per user (NULLs are exempt)
            $table->unique(['user_id', 'source_hash'], 'tx_user_hash_unique');

            // Query performance
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
