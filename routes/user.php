<?php

use App\Http\Controllers\User\Auth\PasswordResetTokenController;
use App\Http\Controllers\User\Auth\UserAuthController;
use App\Http\Controllers\User\Auth\VerifyEmailController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\User\WalletController;

Route::group(['prefix' => 'user'], function () {
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/login/verify-otp', [UserAuthController::class, 'verifyOtp']);
    Route::post('/register', [UserAuthController::class, 'register']);

    Route::middleware(['auth:user'])->group(function () {
        Route::get('/session/verify', [UserAuthController::class, 'verifySession']
        );
        Route::post('/passcode/validate', [UserAuthController::class, 'revalidateSession']
        );

        Route::post('/email/verify-email', [VerifyEmailController::class, 'verifyEmail'])
            ->middleware(['throttle:6,1']);

        Route::post('/email/send-verification-mail', [VerifyEmailController::class, 'sendVerificationEmail'])
            ->middleware(['throttle:6,1']);

        Route::prefix('wallet')->group(function () {
            Route::get('/balance', [WalletController::class, 'getBalance']);
            Route::post('/deposit', [WalletController::class, 'deposit']);
            Route::post('/withdraw', [WalletController::class, 'withdraw']);
            Route::post('/transfer', [WalletController::class, 'transfer']);
            Route::post('/create-vba', [WalletController::class, 'createVba']);
            Route::post('/create-vba-redbiller', [WalletController::class, 'createVbaRedbiller']);
            Route::get('/get-vba', [WalletController::class, 'fetchVBA']);
            Route::post('/check-charges', [WalletController::class, 'checkCharges']);
            Route::get('/transactions', [WalletController::class, 'getTransactions']);
        });
        Route::prefix('profile')->group(function () {
            Route::post('/create', [UserProfileController::class, 'createProfile']);
            Route::post('/tag/create', [UserProfileController::class, 'createTag']);
        });
    });

    Route::post('/password/forgot', [PasswordResetTokenController::class, 'sendResetToken'])
        ->middleware('guest')
        ->name('password.email');

    Route::post('/password/set', [PasswordResetTokenController::class, 'resetPassword'])
        ->middleware('guest')
        ->name('password.store');

});