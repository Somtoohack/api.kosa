<?php

namespace App\Lib;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;

class Flutterwave
{
      public function fluttercard($payment_request){
           $general = GeneralSetting::first();
           $secret = $general->flutterwave_secret;
           
 $url = "https://api.flutterwave.com/v3/virtual-cards";

$data = array (
    
    "currency" => $payment_request["currency"],
    "amount" => $payment_request["amount"],
    "billing_name" => $payment_request["name"],
    
    
   );
$request = json_encode($data);
 
 
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
  CURLOPT_POSTFIELDS =>$request,
  CURLOPT_HTTPHEADER => array(
   'Authorization: Bearer '.$secret,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return json_decode($response);
        
        
    }
        public function loadfunds($amount,$currency,$card_id){
        
        
        $url = "https://api.flutterwave.com/v3/virtual-cards/".$card_id."/fund";
 $general = GeneralSetting::first();
           $secret = $general->flutterwave_secret;
$data = array (
    
    "debit_currency" => $currency,
    "amount" => $amount
 
   );
$request = json_encode($data);
 
 
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
  CURLOPT_POSTFIELDS =>$request,
  CURLOPT_HTTPHEADER => array(
   'Authorization: Bearer '.$secret,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return json_decode($response);
        
        
        
    }
    
    public function getcard($card_id) {
           $url = "https://api.flutterwave.com/v3/virtual-cards/".$card_id;
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
    'Authorization: Bearer '.$secret,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return json_decode($response);
        
    }
    
    
      public function gettransaction($card_id, $from, $to) {
           $url = "https://api.flutterwave.com/v3/virtual-cards/".$card_id."/transactions?from=".$from."&to=".$to."&index=1&size=99999999";
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
    'Authorization: Bearer '.$secret,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return $response;
        
    }

    
    
    public function blockcard($card_id) {
           $url = "https://api.flutterwave.com/v3/virtual-cards/".$card_id."/status/block";
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
  CURLOPT_CUSTOMREQUEST => "PUT",
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '.$secret,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return json_decode($response);
        
    }
    
     public function unblockcard($card_id) {
           $url = "https://api.flutterwave.com/v3/virtual-cards/".$card_id."/status/unblock";
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
  CURLOPT_CUSTOMREQUEST => "PUT",
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '.$secret,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return json_decode($response);
        
    }
    
        public function createvirtualbank($body){
        
        
        $url = "https://api.flutterwave.com/v3/virtual-account-numbers";
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
  CURLOPT_POSTFIELDS =>$request,
  CURLOPT_HTTPHEADER => array(
   'Authorization: Bearer '.$secret,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return json_decode($response);
        
        
        
    }
    
      public function accountdetails($batchid) {
           $url = "https://api.flutterwave.com/v3/virtual-account-numbers/".$batchid;
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
    'Authorization: Bearer '.$secret,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return json_decode($response);
        
    }
             public function verifytrans($transid) {
           $url = "https://api.flutterwave.com/v3/transactions/".$transid."/verify";
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
    'Authorization: Bearer '.$secret,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return json_decode($response);
        
    }
      public function momotransfer($body){
        
        
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
  CURLOPT_POSTFIELDS =>$request,
  CURLOPT_HTTPHEADER => array(
   'Authorization: Bearer '.$secret,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return json_decode($response);
        
        
        
    }
    
     public function getmomo($id){
        
        
        $url = "https://api.flutterwave.com/v3/transfers/".$id;
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
   'Authorization: Bearer '.$secret,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return json_decode($response);
        
        
        
    }
    public function getbanktrans($tx_ref) {
           $url = "https://api.flutterwave.com/v3/transactions?tx_ref=".$tx_ref;
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
    'Authorization: Bearer '.$secret,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
return json_decode($response);
        
    } 
    
}