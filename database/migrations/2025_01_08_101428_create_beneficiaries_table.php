<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiariesTable extends Migration
{
    public function up()
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->string('reference')->unique(); // Reference number
            $table->string('type');                // Type of beneficiary (bank, p2p, airtime)

                                                                        //Bank Transfer
            $table->string('bank_transfer_account_number')->nullable(); // Beneficiary account number
            $table->string('bank_transfer_bank_name')->nullable();      // Beneficiary bank name
            $table->string('bank_transfer_bank_code')->nullable();      // Beneficiary bank name
            $table->string('bank_transfer_account_name')->nullable();   // Beneficiary account name

                                                        //P2P Transfer
            $table->string('p2p_user_tag')->nullable(); // Beneficiary account number
            $table->string('p2p_fullname')->nullable(); // Beneficiary account name

                                                                //Airtime Purchase
            $table->string('airtime_phone_number')->nullable(); // Beneficiary phone number
            $table->string('airtime_network')->nullable();      // Beneficiary network name
            $table->string('airtime_network_code')->nullable(); // Beneficiary network code

                                                                        //Bettings
            $table->string('betting_provider')->nullable();             // Beneficiary betting company
            $table->string('betting_account_number')->nullable();       // Beneficiary betting account
            $table->string('betting_account_phone_number')->nullable(); // Beneficiary betting id
            $table->string('betting_account_name')->nullable();         // Beneficiary betting id

                                                                            //Electricity
            $table->string('electricity_provider')->nullable();             // Beneficiary electricity company
            $table->string('electricity_meter_number')->nullable();         // Beneficiary electricity meter number
            $table->string('electricity_meter_type')->nullable();           // Beneficiary electricity account number
            $table->string('electricity_account_name')->nullable();         // Beneficiary electricity account name
            $table->string('electricity_account_phone_number')->nullable(); // Beneficiary electricity account number

                                                                         //Internet
            $table->string('internet_provider')->nullable();             // Beneficiary internet company
            $table->string('internet_account_number')->nullable();       // Beneficiary internet account number
            $table->string('internet_account_name')->nullable();         // Beneficiary internet account name
            $table->string('internet_account_phone_number')->nullable(); // Beneficiary internet account number

                                                                   //TV Subscription
            $table->string('tv_provider')->nullable();             // Beneficiary tv company
            $table->string('tv_card_number')->nullable();          // Beneficiary tv account number
            $table->string('tv_account_name')->nullable();         // Beneficiary tv account name
            $table->string('tv_account_phone_number')->nullable(); // Beneficiary tv account number

            $table->string('status')->default('active'); // Status of the beneficiary (active, inactive, etc.)
            $table->boolean('verified')->default(false); // Additional information about the beneficiary
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('beneficiaries');
    }
}