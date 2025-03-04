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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();        // ISO 4217 currency code
            $table->string('name', 200);             // Currency name
            $table->string('symbol', 10);            // Currency symbol
            $table->string('country_code', 10);      // ISO 3166-1 alpha-2 country code
            $table->string('country_name', 100);     // Country name
            $table->string('flag');                  // URL to the country flag
            $table->string('dial_code', 10);         // International dialing code
            $table->decimal('exchange_rate', 19, 8); // Exchange rate to base currency
            $table->string('reference')->unique();
            $table->boolean('is_active')->default(true); // Status of the currency
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
