<?php

use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('media', MediaController::class)->parameters(['media' => 'media']);

Route::apiResource('users', UserController::class);

Route::prefix('users/{user}/collection')->group(function () {
    Route::get('/', [UserController::class, 'getCollection']);
    Route::post('/', [UserController::class, 'addToCollection']);
    Route::delete('/{media}', [UserController::class, 'removeFromCollection']);
});
