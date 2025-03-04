<?php
use App\Http\Controllers\User\WebhookController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Route;

Route::prefix('redbiller')->group(function () {
    Route::prefix('webhook')->group(function () {

        Route::get('inspire', function () {
            return response()->json(Inspiring::quote(), 200);

        });
        Route::post('/deposit/receive', [WebhookController::class, 'handleDepositWebhook']);
        Route::post('/deposit/verify', [WebhookController::class, 'verifyDepositWebhook']);

    });
});