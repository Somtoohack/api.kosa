<?php

declare (strict_types = 1);

namespace App\Http\Controllers\User\Auth;

use App\Constants\ErrorCodes;
use App\Http\Controllers\Controller;
use App\Mail\AccountVerified;
use App\Mail\VerifyEmail;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class VerifyEmailController extends Controller
{
    use HttpResponses;

    public function sendVerificationEmail(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user->email_verified_at !== null) {
                return $this->sendMessage('Email is already verified', 2000);
            }

            // Check if the last verification code was sent
            $check = $user->email_verification_code_sent_at;
            if ($user->email_verification_code_sent_at) {
                $lastVerificationCodeSentAt = Carbon::parse($check);
                if ($lastVerificationCodeSentAt->diffInSeconds(now()) < 60) {
                    return $this->sendMessage('You can only request a new verification code every 60 seconds', 4000);
                }
            }

            // Generate and send a new verification code
            $verificationCode = rand(100000, 999999); // generate a random 6-digit code
            $user->email_verification_code = $verificationCode;
            $user->email_verification_code_sent_at = now(); // Update the timestamp
            $user->save(); // Ensure the user is saved with the new timestamp

            // Send email with verification code
            Mail::to($user->email)->queue(new VerifyEmail($user, $verificationCode));

            return $this->sendMessage('Verification code sent to your email', 2000);
        } catch (\Exception $e) {
            Log::error('Error sending verification email: ' . $e->getMessage());
            return $this->sendMessage('Error sending verification email', 5000);
        }
    }

    public function verifyEmail(Request $request)
    {

        //Validate
        $request->validate([
            'code' => 'required|numeric|digits:6',
        ]);
        try {
            $user = Auth::user();
            $code = $request->input('code');
            $data = $user->email_verified_at;

            if ($data !== null) {
                return $this->sendError('Email is already verified', [], ErrorCodes::REQUEST_DENIED);
            }

            $check = $user->email_verification_code_sent_at;
            if ($user->email_verification_code_sent_at) {
                $lastVerificationCodeSentAt = Carbon::parse($check);
                if ($lastVerificationCodeSentAt && $lastVerificationCodeSentAt->diffInMinutes(now()) > 10) {
                    return $this->sendError('Verification code has expired, please request a new one', [], ErrorCodes::REQUEST_DENIED);
                }
            }

            if ($code === $user->email_verification_code) {
                $user->email_verified_at = now();
                $user->save();

                // Send email notification upon successful verification
                Mail::to($user)->queue(new AccountVerified($user)); // {{ edit_1 }}

                return $this->sendMessage('Email verified successfully', 2000);
            } else {
                return $this->sendError('Invalid verification code', [], 4022);
            }
        } catch (\Exception $e) {
            Log::error('Error verifying email: ' . $e->getMessage());
            return $this->sendError('Error verifying email', [], 5000);
        }
    }
}
