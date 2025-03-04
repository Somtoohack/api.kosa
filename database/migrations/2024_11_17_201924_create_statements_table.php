<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('service');
            $table->enum('type', [
                'DEBIT',
                'CREDIT',
            ])->default('DEBIT');
            $table->enum('category', [
                'BANK_TRANSFER',
                'P2P_TRANSFER',
                'AIRTIME',
                'DATA',
                'TELEVISION',
                'ELECTRICITY',
                'BETTING',
            ])->default('BANK_TRANSFER');
            $table->enum('status', [
                'PENDING',
                'SUCCESSFUL',
                'FAILED',
                'REVERSED',
                'INITIATED',
                'DECLINED',
                'RECALLED',
                'RETRIED',
                'PROCESSED',
                'PROCESSING',
            ])->default('PENDING');
            $table->string('decription')->nullable();
            $table->decimal('transaction_amount', 20, 2);
            $table->decimal('charge', 20, 2);
            $table->string('reference')->unique();
            $table->decimal('total', 20, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statements');
    }
}