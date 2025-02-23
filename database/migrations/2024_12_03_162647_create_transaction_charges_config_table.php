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
        Schema::create('transaction_charges_config', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type');                   // e.g., 'deposit', 'withdrawal', 'transfer'
            $table->decimal('charge_amount', 10, 2);              // Charge amount
            $table->decimal('charge_percent', 10, 2)->nullable(); // Charge amount
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_charges_config');
    }
};
