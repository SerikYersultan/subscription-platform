<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'merchant_name')) {
                $table->string('merchant_name')->after('user_id')->nullable();
            }
            if (!Schema::hasColumn('subscriptions', 'next_charge_date')) {
                $table->date('next_charge_date')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $columns = array_filter(
                ['merchant_name', 'next_charge_date'],
                fn($c) => Schema::hasColumn('subscriptions', $c)
            );
            if ($columns) {
                $table->dropColumn(array_values($columns));
            }
        });
    }
};