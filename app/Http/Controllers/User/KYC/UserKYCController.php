<?php
namespace App\Http\Controllers\User\KYC;

use App\Constants\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Services\KosabaseMicroservices\RedbillerService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserKYCController extends Controller
{

    protected $redbiller;

    public function __construct(RedbillerService $redbiller)
    {

        $this->redbiller = $redbiller;

    }

    public function validateBVN(Request $request)
    {
        $request->validate([
            'bvn' => 'required|string|max:11',
        ]);

        try {
            $user = Auth::user();

            if (! $user) {
                return $this->sendError('Invalid session'[], ErrorCodes::TRY_AGAIN);
            }
            if (! $user->profile) {
                return $this->sendError('Please update your profile', [], ErrorCodes::TRY_AGAIN);
            }

            $userKYC = $user->kyc()->first();
            if ($userKYC && $userKYC->bvn_validated) {
                return $this->sendError('BVN already verified', [], ErrorCodes::TRY_AGAIN);
            }
            $bvn       = $request->input('bvn');
            $reference = getTrx();
            // Call the BVN validation service
            $payload = [
                'bvn'       => $bvn,
                'reference' => $reference,
            ];

            $response = $this->redbiller->verifyBVN($payload);
            if ($response && $response['status'] == 'true') {

                Log::info($response['details']);

                $userKYC = $user->kyc()->first();

                $payload = json_encode($response['details']);
                if (($response['details']['first_name'] == $user->profile->first_name) ||
                    ($response['details']['surname'] == $user->profile->last_name)) {
                    Log::info(['Verification failed, Details do not match user profile' => $response['details']]);
                    return $this->sendError('Verification failed, details do not match user profile', [], ErrorCodes::TRY_AGAIN);
                }
                if ($userKYC) {

                    $userKYC->update([
                        'bvn'           => $bvn,
                        'bvn_payload'   => $payload,
                        'bvn_validated' => true,
                    ]);
                } else {
                    $user->kyc()->create([
                        'bvn'           => $bvn,
                        'bvn_payload'   => $payload,
                        'bvn_validated' => true,
                    ]);
                }
                return $this->sendResponse('BVN verification successful');
            } else {
                Log::error(['Error' => $response]);
                $message = $response['message'];
                if ($message == 'This resource was not found') {
                    $message = 'Request denied';
                }
                return $this->sendError($message, [], ErrorCodes::TRY_AGAIN);
            }
        } catch (Exception $e) {
            Log::error(['Error' => $e->getMessage()]);
            return $this->sendError('An error occurred while validating the BVN', [], ErrorCodes::TRY_AGAIN);
        }

    }

    public function validateNIN(Request $request)
    {
        $request->validate([
            'nin' => 'required|string|max:11',
        ]);

        try {
            $user = Auth::user();

            if (! $user) {
                return $this->sendError('Invalid session'[], ErrorCodes::TRY_AGAIN);
            }
            if (! $user->profile) {
                return $this->sendError('Please update your profile', [], ErrorCodes::TRY_AGAIN);
            }
            $userKYC = $user->kyc()->first();
            if ($userKYC && $userKYC->nin_validated) {
                return $this->sendError('NIN already verified', [], ErrorCodes::TRY_AGAIN);
            }
            $nin       = $request->input('nin');
            $reference = getTrx();

            $payload = [
                'nin'       => $nin,
                'reference' => $reference,
            ];

            // Call the NIN validation service
            $response = $this->redbiller->verifyNIN($payload);
            if ($response && $response['status'] == 'true') {

                Log::info($response['details']);

                $userKYC = $user->kyc()->first();

                $nin_payload = json_encode($response['details']);
                if (($response['details']['first_name'] == $user->profile->first_name) ||
                    ($response['details']['surname'] == $user->profile->last_name)) {
                    Log::info(['Verification failed, Details do not match user profile' => $response['details']]);
                    return $this->sendError('Verification failed, details do not match user profile', [], ErrorCodes::TRY_AGAIN);
                }
                if ($userKYC) {

                    $userKYC->update([
                        'nin'           => $nin,
                        'nin_payload'   => $nin_payload,
                        'nin_validated' => true,
                    ]);
                } else {
                    $user->kyc()->create([
                        'nin'           => $nin,
                        'nin_payload'   => $nin_payload,
                        'nin_validated' => true,
                    ]);
                }
                return $this->sendResponse('NIN verification successful');
            } else {
                Log::error(['Error' => $response]);
                $message = $response['message'];
                if ($message == 'This resource was not found') {
                    $message = 'Request denied';
                }
                return $this->sendError($message, [], ErrorCodes::TRY_AGAIN);
            }
        } catch (Exception $e) {
            Log::error(['Error' => $e->getMessage()]);
            return $this->sendError('An error occurred while validating the NIN', [], ErrorCodes::TRY_AGAIN);
        }

    }

}
