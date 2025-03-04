<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

class CreateBusinessesTable extends Migration
{
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('business_key')->unique()->default(Uuid::uuid4()->toString() . '-' . now()->timestamp);
            $table->string('country')->nullable();
            $table->string('device_id')->nullable();
            $table->string('device_name')->nullable();
            $table->string('email_verification_code')->nullable();
            $table->timestamp('email_verification_code_sent_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('business_password_reset_tokens', static function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token'); // Add the used column
            $table->boolean('used')->nullable()->default(false);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('businesses');
        Schema::dropIfExists('business_password_reset_tokens');
    }
}