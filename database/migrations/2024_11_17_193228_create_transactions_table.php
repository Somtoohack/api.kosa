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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->decimal('charge', 15, 2)->default(0.00);
            $table->decimal('net_amount', 15, 2);
            $table->unsignedBigInteger('service_id')->nullable(); // Reference to the specific service table
            $table->enum('service', ['deposit', 'withdrawal', 'p2p_transfer', 'bank_transfer',
                'airtime_purchase', 'data_purchase', 'tv_subscription',
                'electricity_subscription', 'internet_subscription', 'betting_wallet_funding']);
            $table->decimal('balance_before', 20, 2);
            $table->decimal('post_balance', 20, 2);
            $table->enum('type', ['credit', 'debit']);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->string('reference')->unique();
            $table->json('meta')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('transaction_date')->useCurrent(); // Date and time of the transaction
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
