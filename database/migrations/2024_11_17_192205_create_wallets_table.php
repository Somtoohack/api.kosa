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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reference')->unique();
            $table->string('key', 255)->unique();
            $table->decimal('balance', 19, 2)->default(0);         // Increased precision for balance
            $table->decimal('pending_balance', 19, 2)->default(0); // Increased precision for balance
            $table->decimal('dispute_balance', 19, 2)->default(0); // Increased precision for balance
            $table->integer('tier')->default(1);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->string('status')->default('active');          // Status of the wallet (active, inactive, etc.)
            $table->timestamp('last_transaction_at')->nullable(); // Timestamp of the last transaction
            $table->enum('wallet_type', ['main', 'custom'])->default('main');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
