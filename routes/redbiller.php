<?php
use App\Http\Controllers\User\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('redbiller')->group(function () {
    Route::prefix('webhook')->group(function () {
        Route::post('/deposit/receive', [WebhookController::class, 'handleDepositWebhook']);
        Route::post('/deposit/verify', [WebhookController::class, 'verifyDepositWebhook']);

    });
});
