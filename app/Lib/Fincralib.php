<?php

namespace App\Lib;

use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Models\User;

class Fincralib
{
    public function createaccount($data){
     $user = auth()->user();
     $general = GeneralSetting::first();
   
    $body = json_encode($data);
        $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://sandboxapi.fincra.com/profile/virtual-accounts/requests/",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $body,
  CURLOPT_HTTPHEADER => array(
    'api-key: '.$general->fincra_secretkey,
    'content-type: application/json',
    'accept: application/json'
  ),
));

$resp = curl_exec($curl);

$response = json_decode($resp);

curl_close($curl);
return $response;
        
    }
 
  public function getaccount($accountid){
       $user = auth()->user();
     $general = GeneralSetting::first();
    
      $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL =>  'https://sandboxapi.fincra.com/profile/virtual-accounts/'.$accountid,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
   CURLOPT_HTTPHEADER => array(
     'api-key: '.$general->fincra_secretkey,
    'content-type: application/json',
    'accept: application/json'
  ),
));

$resp = curl_exec($curl);

$response = json_decode($resp);

curl_close($curl);
return $response;

  }  
  
  public function getcurrency($currency){
       $user = auth()->user();
     $general = GeneralSetting::first();
    
      $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL =>  'https://sandboxapi.fincra.com/profile/virtual-accounts?currency='.$currency,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
   CURLOPT_HTTPHEADER => array(
     'api-key: '.$general->fincra_secretkey,
    'content-type: application/json',
    'accept: application/json'
  ),
));

$resp = curl_exec($curl);

$response = json_decode($resp);

curl_close($curl);
return $response;

  }  
  
  

 
  
}

?>