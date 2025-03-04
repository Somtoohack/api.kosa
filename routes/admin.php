<?php

use App\Http\Controllers\Admin\AdminAuthController;
use Illuminate\Http\Request;

Route::group(['prefix' => 'admin'], function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/register', [AdminAuthController::class, 'register']);

    Route::get('/', function (Request $request) {
        return response()->json([
            'admin' => $request->user('admin'),
            'message' => 'Welcome Admin',
        ]);
    })->middleware('auth:admin');
});