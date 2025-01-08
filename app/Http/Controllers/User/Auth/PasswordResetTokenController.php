<?php

declare (strict_types = 1);

namespace App\Http\Controllers\User\Auth;

use App\Constants\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Mail\PasswordResetConfirmationMail;
use App\Mail\PasswordResetTokenMail;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

final class PasswordResetTokenController extends Controller
{
    use HttpResponses;

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function sendResetToken(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        // Clear existing tokens and validate user existence in one query
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            throw ValidationException::withMessages(['email' => 'No user found with this email address.']);
        }

        // Clear all existing tokens for the user
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Generate and save a new token
        $tokenData = $this->generateSixDigitToken();
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($tokenData),
            'created_at' => now(),
            'used' => false,
        ]);

        // Send the token to the user's email
        Mail::to($user)->queue(new PasswordResetTokenMail($user, $tokenData));

        return $this->sendMessage(SuccessMessages::PASSWORD_RESET_TOKEN_SENT);
    }

    /**
     * Reset the user's password using the provided token.
     *
     * @throws ValidationException
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        // Validate token and user existence in one query
        $tokenData = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$tokenData || (time() - strtotime($tokenData->created_at) > 600) || $tokenData->used || !Hash::check($request->token, $tokenData->token)) {
            throw ValidationException::withMessages(['token' => 'Invalid or expired token.']);
        }

        // Check if the new password has been used before
        if ($this->isPasswordUsed($request->email, $request->password)) {
            throw ValidationException::withMessages(['password' => 'You have used this password before.']);
        }

        // Mark the token as used and update the user's password
        DB::transaction(function () use ($request, $tokenData) {
            DB::table('password_reset_tokens')->where('email', $request->email)->update(['used' => true]);
            $user = User::where('email', $request->email)->first();
            $user->update(['password' => bcrypt($request->password)]);
            $this->storePasswordInHistory($user->id, $request->password);
            Mail::to($user)->queue(new PasswordResetConfirmationMail($user));
        });

        return $this->sendMessage(SuccessMessages::PASSWORD_CHANGED);
    }

    private function generateSixDigitToken(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function isPasswordUsed(string $email, string $newPassword): bool
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return false;
        }

        // Check the password history for the user
        $usedPasswords = DB::table('password_history')->where('user_id', $user->id)->pluck('password')->toArray();
        foreach ($usedPasswords as $usedPassword) {
            if (Hash::check($newPassword, $usedPassword)) {
                return true;
            }
        }

        return false;
    }

    private function storePasswordInHistory(int $userId, string $password): void
    {
        DB::table('password_history')->insert([
            'user_id' => $userId,
            'password' => Hash::make($password),
            'created_at' => now(),
        ]);
    }
}