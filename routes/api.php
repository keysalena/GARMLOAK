<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\BadgeController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\QuestionAnswerController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Route Publik (Tidak butuh token)
Route::post('/login', [AuthController::class, 'login']);

// Route Terproteksi (Wajib kirim Bearer Token)
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/modules', [ModuleController::class, 'index']);
    Route::get('/modules/{module_id}', [ModuleController::class, 'show']);
    Route::get('/modules/{module_id}/ar', [ModuleController::class, 'arList']);
    Route::get('/modules/{module_id}/progress-summary', [ModuleController::class, 'progressSummary']);
    Route::get('/modules/{module_id}/challenges', [ModuleController::class, 'challengeList']);
    Route::get('/challenges/{challenge_id}/questions', [ModuleController::class, 'questionList']);
    Route::post('/questions/{question_id}/answer', [QuestionAnswerController::class, 'submit']);
    Route::get('/materi/{materi_id}', [ModuleController::class, 'detail']);
    Route::post('/materi/{materi_id}/complete', [ModuleController::class, 'complete']);
    Route::post('/ar/{ar_id}/complete', [ModuleController::class, 'completeAr']);
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::get('/user', [UserController::class, 'profile']);
    Route::get('/badges', [BadgeController::class, 'index']);
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);
    Route::get('/activity/summary', [ActivityController::class, 'summary']);
    Route::post('/activity/log-time', [ActivityController::class, 'logTime']);
    Route::get('/home', [HomeController::class, 'index']);
    /**
     * @group Autentikasi
     * @authenticated
     * 
     * Logout User
     * 
     * Menghapus token sesi saat ini agar tidak bisa digunakan lagi.
     */    Route::post('/logout', function (\Illuminate\Http\Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logout berhasil']);
    });
});
