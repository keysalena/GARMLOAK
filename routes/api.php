<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Http\Request;

// ---------------------------------------------------------------
// Public (tanpa token). Throttle melindungi login dari brute force.
// ---------------------------------------------------------------
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::get('/kelas', [AuthController::class, 'getKelas']);

// ---------------------------------------------------------------
// Protected (wajib Bearer Token). Hanya endpoint yang dipakai Flutter.
// ---------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/home', [HomeController::class, 'index']);

    Route::get('/modules', [ModuleController::class, 'index']);
    Route::get('/modules/{id}', [ModuleController::class, 'show']);
    Route::post('/materi/{id}/complete', [ModuleController::class, 'completeMateri']);
    Route::post('/modules/{moduleId}/submit-kuis', [ModuleController::class, 'submitKuis']);

    Route::get('/user/profile', function (Request $request) {
        return response()->json(['success' => true, 'data' => $request->user()]);
    });
    Route::post('/user/profile/update', [ProfileController::class, 'updateProfile']);
    Route::post('/user/password/update', [ProfileController::class, 'updatePassword']);
    Route::post('/user/profile/avatar', [ProfileController::class, 'updateAvatar']);
});
