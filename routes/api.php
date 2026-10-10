<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\ProfileController;

// Route Public (Tidak perlu token)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/kelas', [AuthController::class, 'getKelas']);

// Route Protected (Wajib mengirimkan Bearer Token dari hasil Login)
Route::middleware('auth:sanctum')->group(function () {

    // Endpoint Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/home', [\App\Http\Controllers\Api\HomeController::class, 'index']);

    Route::get('/modules', [ModuleController::class, 'index']);
    Route::get('/modules/{id}', [ModuleController::class, 'show']);


    Route::post('/materi/{id}/complete', [ModuleController::class, 'completeMateri']);
    Route::post('/modules/{moduleId}/submit-kuis', [ModuleController::class, 'submitKuis']);
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::get('/progress', [ProfileController::class, 'getProgress']);
    Route::get('/user/profile', [ProfileController::class, 'getProfile']);
    Route::post('/user/profile/update', [ProfileController::class, 'updateProfile']);
    Route::post('/user/password/update', [ProfileController::class, 'updatePassword']);
    Route::post('/user/profile/avatar', [ProfileController::class, 'updateAvatar']);
});
