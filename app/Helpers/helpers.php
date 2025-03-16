<?php

use App\Lib\ClientInfo;
use App\Models\Transaction;
use App\Models\TransactionChargesConfig;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

function systemDetails()
{
    $system['name']          = 'Beamer Mock API';
    $system['version']       = '1.0';
    $system['build_version'] = '1.0';
    return $system;
}

function slug($string)
{
    return Str::slug($string);
}

function verificationCode($length)
{
    if ($length == 0) {
        return 0;
    }
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters       = '1234567890';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getTrx($length = 12)
{
    $characters       = strtolower('ABCDEFGHJKMNOPQRSTUVWXYZ123456789');
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    $timestamp = Carbon::now()->format('YmdHis');
    $key       = $randomString . $timestamp;

    $key = str_shuffle($key);
    return strrev($key);
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount ?? 0, $length);
    return $amount + 0;
}

function keyToTitle($text)
{
    return ucfirst(preg_replace('/[^A-Za-z0-9 ]/', ' ', $text));
}

function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}

function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}

function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}

function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}

function getImage($image, $size = null)
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }
    if ($size) {
        return route('placeholder.image', $size);
    }
    return asset('assets/images/default.png');
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function showDateTime($date, $format = 'Y-m-d h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}

function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}

function getRealIP()
{
    $ip = $_SERVER['REMOTE_ADDR'];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}

function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, 'dateSort');
    return $arr;
}

function keyGenerator($length = 50)
{
    $characters = 'abcdefghijklmnpqrstuvwxyz0123456789';
    $string     = '';
    $max        = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[mt_rand(0, $max)];
    }
    return $string;
}

function randomKeyGen($length = 36)
{
    $characters = 'abcdefghijklmnpqrstuvwxyz0123456789';
    $string     = '';
    $max        = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[mt_rand(0, $max)];
    }
    $timestamp = Carbon::now()->format('YmdHis');
    $key       = $string . $timestamp;
    return strrev($key);
}

function currencyConverter($amount, $rate)
{
    return $amount / $rate;
}

function toBaseCurrency($amount, $rate)
{
    return $amount * $rate;
}

function chargeCalculator($amount, $percent, $fixed)
{
    $percentCharge = ($amount * $percent) / 100;
    return $fixed + $percentCharge;
}

function getSleepImage()
{
    $images = glob('assets/images/frontend/sleep/*');
    $image  = $images[rand(0, count(@$images ?? []) - 1)];

    if ($image) {
        $image = asset($image);
    } else {
        $image = asset('assets/images/frontend/sleep/default.jpg');
    }

    return $image;
}

function getCountries()
{
    $countries = json_decode(file_get_contents(base_path('assets/country.json')));
    return $countries;
}

function getCountryDetails($countryCode)
{
    $countries = getCountries();
    foreach ($countries as $country) {
        if (strtolower($country->code) == strtolower($countryCode)) {
            return $country;
        }
    }
    return null;
}

function getValidatedToken(Request $request)
{
    $token = $request->header('Authorization');

    if (! $token) {
        return null;
    }

    $token = str_replace('Bearer ', '', $token);
    return $token;
}

function getIpDetails($ipAddress)
{
    try {
        $response = Http::get('https://ipapi.co/' . $ipAddress . '/json/');
        $response->throw(); // throw an exception if the response is not successful (200-299)
        $data = $response->json();

        return [
            'country'   => $data['country_name'],
            'region'    => $data['region'],
            'city'      => $data['city'],
            'latitude'  => $data['latitude'],
            'longitude' => $data['longitude'],
        ];
    } catch (\Illuminate\Http\Client\RequestException $e) {
        // catch request exceptions, such as connection errors or timeouts
        Log::error("Error fetching IP data: " . $e->getMessage());
        return null; // or return a default value, or throw a custom exception
    } catch (\JsonException $e) {
        // catch JSON parsing errors
        Log::error("Error parsing IP data JSON: " . $e->getMessage());
        return null; // or return a default value, or throw a custom exception
    } catch (\Exception $e) {
        // catch any other exceptions
        Log::error("Error fetching IP data: " . $e->getMessage());
        return null; // or return a default value, or throw a custom exception
    }
}

