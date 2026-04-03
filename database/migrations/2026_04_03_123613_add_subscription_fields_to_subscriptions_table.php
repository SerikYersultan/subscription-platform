<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('merchant_name')->after('user_id');
            $table->decimal('amount', 10, 2)->after('merchant_name');
            $table->string('status')->default('active')->after('amount');
            $table->date('next_charge_date')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['merchant_name', 'amount', 'status', 'next_charge_date']);
        });
    }
};