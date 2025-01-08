<?php

namespace App\Lib;

use App\Models\GeneralSetting;
use App\Models\User;

class Vtpass
{
    public function airtime($serviceid, $amount, $phone)
    {
        $user = auth()->user();
        $general = GeneralSetting::first();
        date_default_timezone_set('America/Los_Angeles');
        $refid = date("Ymdhi") . rand(00001, 9999999999);
        $authstring = base64_encode($general->vtpusername . ":" . $general->vtppassword);
        $body = array(
            'request_id' => $refid,
            'serviceID' => $serviceid,
            'amount' => $amount,
            'phone' => $phone);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $general->vtpurl . "pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $authstring,
            ),
        ));

        $resp = curl_exec($curl);

        $response = json_decode($resp);

        curl_close($curl);
        return $response;

    }

    public function datavariations($serviceid)
    {
        $user = auth()->user();
        $general = GeneralSetting::first();
        date_default_timezone_set('America/Los_Angeles');
        $refid = date("Ymdhi") . rand(00001, 9999999999);
        $authstring = base64_encode($general->vtpusername . ":" . $general->vtppassword);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $general->vtpurl . 'service-variations?serviceID=' . $serviceid,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $authstring,
            ),
        ));

        $resp = curl_exec($curl);

        $response = json_decode($resp);

        curl_close($curl);
        return $response;

    }

    public function datarecharge($serviceid, $phone, $variation_code)
    {
        $user = auth()->user();
        $general = GeneralSetting::first();
        date_default_timezone_set('America/Los_Angeles');
        $refid = date("Ymdhi") . rand(00001, 9999999999);
        $authstring = base64_encode($general->vtpusername . ":" . $general->vtppassword);
        $body = array(
            'request_id' => $refid,
            'serviceID' => $serviceid,
            'billersCode' => $phone,
            'variation_code' => $variation_code,
            'phone' => $phone,
        );
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $general->vtpurl . "pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $authstring,
            ),
        ));

        $resp = curl_exec($curl);

        $response = json_decode($resp);

        curl_close($curl);
        return $response;

    }

    public function tvariations($serviceid)
    {
        $user = auth()->user();
        $general = GeneralSetting::first();
        date_default_timezone_set('America/Los_Angeles');
        $refid = date("Ymdhi") . rand(00001, 9999999999);
        $authstring = base64_encode($general->vtpusername . ":" . $general->vtppassword);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $general->vtpurl . 'service-variations?serviceID=' . $serviceid,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $authstring,
            ),
        ));

        $resp = curl_exec($curl);

        $response = json_decode($resp);

        curl_close($curl);
        return $response;

    }

    public function verifytv($serviceid, $phone)
    {
        $user = auth()->user();
        $general = GeneralSetting::first();
        date_default_timezone_set('America/Los_Angeles');
        $refid = date("Ymdhi") . rand(00001, 9999999999);
        $authstring = base64_encode($general->vtpusername . ":" . $general->vtppassword);
        $body = array(
            'request_id' => $refid,
            'serviceID' => $serviceid,
            'billersCode' => $phone,

        );
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $general->vtpurl . "merchant-verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $authstring,
            ),
        ));

        $resp = curl_exec($curl);

        $response = json_decode($resp);

        curl_close($curl);
        return $response;

    }

    public function tvchange($serviceid, $phone, $variation_code)
    {
        $user = auth()->user();
        $general = GeneralSetting::first();
        date_default_timezone_set('America/Los_Angeles');
        $refid = date("Ymdhi") . rand(00001, 9999999999);
        $authstring = base64_encode($general->vtpusername . ":" . $general->vtppassword);
        $body = array(
            'request_id' => $refid,
            'serviceID' => $serviceid,
            'billersCode' => $phone,
            'variation_code' => $variation_code,
            'phone' => $phone,
            'subscription_type' => "change",
        );
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $general->vtpurl . "pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $authstring,
            ),
        ));

        $resp = curl_exec($curl);

        $response = json_decode($resp);

        curl_close($curl);
        return $response;

    }

    public function tvrenew($serviceid, $phone, $variation_code)
    {
        $user = auth()->user();
        $general = GeneralSetting::first();
        date_default_timezone_set('America/Los_Angeles');
        $refid = date("Ymdhi") . rand(00001, 9999999999);
        $authstring = base64_encode($general->vtpusername . ":" . $general->vtppassword);
        $body = array(
            'request_id' => $refid,
            'serviceID' => $serviceid,
            'billersCode' => $phone,
            'variation_code' => $variation_code,
            'phone' => $phone,
            'subscription_type' => "renew",
        );
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $general->vtpurl . "pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $authstring,
            ),
        ));

        $resp = curl_exec($curl);

        $response = json_decode($resp);

        curl_close($curl);
        return $response;

    }

    public function verifyelectric($serviceid, $phone, $type)
    {
        $user = auth()->user();
        $general = GeneralSetting::first();
        date_default_timezone_set('America/Los_Angeles');
        $refid = date("Ymdhi") . rand(00001, 9999999999);
        $authstring = base64_encode($general->vtpusername . ":" . $general->vtppassword);
        $body = array(
            'request_id' => $refid,
            'serviceID' => $serviceid,
            'billersCode' => $phone,
            'type' => $type,

        );
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $general->vtpurl . "merchant-verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $authstring,
            ),
        ));

        $resp = curl_exec($curl);

        $response = json_decode($resp);

        curl_close($curl);
        return $response;

    }

    public function electricpay($serviceid, $phone, $variation_code, $amount)
    {
        $user = auth()->user();
        $general = GeneralSetting::first();
        date_default_timezone_set('America/Los_Angeles');
        $refid = date("Ymdhi") . rand(00001, 9999999999);
        $authstring = base64_encode($general->vtpusername . ":" . $general->vtppassword);
        $body = array(
            'request_id' => $refid,
            'serviceID' => $serviceid,
            'billersCode' => $phone,
            'variation_code' => $variation_code,
            'phone' => $phone,
            'amount' => $amount,
        );
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $general->vtpurl . "pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $authstring,
            ),
        ));

        $resp = curl_exec($curl);

        $response = json_decode($resp);

        curl_close($curl);
        return $response;

    }

    public function eduvariations($serviceid)
    {
        $user = auth()->user();
        $general = GeneralSetting::first();
        date_default_timezone_set('America/Los_Angeles');
        $refid = date("Ymdhi") . rand(00001, 9999999999);
        $authstring = base64_encode($general->vtpusername . ":" . $general->vtppassword);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $general->vtpurl . 'service-variations?serviceID=' . $serviceid,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $authstring,
            ),
        ));

        $resp = curl_exec($curl);

        $response = json_decode($resp);

        curl_close($curl);
        return $response;

    }

    public function edupay($serviceid, $phone, $variation_code)
    {
        $user = auth()->user();
        $general = GeneralSetting::first();
        date_default_timezone_set('America/Los_Angeles');
        $refid = date("Ymdhi") . rand(00001, 9999999999);
        $authstring = base64_encode($general->vtpusername . ":" . $general->vtppassword);
        $body = array(
            'request_id' => $refid,
            'serviceID' => $serviceid,
            'billersCode' => $phone,
            'variation_code' => $variation_code,
            'phone' => $phone,

        );
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $general->vtpurl . "pay",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . $authstring,
            ),
        ));

        $resp = curl_exec($curl);

        $response = json_decode($resp);

        curl_close($curl);
        return $response;

    }

}