//Create a helper function to check and calculate specific transaction charges for a transaction on user specific wallets using the walletid
function getTransactionCharges($wallet, $amount, $transactionType)
{
    Log::info("Fetching charge config for type: $transactionType and amount: $amount");
    $charge = TransactionChargesConfig::where('transaction_type', $transactionType)
        ->where('currency', $wallet->currency->code)
        ->first();

    $chargeAmount  = $charge ? $charge->charge_amount : 0;
    $chargePercent = $charge ? $charge->charge_percent : 0;

    if ($wallet->customWalletCharges != null) {
        $customCharges = $wallet->customWalletCharges->where('transaction_type', $transactionType)
            ->where('charge_currency', $wallet->currency->code)
            ->first();
        if ($customCharges != null) {
            Log::info("Custom wallet charge found with charge amount: " . $customCharges->charge_amount . " and charge percent: " . $customCharges->charge_percent);
            $chargeAmount  = $customCharges->charge_amount;
            $chargePercent = $customCharges->charge_percent;
        }
    } else {
        Log::info("No custom wallet charge found for wallet with ID: $wallet->id");

    }

    Log::info([
        'charge_amount'     => $chargeAmount,
        'type'              => $transactionType,
        'calculated_charge' => floatval($chargeAmount + ($chargePercent / 100) * $amount),
        'amount'            => $amount,
        'charge_percent'    => $chargePercent,
    ]);

    return (object) [
        'charge_amount'     => $chargeAmount,
        'type'              => $transactionType,
        'calculated_charge' => floatval($chargeAmount + ($chargePercent / 100) * $amount),
        'amount'            => $amount,
        'charge_percent'    => $chargePercent,
    ];
}

//Create a helper function to check and calculate wallet credit  limits for a wallet
function checkTransactionLimits(Wallet $wallet, float $amount, string $type, array $limits): array
{
    $dailyTotal = $wallet->transactions()
        ->where('type', $type)
        ->whereDate('created_at', today())
        ->sum('amount');

    if ($dailyTotal + $amount > $limits['daily_limit']) {
        return [
            'can_transact'    => false,
            'message'         => ucfirst($type) . ' daily limit exceeded',
            'limit_type'      => 'daily',
            'spent'           => number_format($dailyTotal, 2),
            'remaining_limit' => number_format($limits['daily_limit'] - $dailyTotal, 2),
        ];
    }

    $weeklyTotal = $wallet->transactions()
        ->where('type', $type)
        ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
        ->sum('amount');

    if ($weeklyTotal + $amount > $limits['weekly_limit']) {
        return [
            'can_transact'    => false,
            'message'         => ucfirst($type) . ' weekly limit exceeded',
            'limit_type'      => 'weekly',
            'spent'           => number_format($weeklyTotal, 2),
            'remaining_limit' => number_format($limits['weekly_limit'] - $weeklyTotal, 2),
        ];
    }

    $monthlyTotal = $wallet->transactions()
        ->where('type', $type)
        ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
        ->sum('amount');

    if ($monthlyTotal + $amount > $limits['monthly_limit']) {
        return [
            'can_transact'    => false,
            'message'         => ucfirst($type) . ' monthly limit exceeded',
            'limit_type'      => 'monthly',
            'spent'           => number_format($monthlyTotal, 2),
            'remaining_limit' => number_format($limits['monthly_limit'] - $monthlyTotal, 2),
        ];
    }

    return [
        'can_transact' => true,
        'message'      => 'Transaction within ' . $type . ' limits',
    ];
}

function logDepositStatement($wallet, $deposit, $initialBalance, )
{

    $transaction = new Transaction([
        'wallet_id'      => $wallet->id,
        'user_id'        => $wallet->user_id,
        'amount'         => $deposit->amount,
        'net_amount'     => $deposit->net_amount,
        'charge'         => $deposit->charge,
        'balance_before' => $initialBalance,
        'post_balance'   => $wallet->balance,
        'type'           => 'credit',
        'status'         => 'success',
        'description'    => 'Deposit of ' . number_format($deposit->net_amount, 2) . ' to ' . $wallet->currency->code . ' wallet',
        'reference'      => $deposit->provider_reference,
        'service'        => 'deposit',
    ]);
    $transaction->save();

    return $transaction;
}