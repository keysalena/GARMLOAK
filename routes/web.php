<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware([EnsureAdmin::class])->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('kelas', [AdminDashboardController::class, 'kelasIndex'])->name('kelas.index');
        Route::get('kelas/create', [AdminDashboardController::class, 'createKelas'])->name('kelas.create');
        Route::get('kelas/{kelas}/edit', [AdminDashboardController::class, 'editKelas'])->name('kelas.edit');
        Route::post('kelas', [AdminDashboardController::class, 'storeKelas'])->name('kelas.store');
        Route::put('kelas/{kelas}', [AdminDashboardController::class, 'updateKelas'])->name('kelas.update');
        Route::delete('kelas/{kelas}', [AdminDashboardController::class, 'destroyKelas'])->name('kelas.destroy');

        Route::get('mahasiswa', [AdminDashboardController::class, 'mahasiswaIndex'])->name('mahasiswa.index');
        Route::get('mahasiswa/create', [AdminDashboardController::class, 'createMahasiswa'])->name('mahasiswa.create');
        Route::get('mahasiswa/{user}/edit', [AdminDashboardController::class, 'editMahasiswa'])->name('mahasiswa.edit');
        Route::post('mahasiswa', [AdminDashboardController::class, 'storeMahasiswa'])->name('mahasiswa.store');
        Route::put('mahasiswa/{user}', [AdminDashboardController::class, 'updateMahasiswa'])->name('mahasiswa.update');
        Route::delete('mahasiswa/{user}', [AdminDashboardController::class, 'destroyMahasiswa'])->name('mahasiswa.destroy');

        Route::get('modul', [AdminDashboardController::class, 'modulIndex'])->name('modul.index');
        Route::get('modul/create', [AdminDashboardController::class, 'createModul'])->name('modul.create');
        Route::get('modul/{module}/edit', [AdminDashboardController::class, 'editModul'])->name('modul.edit');
        Route::post('modul', [AdminDashboardController::class, 'storeModul'])->name('modul.store');
        Route::put('modul/{module}', [AdminDashboardController::class, 'updateModul'])->name('modul.update');
        Route::delete('modul/{module}', [AdminDashboardController::class, 'destroyModul'])->name('modul.destroy');

        Route::get('materi', [AdminDashboardController::class, 'materiIndex'])->name('materi.index');
        Route::get('materi/create', [AdminDashboardController::class, 'createMateri'])->name('materi.create');
        Route::get('materi/{materi}/edit', [AdminDashboardController::class, 'editMateri'])->name('materi.edit');
        Route::post('materi', [AdminDashboardController::class, 'storeMateri'])->name('materi.store');
        Route::put('materi/{materi}', [AdminDashboardController::class, 'updateMateri'])->name('materi.update');
        Route::delete('materi/{materi}', [AdminDashboardController::class, 'destroyMateri'])->name('materi.destroy');

        Route::get('ar-content', [AdminDashboardController::class, 'arContentIndex'])->name('ar-content.index');
        Route::get('ar-content/create', [AdminDashboardController::class, 'createArContent'])->name('ar-content.create');
        Route::get('ar-content/{arContent}/edit', [AdminDashboardController::class, 'editArContent'])->name('ar-content.edit');
        Route::post('ar-content', [AdminDashboardController::class, 'storeArContent'])->name('ar-content.store');
        Route::put('ar-content/{arContent}', [AdminDashboardController::class, 'updateArContent'])->name('ar-content.update');
        Route::delete('ar-content/{arContent}', [AdminDashboardController::class, 'destroyArContent'])->name('ar-content.destroy');

        Route::get('challenge', [AdminDashboardController::class, 'challengeIndex'])->name('challenge.index');
        Route::get('challenge/create', [AdminDashboardController::class, 'createChallenge'])->name('challenge.create');
        Route::get('challenge/{challenge}/edit', [AdminDashboardController::class, 'editChallenge'])->name('challenge.edit');
        Route::post('challenge', [AdminDashboardController::class, 'storeChallenge'])->name('challenge.store');
        Route::put('challenge/{challenge}', [AdminDashboardController::class, 'updateChallenge'])->name('challenge.update');
        Route::delete('challenge/{challenge}', [AdminDashboardController::class, 'destroyChallenge'])->name('challenge.destroy');

        Route::get('questions', [AdminDashboardController::class, 'questionsIndex'])->name('questions.index');
        Route::get('questions/create', [AdminDashboardController::class, 'createQuestion'])->name('questions.create');
        Route::get('questions/{question}/edit', [AdminDashboardController::class, 'editQuestion'])->name('questions.edit');
        Route::post('questions', [AdminDashboardController::class, 'storeQuestion'])->name('questions.store');
        Route::put('questions/{question}', [AdminDashboardController::class, 'updateQuestion'])->name('questions.update');
        Route::delete('questions/{question}', [AdminDashboardController::class, 'destroyQuestion'])->name('questions.destroy');
    });
});
