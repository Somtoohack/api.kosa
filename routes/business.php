<?php

use App\Http\Controllers\Business\BusinessAuthController;
use Illuminate\Http\Request;

Route::group(['prefix' => 'business'], function () {
    Route::post('/login', [BusinessAuthController::class, 'login']);
    Route::post('/register', [BusinessAuthController::class, 'register']);

    Route::get('/', function (Request $request) {
        return response()->json([
            'business' => $request->user('business'),
            'message' => 'Welcome Business',
        ]);
    })->middleware('auth:business');
});