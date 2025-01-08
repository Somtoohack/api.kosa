<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id(); // This will create an auto-incrementing primary key
            $table->string('sitename', 40)->nullable();
            $table->string('fiat_api_key', 255)->nullable();
            $table->string('crypto_api_key', 255)->nullable();
            $table->string('public_key', 255)->nullable();
            $table->string('private_key', 255)->nullable();
            $table->string('merchant_id', 255)->nullable();
            $table->boolean('dark')->default(0)->comment('1=> Dark Template Enable, 2=> Dark Template Disable');
            $table->string('cur_text', 40)->nullable()->comment('currency text');
            $table->string('cur_sym', 40)->nullable()->comment('currency symbol');
            $table->string('email_from', 40)->nullable();
            $table->string('sms_api', 255)->nullable();
            $table->string('base_color', 40)->nullable();
            $table->string('secondary_color', 40)->nullable();
            $table->text('mail_config')->nullable()->comment('email configuration');
            $table->text('sms_config')->nullable();
            $table->boolean('ev')->default(0)->comment('email verification, 0 - dont check, 1 - check');
            $table->boolean('en')->default(0)->comment('email notification, 0 - dont send, 1 - send');
            $table->boolean('sv')->default(0)->comment('sms verification, 0 - dont check, 1 - check');
            $table->boolean('sn')->default(0)->comment('sms notification, 0 - dont send, 1 - send');
            $table->tinyInteger('va')->default(0);
            $table->tinyInteger('cd')->default(0);
            $table->boolean('force_ssl')->default(0);
            $table->boolean('secure_password')->default(0);
            $table->boolean('agree')->default(0);
            $table->boolean('registration')->default(0)->comment('0: Off, 1: On');
            $table->string('active_template', 40)->nullable();
            $table->text('sys_version')->nullable();
            $table->timestamp('last_cron')->nullable();
            $table->text('modules')->nullable();
            $table->string('reloadly_id', 255)->nullable();
            $table->string('reloadly_secret', 255)->nullable();
            $table->string('reloadly', 12)->nullable();
            $table->string('flutterwave_secret', 255)->nullable();
            $table->string('flutterwave_public', 255)->nullable();
            $table->string('flutterwave_merchant', 255)->nullable();
            $table->string('ezpin_secret', 255)->nullable();
            $table->string('ezpin_id', 255)->nullable();
            $table->string('doc', 555)->nullable();
            $table->timestamps(); // This will create created_at and updated_at columns
            $table->decimal('usd_fee', 28, 8)->nullable();
            $table->decimal('ngn_fee', 28, 8)->nullable();
            $table->decimal('load_fee', 20, 2)->nullable();
            $table->decimal('pg_fee', 28, 8)->nullable();
            $table->decimal('tx_fee', 28, 8)->nullable();
            $table->decimal('ev_fee', 28, 8)->nullable();
            $table->string('merchant_key', 255)->nullable();
            $table->string('card_bin', 4)->default('7249');
            $table->string('apto_url', 555)->nullable();
            $table->string('apto_mobile_key', 255)->nullable();
            $table->string('apto_public_key', 255)->nullable();
            $table->string('apto_secret_key', 255)->nullable();
            $table->string('apto_balance_id', 255)->nullable();
            $table->string('verifai', 255)->nullable();
            $table->boolean('deposit_commission')->default(1);
            $table->boolean('invest_commission')->default(1);
            $table->boolean('invest_return_commission')->default(1);
            $table->decimal('signup_bonus_amount', 11, 2)->nullable();
            $table->boolean('signup_bonus_control')->nullable();
            $table->text('off_day')->nullable();
            $table->integer('b_transfer')->nullable();
            $table->decimal('f_charge', 18, 8)->nullable();
            $table->decimal('p_charge', 18, 8)->nullable();
            $table->decimal('user_cashout', 11, 2)->nullable();
            $table->decimal('user_cashin', 11, 2)->nullable();
            $table->decimal('agent_cashpay', 11, 2)->nullable();
            $table->decimal('agent_cashrcv', 11, 2)->nullable();
            $table->boolean('verifai_act')->default(0);
            $table->boolean('tremendous')->default(0);
            $table->string('tremendous_key', 100)->nullable();
            $table->string('tremendous_us', 100)->nullable();
            $table->string('tremendous_row', 100)->nullable();
            $table->string('tremendous_mode', 100)->nullable();
            $table->decimal('usdvisa_loadfee', 6, 2)->nullable();
            $table->decimal('usdvisa_fee', 6, 2)->default(0.00);
            $table->boolean('vonage')->default(1);
            $table->string('vonagesecret', 100)->nullable();
            $table->string('vonageapikey', 100)->nullable();
            $table->decimal('crypto_masterfee', 6, 2)->nullable();
            $table->boolean('crypto_master')->default(1);
            $table->boolean('games')->nullable();
            $table->string('onramper', 100)->nullable();
            $table->boolean('investments')->nullable();
            $table->boolean('remittance')->default(0);
            $table->integer('referred_to')->nullable();
            $table->integer('referred_by')->nullable();
            $table->integer('signup_bonus')->nullable();
            $table->string('redeem_minpoints', 6)->nullable();
            $table->string('redeem_perpoints', 6)->nullable();
            $table->string('crypto_perpoints', 6)->nullable();
            $table->string('fiat_perpoints', 6)->nullable();
            $table->string('fiat_minpoints', 6)->nullable();
            $table->boolean('points')->default(0);
            $table->string('stripefee', 500)->nullable();
            $table->decimal('cryptoload_fee', 6, 2)->nullable();
            $table->boolean('crypto')->nullable();
            $table->boolean('physical')->default(0);
            $table->decimal('physical_fee', 11, 2)->nullable();
            $table->decimal('cryptomerch_fee', 28, 8)->nullable();
            $table->decimal('ccard_fee', 20, 2)->nullable();
            $table->decimal('visa_fee', 6, 2)->default(0.00);
            $table->decimal('visa_loadfee', 6, 2)->default(0.00);
            $table->boolean('livitpay')->default(0);
            $table->string('livitpay_public', 100)->nullable();
            $table->string('livitpay_private', 100)->nullable();
            $table->string('tatum_mode', 50)->nullable();
            $table->string('tatum_key', 255)->nullable();
            $table->string('stripePublishableKey', 500)->nullable();
            $table->string('stripeuniquekey', 500)->nullable();
            $table->string('stripeSecretKey', 500)->nullable();
            $table->boolean('kyc')->nullable();
            $table->string('juvidoe_mode', 15)->nullable();
            $table->boolean('dps')->nullable();
            $table->boolean('loan')->nullable();
            $table->boolean('fdr')->nullable();
            $table->decimal('crypto_monthly', 6, 2)->default(0.00);
            $table->decimal('fiat_monthly', 6, 2)->default(0.00);
            $table->longText('kyc_modules')->nullable();
            $table->boolean('transfers')->nullable();
            $table->string('vtpusername', 255)->nullable();
            $table->string('vtppassword', 255)->nullable();
            $table->string('vtpurl', 255)->nullable();
            $table->string('fincra_secretkey', 255)->nullable();
            $table->string('kuda_email', 100)->nullable();
            $table->string('kuda_apikey', 100)->nullable();
            $table->string('kuda_url', 200)->nullable();
            $table->boolean('payment_deposit')->nullable();
            $table->boolean('user_transfer')->nullable();
            $table->boolean('payment_withdrawal')->nullable();
            $table->string('plaid_client', 255)->nullable();
            $table->string('plaid_secret', 255)->nullable();
            $table->string('plaid_url', 255)->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('general_settings');
    }
}