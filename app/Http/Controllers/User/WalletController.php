<?php
namespace App\Http\Controllers\User;

use App\Constants\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Models\VirtualBankAccount;
use App\Models\Wallet;
use App\Services\RedbillerService;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService, RedbillerService $redbiller)
    {
        $this->walletService = $walletService;
        $this->redbiller     = $redbiller;

    }

    public function getBalance(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'wallet_key' => 'required|exists:wallets,key',
        ]);

        $wallet = Wallet::where('key', $request->input('wallet_key'))
            ->where('user_id', $user->id)
            ->first();

        if (! $wallet) {
            return $this->sendError('Invalid wallet', [], ErrorCodes::TRY_AGAIN);
        }

        $balance = $this->walletService->getBalance($user, $wallet);

        return $this->sendResponse(
            $balance,
            'Balance retrieved successfully.'
        );
    }
    public function checkCharges(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'amount'       => 'required|numeric|min:1',
            'service_type' => 'required|string|max:255',
        ]);

        // Retrieve the authenticated user
        $user = $request->user();

        // Get the amount and service type from the request
        $amount      = $request->input('amount');
        $serviceType = $request->input('service_type');

        // Call the WalletService to check for transaction charges
        $chargeDetails = $this->walletService->checkTransactionCharges($user, $amount, $serviceType);

        // Return the response based on the success of the operation
        if ($chargeDetails['success']) {
            return $this->sendResponse(
                $chargeDetails,
                'Transaction charges retrieved successfully.'
            );
        } else {
            return $this->sendError(
                $chargeDetails['message']
            );
        }
    }

    public function deposit(Request $request)
    {

        $request->validate([
            'amount'     => 'required|numeric|min:10|gt:10',
            'wallet_key' => 'required|exists:wallets,key',
        ]);
        $amount = $request->amount;
        $userId = Auth::id();

        $response = $this->walletService->deposit(Auth::user(), $amount, $request->wallet_key);

        if ($response['success']) {
            return $this->sendResponse(
                $response['message']
            );
            return response()->json($response);
        } else {
            return $this->sendError(
                $response['message'],
                $response['reason'],
            );
        }

    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10|gt:10',
        ]);
        $amount   = $request->amount; // Get the amount from the request
        $userId   = Auth::id();
        $response = $this->walletService->withdraw(Auth::user(), $amount);
        return response()->json($response);
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'amount'   => 'required|numeric|min:50.00',
            'user_tag' => 'required|string|max:255|exists:user_profiles,user_tag',
        ], [
            'user_tag.exists' => 'Recipient not found.',
            'amount.min'      => 'Minimum transfer amount is ₦50.00.',
        ]);

        $userId      = Auth::id();
        $userTag     = $request->input('user_tag');
        $recipient   = UserProfile::where('user_tag', $userTag)->first();
        $recipientId = $recipient->user_id;

        $amount = $request->input('amount');

        $result = $this->walletService->transfer($userId, $recipientId, $amount);

        if ($result['success']) {
            return $this->sendResponse(
                $result['message']
            );
            return response()->json($result);
        } else {
            return $this->sendError(
                $result['message']
            );
        }
    }

    public function getTransactions(Request $request)
    {
        $userId       = Auth::id();
        $perPage      = $request->input('per_page', 15);
        $transactions = $this->walletService->getTransactions($userId, $perPage);
        return response()->json($transactions);
    }

    public function createVbaRedbiller(Request $request)
    {

        $user = $request->user();

        $reference = randomKeyGen(20);

        if (! $user->profile) {
            return $this->sendError('Please create a profile first', [], ErrorCodes::TRY_AGAIN);
        }

        $userKYC = $user->kyc()->first();
        // check if theres kyc and bvn has not been validated
        if (! $userKYC || $userKYC->bvn_validated == false) {
            return $this->sendError('Please complete your KYC first', [], ErrorCodes::TRY_AGAIN);
        }

        $request->validate([
            'bank'       => 'required|string',
            'wallet_key' => 'required|string|exists:wallets,key',
        ]);

        $bank   = $request->input('bank');
        $wallet = Wallet::where('key', $request->input('wallet_key'))
            ->where('user_id', $user->id)
            ->first();

        if (! $wallet) {
            return $this->sendError('Invalid wallet', [], ErrorCodes::TRY_AGAIN);
        }

        $body = [
            "email"           => $user->email,
            "bvn"             => $user->kyc->bvn, //"22513739970",
            "bank"            => $bank,
            "first_name"      => $user->profile->first_name,
            "surname"         => $user->profile->last_name,
            "phone_no"        => $user->profile->phone_number,
            "date_of_birth"   => $user->profile->date_of_birth,
            "auto_settlement" => 'false',
            "reference"       => $reference,
        ];

        // return $body;

        $response = $this->redbiller->createPaymentSubAccount($body);

        if ($response['response'] == 200 && $response['status'] === 'true') {

            $createdVBA = VirtualBankAccount::create([
                'user_id'        => $user->id,
                'wallet_id'      => $wallet->id,
                'wallet_key'     => $wallet->key,
                'account_number' => $response['details']['sub_account']['account_no'],
                'bank_name'      => $response['details']['sub_account']['bank_name'],
                'account_name'   => $response['details']['sub_account']['account_name'] ?? 'N/A',
                'status'         => true,
                'provider'       => 'redbiller',
                'meta'           => json_encode($response['details']),
                'reference'      => $response['details']['sub_account']['reference'],
            ]);

            return $this->sendResponse([
                'account_number' => $createdVBA->account_number,
                'bank_name'      => $createdVBA->bank_name,
                'account_name'   => $createdVBA->account_name,
                'currency'       => $createdVBA->currency,
                'order_ref'      => $createdVBA->order_ref,
            ],
                $response['message']
            );

        } else {
            return response()->json($response, 400);
        }

    }

    //fetch all vbas with the Redbiller api
    public function fetchVBA(Request $request)
    {
        $accounts = $request->user()->virtualBankAccounts;

        return response()->json($accounts, 200);

        $params = [
            'bank' => 'Kuda',
            // 'reference' => '84918140214202exlrma2lbjqcemb12ju7',
            // 'account_no' => 'your_account_no',
            // 'blacklisted' => 'false',
            // 'channel' => 'API',
            // 'start_date' => '2023-01-01',
            // 'end_date' => '2023-12-31',
            // 'page' => 1,
            // 'limit' => 100,
        ];

        $myVBAs = $this->redbiller->getAllVirtualAccount($params);

        if ($myVBAs->status === "true" && $myVBAs->response === 200) {

            return $this->sendResponse($myVBAs->details,
                $myVBAs->message
            );

        } else {
            return response()->json($myVBAs->message, 400);
        }

    }

    public function getBanks(Request $request)
    {
        $redbillerService = new RedbillerService();
        $responseArray    = $redbillerService->getBankList();
        $response         = json_decode(json_encode($responseArray), false);

        if ($response->status === "true" && $response->response === 200) {
            return $this->sendResponse($response->details, 'Banks retrieved successfully.');
        } else {
            return $this->sendError($response->message);
        }

    }
}