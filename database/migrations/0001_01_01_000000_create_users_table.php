<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('user_key')->unique()->default(Uuid::uuid4()->toString() . '-' . now()->timestamp);
            $table->string('country');
            $table->string('device_id')->nullable();
            $table->integer('failed_attempts')->default(0);
            $table->boolean('is_locked')->default(false);
            $table->string('device_name')->nullable();
            $table->string('email_verification_code')->nullable();
            $table->timestamp('email_verification_code_sent_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', static function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token'); // Add the used column
            $table->boolean('used')->nullable()->default(false);
            $table->timestamp('created_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
    }
};