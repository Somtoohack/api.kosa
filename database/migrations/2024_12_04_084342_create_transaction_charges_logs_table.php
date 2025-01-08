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
        Schema::create('transaction_charges_logs', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_reference')->index();
            $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade');
            $table->string('transaction_type'); // 'credit' or 'debit'
            $table->float('charge_amount');
            $table->float('profit_amount')->nullable(); // Only applicable for credits
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_charges_logs');
    }
};