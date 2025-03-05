<?php
namespace App\Services;

use App\Lib\KosaMicroservice;

class RedbillerService extends KosaMicroservice
{
    // Define redbiller endpoints as constants
    const ENDPOINT_TRANSFER                   = 'redbiller/bank/transfer';
    const ENDPOINT_VERIFY_TRANSFER            = 'redbiller/bank/transfer/verify';
    const ENDPOINT_BANK_LIST                  = 'redbiller/bank/list';
    const ENDPOINT_VERIFY_BANK_ACCOUNT        = 'redbiller/bank/account/verify';
    const ENDPOINT_BALANCE                    = 'redbiller/balance';
    const ENDPOINT_STATEMENT                  = 'redbiller/statement';
    const ENDPOINT_CREATE_PSA                 = 'redbiller/psa/create';
    const ENDPOINT_PSA_BALANCE                = 'redbiller/psa/balance';
    const ENDPOINT_PURCHASE_AIRTIME           = 'redbiller/bills/airtime/purchase';
    const ENDPOINT_VERIFY_AIRTIME             = 'redbiller/bills/airtime/verify';
    const ENDPOINT_RETRY_AIRTIME              = 'redbiller/bills/airtime/retry';
    const ENDPOINT_AIRTIME_RETRIED_TRAIL      = 'redbiller/bills/airtime/retried-trail';
    const ENDPOINT_PURCHASE_DATA              = 'redbiller/bills/data/purchase';
    const ENDPOINT_DATA_PLANS                 = 'redbiller/bills/data/plans';
    const ENDPOINT_VERIFY_DATA                = 'redbiller/bills/data/verify';
    const ENDPOINT_RETRY_DATA                 = 'redbiller/bills/data/retry';
    const ENDPOINT_DATA_RETRIED_TRAIL         = 'redbiller/bills/data/retried-trail';
    const ENDPOINT_PURCHASE_ELECTRICITY       = 'redbiller/bills/electricity/purchase';
    const ENDPOINT_VERIFY_METER               = 'redbiller/bills/electricity/verify-meter';
    const ENDPOINT_VERIFY_ELECTRICITY         = 'redbiller/bills/electricity/verify-purchase';
    const ENDPOINT_PURCHASE_CABLE             = 'redbiller/bills/cable/purchase';
    const ENDPOINT_CABLE_PLANS                = 'redbiller/bills/cable/plans';
    const ENDPOINT_VERIFY_DECODER             = 'redbiller/bills/cable/verify-decoder';
    const ENDPOINT_VERIFY_CABLE               = 'redbiller/bills/cable/verify';
    const ENDPOINT_PURCHASE_INTERNET          = 'redbiller/bills/internet/purchase';
    const ENDPOINT_INTERNET_PLANS             = 'redbiller/bills/internet/plans';
    const ENDPOINT_VERIFY_DEVICE              = 'redbiller/bills/internet/verify-device';
    const ENDPOINT_VERIFY_INTERNET            = 'redbiller/bills/internet/verify';
    const ENDPOINT_CREDIT_BETTING             = 'redbiller/bills/betting/credit';
    const ENDPOINT_BETTING_PROVIDERS          = 'redbiller/bills/betting/providers';
    const ENDPOINT_VERIFY_BETTING_ACCOUNT     = 'redbiller/bills/betting/verify-account';
    const ENDPOINT_VERIFY_BETTING             = 'redbiller/bills/betting/verify';
    const ENDPOINT_LOOKUP_BVN                 = 'redbiller/kyc/bvn/lookup';
    const ENDPOINT_VERIFY_BVN2                = 'redbiller/kyc/bvn/verify/2.0';
    const ENDPOINT_VERIFY_BVN                 = 'redbiller/kyc/bvn/verify/1.0';
    const ENDPOINT_VERIFY_BVN3                = 'redbiller/kyc/bvn/verify/3.0';
    const ENDPOINT_FIND_BANK_ACCOUNT          = 'redbiller/kyc/bank-account/find';
    const ENDPOINT_LOOKUP_BANK_ACCOUNT        = 'redbiller/kyc/bank-account/lookup';
    const ENDPOINT_BANK_ACCOUNT_TIER          = 'redbiller/kyc/bank-account/tier';
    const ENDPOINT_VALIDATE_BANK_ACCOUNT_TIER = 'redbiller/kyc/bank-account/validate-tier';
    const ENDPOINT_VERIFY_PHONE               = 'redbiller/kyc/phone/verify';
    const ENDPOINT_VERIFY_NIN                 = 'redbiller/kyc/nin/verify';
    const ENDPOINT_VERIFY_VOTERS_CARD         = 'redbiller/kyc/voters-card/verify';
    const ENDPOINT_VERIFY_PASSPORT            = 'redbiller/kyc/passport/verify';
    const ENDPOINT_VERIFY_DRIVERS_LICENSE     = 'redbiller/kyc/drivers-license/verify';
    const ENDPOINT_DEPOSIT_WEBHOOK            = 'redbiller/webhook/deposit/receive';
    const ENDPOINT_VERIFY_DEPOSIT_WEBHOOK     = 'redbiller/webhook/deposit/verify';

