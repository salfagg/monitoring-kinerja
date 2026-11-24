<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogHarianController;
use App\Http\Controllers\PekerjaanController;
use App\Http\Controllers\PokjaController;
use App\Http\Controllers\RekapPenugasanController;
use App\Http\Controllers\SkpController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - TANJIDOR BGTK DKI Jakarta
|--------------------------------------------------------------------------
| Sistem Tata Kelola Kinerja Digital dan Monitoring
*/

// Root Redirect
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// =========================================================================
// 1. MODUL AUTENTIKASI (GUEST)
// =========================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// =========================================================================
// 2. MODUL UTAMA APLIKASI (AUTHENTICATED)
// =========================================================================
Route::middleware('auth')->group(function () {
    // Sesi Pengguna
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard & Monitoring Terpadu
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
        Route::get('/monitoring-balai', 'commandCenter')->name('monitoring.balai');
    });

    // ---------------------------------------------------------------------
    // A. Modul Penugasan & Pekerjaan (PekerjaanController)
    // ---------------------------------------------------------------------
    Route::controller(PekerjaanController::class)->group(function () {
        Route::get('pekerjaan-saya', 'pekerjaanSaya')->name('pekerjaan.saya');
        Route::get('persetujuan', 'persetujuanIndex')->name('persetujuan.index');
        Route::post('pekerjaan/{pekerjaan}/progres', 'updateProgres')->name('pekerjaan.update-progres');
        Route::post('realisasi/{realisasi}/approve', 'approveRealisasi')->name('realisasi.approve');
        Route::post('realisasi/{realisasi}/reject', 'rejectRealisasi')->name('realisasi.reject');
        Route::post('pekerjaan/{pekerjaan}/review-rincian', 'reviewRincian')->name('pekerjaan.review-rincian');
        Route::post('pekerjaan/{pekerjaan}/review-hasil', 'reviewHasil')->name('pekerjaan.review-hasil');
    });
    Route::resource('pekerjaan', PekerjaanController::class);

    // ---------------------------------------------------------------------
    // B. Modul Rekapitulasi Beban Penugasan & Surat Tugas (ST)
    // ---------------------------------------------------------------------
    Route::controller(RekapPenugasanController::class)->prefix('rekap-penugasan')->name('rekap-penugasan.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/export-excel', 'exportExcel')->name('export-excel');
    });

    // ---------------------------------------------------------------------
    // C. Modul Sasaran Kinerja Pegawai (SKP Tahunan / RHK)
    // ---------------------------------------------------------------------
    Route::post('skp/{skp}/approve', [SkpController::class, 'approve'])->name('skp.approve');
    Route::resource('skp', SkpController::class);

    // ---------------------------------------------------------------------
    // D. Modul Log Harian & Rekapitulasi Kinerja Bulanan
    // ---------------------------------------------------------------------
    Route::controller(LogHarianController::class)->prefix('log-harian')->name('log-harian.')->group(function () {
        Route::get('/rekap', 'rekap')->name('rekap');
        Route::get('/export-csv', 'exportCsv')->name('export-csv');
        Route::post('/ajukan-bulan', 'ajukanBulan')->name('ajukan-bulan');
        Route::post('/{logHarian}/ajukan', 'ajukan')->name('ajukan');
        Route::post('/{logHarian}/approve', 'approve')->name('approve');
    });
    Route::resource('log-harian', LogHarianController::class);

    // ---------------------------------------------------------------------
    // E. Modul Master Data (Super Admin Protected)
    // ---------------------------------------------------------------------
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('pokjas/{pokja}/sync-anggota', [PokjaController::class, 'syncAnggota'])->name('pokjas.sync-anggota');
        Route::resource('pokjas', PokjaController::class);
    });
});
