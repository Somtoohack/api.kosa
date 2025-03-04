<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OtpService
{
    /**
     * Generate and send OTP to user
     *
     * @param User $user
     * @return bool
     */
    public function sendOtp(User $user)
    {
        // Generate a 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache for 10 minutes with user email as key
        Cache::put('otp_' . $user->email, $otp, now()->addMinutes(10));

        // Here you would typically send the OTP via email or SMS
        // For example:

        Mail::to($user->email)->queue(new OtpMail($user, $otp));

        return true;
    }

    /**
     * Verify OTP
     *
     * @param User $user
     * @param string $otp
     * @return bool
     */
    public function verifyOtp(User $user, string $otp)
    {
        $cachedOtp = Cache::get('otp_' . $user->email);

        if ($cachedOtp && $cachedOtp === $otp) {
            // Clear the OTP from cache
            Cache::forget('otp_' . $user->email);

            // Unlock the user's account
            $user->is_locked = false;
            $user->failed_attempts = 0;
            $user->save();

            return true;
        }

        return false;
    }
}