<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\ErrorCodes;
use App\Constants\ErrorMessages;
use App\Constants\SuccessMessages;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Http\Resources\UserProfileResource;
use App\Jobs\LogLoginJob;
use App\Jobs\UserRegisteredEmailJob;
use App\Models\User;
use App\Services\OtpService;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{

    use HttpResponses;

    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }
    public function login(LoginRequest $request)
    {

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->sendError(ErrorMessages::INVALID_USER, [], ErrorCodes::INVALID_USER);
            }

            if (!Hash::check($request->password, $user->password)) {
                // Increment failed login attempts
                $user->increment('failed_attempts');

                // Lock account if failed attempts reach 3
                if ($user->failed_attempts >= 3) {
                    $user->is_locked = true;
                    $user->save();

                    // return $this->sendError('Account locked due to multiple failed attempts. Please verify with OTP', [], ErrorCodes::ACCOUNT_LOCKED);

                }

                return $this->sendError(ErrorMessages::INCORRECT_PASSWORD, [], ErrorCodes::INCORRECT_PASSWORD);
            }

            // Reset failed attempts on successful login
            $user->failed_attempts = 0;
            $user->save();

            // Send OTP if the account was previously locked
            if ($user->is_locked) {
                $this->otpService->sendOtp($user);

                return $this->sendError('Account is locked. OTP sent for verification', [], ErrorCodes::ACCOUNT_LOCKED);
            }

            $token = $user->createToken('UserToken')->plainTextToken;

            $ip = $request->ip();
            if ($ip == '127.0.0.1') {
                $ip = '102.88.84.85';
            }
            dispatch(new LogLoginJob($user, $request->email, $request->device_id, $request->device_name, $ip, $request->header('User-Agent')));

            return $this->sendResponse(
                [
                    'user' => array_merge(
                        (new UserProfileResource($user))->toArray(request()),
                        ['token' => $token]
                    ),
                ],
                SuccessMessages::LOGIN_SUCCESSFUL
            );

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
                'auth_code' => 'required|array',
                'auth_code.otp' => 'required|string|size:6',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return $this->sendError(
                    'User not found',
                    [],
                    ErrorCodes::INVALID_USER
                );
            }

            // Verify password first
            if (!Hash::check($request->password, $user->password)) {
                return $this->sendError(
                    ErrorMessages::INCORRECT_PASSWORD,
                    [],
                    ErrorCodes::INCORRECT_PASSWORD
                );
            }

            // Then verify OTP
            if ($this->otpService->verifyOtp($user, $request->auth_code['otp'])) {
                // Reset the lock status and failed attempts on successful verification
                $user->is_locked = false;
                $user->failed_attempts = 0;
                $user->save();

                $token = $user->createToken('UserToken')->plainTextToken;

                return $this->sendResponse(
                    [
                        'user' => array_merge(
                            (new UserProfileResource($user))->toArray(request()),
                            ['token' => $token]
                        ),
                    ],
                    SuccessMessages::LOGIN_SUCCESSFUL
                );

            }

            return $this->sendError(
                'Invalid OTP',
                [],
                ErrorCodes::INVALID_REQUEST
            );

        } catch (\Throwable $th) {
            return $this->sendError(
                'OTP verification failed',
                ['error' => $th->getMessage()],
                ErrorCodes::TRY_AGAIN
            );
        }
    }

    public function verifySession(Request $request)
    {
        $user = Auth::user();

        return $this->sendResponse(
            [
                'user' => array_merge(
                    (new UserProfileResource($user))->toArray(request()),
                    ['token' => getValidatedToken($request)]
                ),
            ],
            'Session verified'
        );

    }

    /**
     * Revalidate user session with password or passcode
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function revalidateSession(Request $request)
    {
        try {

            $request->validate([
                'passcode' => 'required|string',
            ]);

            $user = Auth::user();

            if (!$user) {
                return $this->sendError(
                    'Unauthorized access',
                    [],
                    HttpResponses::ERROR_CODE_UNAUTHORIZED
                );
            }

            if (!Hash::check($request->passcode, $user->password)) {
                return $this->sendError(
                    'Incorrect passcode',
                    [],
                    ErrorCodes::INCORRECT_PASSWORD
                );
            }

            // Generate new token
            $token = $user->createToken('UserToken')->plainTextToken;

            return $this->sendResponse(
                [
                    'user' => array_merge(
                        (new UserProfileResource($user))->toArray(request()),
                        ['token' => $token]
                    ),
                ],
                SuccessMessages::LOGIN_SUCCESSFUL
            );

        } catch (\Throwable $th) {
            return $this->sendError(
                'Session revalidation failed',
                ['error' => $th->getMessage()],
                ErrorCodes::TRY_AGAIN
            );
        }
    }

    public function register(RegisterRequest $request)
    {
        try {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'country' => $request->country ?? 'NGA',
                'user_key' => getTrx(5) . '-' . now()->timestamp,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('UserToken')->plainTextToken;

            $ip = $request->ip();
            if ($ip == '127.0.0.1') {
                $ip = '102.88.84.85';
            }
            dispatch(new LogLoginJob($user, $request->email, $request->device_id, $request->device_name, $ip, $request->header('User-Agent')));

            dispatch(new UserRegisteredEmailJob($user));
            return $this->sendResponse(
                [
                    'user' => array_merge(
                        (new UserProfileResource($user))->toArray(request()),
                        ['token' => $token]
                    ),
                ],
                SuccessMessages::LOGIN_SUCCESSFUL
            );
        } catch (\Throwable $th) {

            return $this->sendError('Please try again shortly', [], ErrorCodes::TRY_AGAIN);
        }
    }
}