    // Bank Transfer
    public function initiateBankTransfer($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_TRANSFER, $data);
    }

    public function verifyBankTransfer($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_TRANSFER, $data);
    }

    public function verifyDepositReference($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_DEPOSIT_WEBHOOK, $data);
    }

    public function getBankList()
    {
        return $this->makeRequest('get', self::ENDPOINT_BANK_LIST);
    }

    public function verifyBankAccount($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_BANK_ACCOUNT, $data);
    }

    // Balance and Statement
    public function getBalance()
    {
        return $this->makeRequest('get', self::ENDPOINT_BALANCE);
    }

    public function getTransactionStatement()
    {
        return $this->makeRequest('get', self::ENDPOINT_STATEMENT);
    }

    // Payment Sub Account (PSA)
    public function createPaymentSubAccount($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_CREATE_PSA, $data);
    }

    public function getPSABalance($reference)
    {
        return $this->makeRequest('get', self::ENDPOINT_PSA_BALANCE . "/{$reference}");
    }

    // Bill Payments
    public function purchaseAirtime($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_PURCHASE_AIRTIME, $data);
    }

    public function verifyAirtimePurchase($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_AIRTIME, $data);
    }

    public function retryAirtimePurchase($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_RETRY_AIRTIME, $data);
    }

    public function getAirtimeRetriedTrail()
    {
        return $this->makeRequest('get', self::ENDPOINT_AIRTIME_RETRIED_TRAIL);
    }

    public function purchaseDataPlan($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_PURCHASE_DATA, $data);
    }

    public function getDataPlans($product)
    {
        return $this->makeRequest('get', self::ENDPOINT_DATA_PLANS . "/{$product}");
    }

    public function verifyDataPlanPurchase($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_DATA, $data);
    }

    public function retryDataPlanPurchase($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_RETRY_DATA, $data);
    }

    public function getDataPlanRetriedTrail()
    {
        return $this->makeRequest('get', self::ENDPOINT_DATA_RETRIED_TRAIL);
    }

    public function purchaseElectricity($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_PURCHASE_ELECTRICITY, $data);
    }

    public function verifyMeterNumber($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_METER, $data);
    }

    public function verifyElectricityPurchase($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_ELECTRICITY, $data);
    }

    public function purchaseCableTV($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_PURCHASE_CABLE, $data);
    }

    public function getCablePlans($product)
    {
        return $this->makeRequest('get', self::ENDPOINT_CABLE_PLANS . "/{$product}");
    }

    public function verifyDecoderNumber($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_DECODER, $data);
    }

    public function verifyCableTVPurchase($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_CABLE, $data);
    }

    public function purchaseInternet($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_PURCHASE_INTERNET, $data);
    }

    public function getInternetPlans($product)
    {
        return $this->makeRequest('get', self::ENDPOINT_INTERNET_PLANS . "/{$product}");
    }

    public function verifyDeviceNumber($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_DEVICE, $data);
    }

    public function verifyInternetPurchase($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_INTERNET, $data);
    }

    public function creditBettingAccount($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_CREDIT_BETTING, $data);
    }

    public function getBettingProviders()
    {
        return $this->makeRequest('get', self::ENDPOINT_BETTING_PROVIDERS);
    }

    public function verifyBettingAccount($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_BETTING_ACCOUNT, $data);
    }

    public function verifyBettingPayment($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_BETTING, $data);
    }

    // KYC
    public function lookupBVN($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_LOOKUP_BVN, $data);
    }

    public function verifyBVN($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_BVN, $data);
    }
    public function verifyBVN2($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_BVN2, $data);
    }

    public function verifyBVN3($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_BVN3, $data);
    }

    public function findBankAccount($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_FIND_BANK_ACCOUNT, $data);
    }

    public function lookupBankAccount($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_LOOKUP_BANK_ACCOUNT, $data);
    }

    public function getBankAccountTier($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_BANK_ACCOUNT_TIER, $data);
    }

    public function validateBankAccountTier($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VALIDATE_BANK_ACCOUNT_TIER, $data);
    }

    public function verifyPhoneNumber($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_PHONE, $data);
    }

    public function verifyNIN($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_NIN, $data);
    }

    public function verifyVotersCard($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_VOTERS_CARD, $data);
    }

    public function verifyPassport($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_PASSPORT, $data);
    }

    public function verifyDriversLicense($data)
    {
        return $this->makeRequest('post', self::ENDPOINT_VERIFY_DRIVERS_LICENSE, $data);
    }

}