<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PredictionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/prediksi', [PredictionController::class, 'index'])->name('predictions.index');
    Route::post('/prediksi', [PredictionController::class, 'predict'])->name('predictions.predict');
    Route::post('/prediksi/bandingkan', [PredictionController::class, 'compare'])->name('predictions.compare');
    Route::get('/prediksi/{history}/print', [PredictionController::class, 'print'])->name('predictions.print');

    Route::get('/mahasiswa/export', [MahasiswaController::class, 'export'])->name('mahasiswa.export');
    Route::post('/mahasiswa/import', [MahasiswaController::class, 'import'])->name('mahasiswa.import');
    Route::resource('mahasiswa', MahasiswaController::class)->except(['show']);

    Route::get('/evaluasi', [EvaluationController::class, 'index'])->name('evaluation.index');
});
