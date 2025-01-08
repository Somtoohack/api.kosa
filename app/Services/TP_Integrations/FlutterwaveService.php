<?php

namespace App\Services\TP_Integrations;

use App\Models\User;
use App\Models\Wallet;
use Flutterwave\Service\VirtualAccount;
use Illuminate\Support\Facades\Log;

class FlutterwaveService
{

    public function createVbaService(User $user)
    {
        try {
            $service = new VirtualAccount();

            $payload = [
                "email" => $user->email,
                "bvn" => "22513739970",
                "amount" => 50,
                "firstname" => $user->profile->first_name,
                "lastname" => $user->profile->last_name,
                "narration" => $user->profile->full_name,
                "currency" => $user->wallet->currency,
                "is_permanent" => true,
                "tx_ref" => "TX_REF_" . $user->wallet->reference . '_' . time(),
            ];

            $response = $service->create($payload);

            Log::info(print_r($response, true));
            if ($response->status == "success") {
                return [
                    'success' => true,
                    'message' => 'Virtual Account created successfully',
                    'data' => $response->data,
                ];
            } else {
                [
                    'success' => false,
                    'message' => 'Failed to create Virtual Account',
                    'data' => null,
                ];
            }
        } catch (\Exception $e) {
            // Log the error or perform any other error handling
            \Log::error($e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while creating Virtual Account',
                'data' => null,
            ];
        }
    }

    public function getVBA($vbaId)
    {
        try {
            $service = new VirtualAccount();
            $response = $service->get($vbaId);

            if ($response->status == "success") {
                return [
                    'success' => true,
                    'message' => 'Virtual Bank Account retrieved successfully',
                    'data' => $response->data,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to retrieve Virtual Bank Account',
                    'data' => null,
                ];
            }
        } catch (\Exception $e) {
            // Log the error or perform any other error handling
            \Log::error($e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while retrieving Virtual Bank Account',
                'data' => null,
            ];
        }
    }
}