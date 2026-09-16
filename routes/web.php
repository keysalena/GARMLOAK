<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::prefix('/')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware([EnsureAdmin::class])->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('kelas', [AdminDashboardController::class, 'kelasIndex'])->name('kelas.index');
        Route::post('kelas', [AdminDashboardController::class, 'storeKelas'])->name('kelas.store');
        Route::put('kelas/{kelas}', [AdminDashboardController::class, 'updateKelas'])->name('kelas.update');
        Route::delete('kelas/{kelas}', [AdminDashboardController::class, 'destroyKelas'])->name('kelas.destroy');

        Route::get('admin/kelas/{kelas}/mahasiswa', [AdminDashboardController::class, 'mahasiswaIndex'])->name('kelas.mahasiswa');
        Route::post('mahasiswa', [AdminDashboardController::class, 'storeMahasiswa'])->name('mahasiswa.store');
        Route::put('mahasiswa/{user}', [AdminDashboardController::class, 'updateMahasiswa'])->name('mahasiswa.update');
        Route::delete('mahasiswa/{user}', [AdminDashboardController::class, 'destroyMahasiswa'])->name('mahasiswa.destroy');

        Route::get('modul', [AdminDashboardController::class, 'modulIndex'])->name('modul.index');
        Route::post('modul', [AdminDashboardController::class, 'storeModul'])->name('modul.store');
        Route::put('modul/{module}', [AdminDashboardController::class, 'updateModul'])->name('modul.update');
        Route::delete('modul/{module}', [AdminDashboardController::class, 'destroyModul'])->name('modul.destroy');
        
        Route::get('modul/{module}/materi', [AdminDashboardController::class, 'materiIndex'])->name('modul.materi');
        Route::post('materi', [AdminDashboardController::class, 'storeMateri'])->name('materi.store');
        Route::put('materi/{materi}', [AdminDashboardController::class, 'updateMateri'])->name('materi.update');
        Route::delete('materi/{materi}', [AdminDashboardController::class, 'destroyMateri'])->name('materi.destroy');

        Route::get('ar-content', [AdminDashboardController::class, 'arContentIndex'])->name('ar-content.index');
        Route::post('ar-content', [AdminDashboardController::class, 'storeArContent'])->name('ar-content.store');
        Route::put('ar-content/{arContent}', [AdminDashboardController::class, 'updateArContent'])->name('ar-content.update');
        Route::delete('ar-content/{arContent}', [AdminDashboardController::class, 'destroyArContent'])->name('ar-content.destroy');

        Route::get('module/{module}/questions', [AdminDashboardController::class, 'questionsIndex'])->name('modul.questions');
        Route::post('questions', [AdminDashboardController::class, 'storeQuestion'])->name('questions.store');
        Route::put('questions/{question}', [AdminDashboardController::class, 'updateQuestion'])->name('questions.update');
        Route::delete('questions/{question}', [AdminDashboardController::class, 'destroyQuestion'])->name('questions.destroy');

        Route::get('questions/{question}/options', [AdminDashboardController::class, 'optionsIndex'])->name('questions.options');
        Route::post('options', [AdminDashboardController::class, 'storeOption'])->name('options.store');
        Route::put('options/{option}', [AdminDashboardController::class, 'updateOption'])->name('options.update');
        Route::delete('options/{option}', [AdminDashboardController::class, 'destroyOption'])->name('options.destroy');
    });
});
