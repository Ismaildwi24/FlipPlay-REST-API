<?php

use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - FlipPlay: The Vibe Grid (Modul 4)
|--------------------------------------------------------------------------
|
| Rute REST API untuk katalog media, manajemen profil pengguna,
| serta custom action relasi koleksi media (belongsToMany).
|
*/

// CRUD API Standar untuk Entitas Media (parameter diset 'media')
Route::apiResource('media', MediaController::class)->parameters(['media' => 'media']);

// CRUD API Standar untuk Entitas Pengguna (User)
Route::apiResource('users', UserController::class);

// Custom Action untuk Relasi Many-to-Many (Koleksi Pengguna)
Route::prefix('users/{user}/collection')->group(function () {
    Route::get('/', [UserController::class, 'getCollection']);
    Route::post('/', [UserController::class, 'addToCollection']);
    Route::delete('/{media}', [UserController::class, 'removeFromCollection']);
});
