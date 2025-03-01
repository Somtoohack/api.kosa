<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallet_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->decimal('maximum_balance', 20, 2);
            $table->decimal('credit_daily_limit', 20, 2);
            $table->decimal('credit_weekly_limit', 20, 2);
            $table->decimal('credit_monthly_limit', 20, 2);
            $table->decimal('debit_daily_limit', 20, 2);
            $table->decimal('debit_weekly_limit', 20, 2);
            $table->decimal('debit_monthly_limit', 20, 2);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_limits');
    }
};
