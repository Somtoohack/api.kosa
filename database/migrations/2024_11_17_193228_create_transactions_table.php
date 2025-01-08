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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 20, 2);
            $table->decimal('balance_before', 20, 2);
            $table->decimal('post_balance', 20, 2);
            $table->decimal('charge', 20, 2);
            $table->enum('type', ['credit', 'debit']);
            $table->enum('category', ['withdrawal', 'deposit', 'bill', 'transfer']);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->string('reference')->unique();
            $table->string('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};