<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositsTable extends Migration
{
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('virtual_bank_account_id')->constrained()->onDelete('cascade');
            $table->string('provider_reference')->unique();
            $table->decimal('amount', 15, 2);
            $table->decimal('charge', 15, 2)->default(0.00);
            $table->decimal('net_amount', 15, 2);
            $table->json('payload')->nullable();
            $table->enum('status', ['pending', 'successful', 'failed']);
            $table->string('sender_name');                   // Name of the sender
            $table->string('sender_account_number');         // Sender's account number
            $table->string('sender_bank_name');              // Sender's bank name
            $table->timestamp('deposit_date')->useCurrent(); // Date and time of the deposit
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('deposits');
    }
}
