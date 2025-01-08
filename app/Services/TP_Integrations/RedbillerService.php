<?php

namespace App\Services\TP_Integrations;

use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Log;

class RedbillerService
{
    protected $rbSecretKey;
    protected $rbPublicKey;

    public function __construct()
    {
        $this->rbSecretKey = env('RB_SECRET_KEY');
        $this->rbPublicKey = env('RB_PUBLIC_KEY');
    }

    public function createvirtualbank($body)
    {

        $url = "https://api.test.redbiller.com/1.0/collections/PSA/create";

        $request = json_encode($body);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => array(
                'Private-Key: ' . $this->rbSecretKey,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        Log::info(print_r($response, true));
        return json_decode($response);

    }

    public function getAllVirtualAccount($params = [])
    {
        $url = "https://api.live.redbiller.com/1.0/collections/PSA/list";

        // Build query string from parameters
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Private-Key: ' . $this->rbSecretKey,
                'Content-Type: application/json',
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        Log::info(print_r($response, true));
        return json_decode($response);
    }

    public function accountdetails($batchid)
    {
        $url = "https://api.flutterwave.com/v3/virtual-account-numbers/" . $batchid;
        $general = GeneralSetting::first();
        $secret = $general->flutterwave_secret;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $secret,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);

    }
    public function verifytrans($transid)
    {
        $url = "https://api.flutterwave.com/v3/transactions/" . $transid . "/verify";
        $general = GeneralSetting::first();
        $secret = $general->flutterwave_secret;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $secret,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);

    }
    public function momotransfer($body)
    {

        $url = "https://api.flutterwave.com/v3/transfers";
        $general = GeneralSetting::first();
        $secret = $general->flutterwave_secret;

        $request = json_encode($body);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $request,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $secret,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);

    }

    public function getmomo($id)
    {

        $url = "https://api.flutterwave.com/v3/transfers/" . $id;
        $general = GeneralSetting::first();
        $secret = $general->flutterwave_secret;

        $request = json_encode($body);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",

            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $secret,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);

    }
    public function getbanktrans($tx_ref)
    {
        $url = "https://api.flutterwave.com/v3/transactions?tx_ref=" . $tx_ref;
        $general = GeneralSetting::first();
        $secret = $general->flutterwave_secret;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $secret,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);

    }

}