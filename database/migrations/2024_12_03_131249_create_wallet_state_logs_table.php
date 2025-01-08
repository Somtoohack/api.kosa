<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletStateLogsTable extends Migration
{
    public function up()
    {
        Schema::create('wallet_state_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade');
            $table->string('state'); // 'debit_disabled' or 'credit_disabled'
            $table->string('reason')->nullable(); // Reason for the state change
            $table->json('metadata')->nullable(); // Store additional data as JSON
            $table->timestamp('applied_at'); // When the state was applied
            $table->timestamps(); // Created and updated timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_state_logs');
    }
}