<?php

use App\Http\Controllers\Events\EventCategoryController;

Route::group(['prefix' => 'event'], function () {
    Route::prefix('categories')->group(function () {
        Route::get('/list', [EventCategoryController::class, 'index']);
        Route::post('/create', [EventCategoryController::class, 'store']);
        Route::get('/get/{slug}', [EventCategoryController::class, 'show']);
        Route::post('/update/{slug}', [EventCategoryController::class, 'update']);
        Route::delete('/delete/{slug}', [EventCategoryController::class, 'destroy']);
    });
});
