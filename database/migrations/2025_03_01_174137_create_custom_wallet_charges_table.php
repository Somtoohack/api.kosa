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
        Schema::create('custom_wallet_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->string('transaction_type'); // e.g., 'deposit', 'withdrawal', 'transfer'
            $table->string('charge_currency');  // e.g., 'USD', 'NGN'
            $table->decimal('charge_amount', 19, 2);
            $table->decimal('charge_percent', 19, 2)->nullable(); // Charge percentage
            $table->decimal('charge_cap', 19, 2)->nullable();     // Charge percentage

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_wallet_charges');
    }
};