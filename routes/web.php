<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogHarianController;
use App\Http\Controllers\PekerjaanController;
use App\Http\Controllers\PokjaController;
use App\Http\Controllers\SkpController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Modul Autentikasi (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitoring-balai', [DashboardController::class, 'commandCenter'])->name('monitoring.balai');

    // FONDASI UTAMA: Modul Hierarki Pekerjaan (PRD BGTK)
    Route::get('pekerjaan-saya', [PekerjaanController::class, 'pekerjaanSaya'])->name('pekerjaan.saya');
    Route::get('persetujuan', [PekerjaanController::class, 'persetujuanIndex'])->name('persetujuan.index');
    Route::resource('pekerjaan', PekerjaanController::class);
    Route::post('pekerjaan/{pekerjaan}/progres', [PekerjaanController::class, 'updateProgres'])->name('pekerjaan.update-progres');
    Route::post('realisasi/{realisasi}/approve', [PekerjaanController::class, 'approveRealisasi'])->name('realisasi.approve');
    Route::post('realisasi/{realisasi}/reject', [PekerjaanController::class, 'rejectRealisasi'])->name('realisasi.reject');
    Route::post('pekerjaan/{pekerjaan}/review-rincian', [PekerjaanController::class, 'reviewRincian'])->name('pekerjaan.review-rincian');
    Route::post('pekerjaan/{pekerjaan}/review-hasil', [PekerjaanController::class, 'reviewHasil'])->name('pekerjaan.review-hasil');

    // Modul Sasaran Kinerja Pegawai (SKP Tahunan / RHK)
    Route::post('skp/{skp}/approve', [SkpController::class, 'approve'])->name('skp.approve');
    Route::resource('skp', SkpController::class);

    // Modul Log Harian & Rekap Kinerja Bulanan
    Route::get('log-harian/rekap', [LogHarianController::class, 'rekap'])->name('log-harian.rekap');
    Route::get('log-harian/export-csv', [LogHarianController::class, 'exportCsv'])->name('log-harian.export-csv');
    Route::post('log-harian/ajukan-bulan', [LogHarianController::class, 'ajukanBulan'])->name('log-harian.ajukan-bulan');
    Route::post('log-harian/{logHarian}/ajukan', [LogHarianController::class, 'ajukan'])->name('log-harian.ajukan');
    Route::post('log-harian/{logHarian}/approve', [LogHarianController::class, 'approve'])->name('log-harian.approve');
    Route::resource('log-harian', LogHarianController::class);

    // Modul Pengguna & Pokja (Admin Protected)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('pokjas/{pokja}/sync-anggota', [PokjaController::class, 'syncAnggota'])->name('pokjas.sync-anggota');
        Route::resource('pokjas', PokjaController::class);
    });
});
