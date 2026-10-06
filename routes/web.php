<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;

// ==========================================
// 1. GROUP WALI KELAS (Input Absensi)
// ==========================================
// Halaman Gerbang (Pilih Kelas & PIN)
Route::get('/', [AbsensiController::class, 'index'])->name('absen.login');

Route::get('/absen', [AbsensiController::class, 'index']);

// Proses Validasi PIN
Route::post('/verify-pin', [AbsensiController::class, 'verifyPinWeb'])->name('absen.verify');

// Halaman Daftar Siswa (Form Absen)
Route::get('/form-absen', [AbsensiController::class, 'formAbsen'])->name('absen.form');

// Proses Simpan ke Database
Route::post('/simpan-absen', [AbsensiController::class, 'storeWeb'])->name('absen.store');


// ==========================================
// 2. GROUP LAPORAN (Untuk Fase Dashboard Admin)
// ==========================================
Route::prefix('laporan')->group(function () {
    Route::get('/kelas/{kelas_id}/harian/{tanggal}', [ReportController::class, 'harianKelas']);
    Route::get('/siswa/{siswa_id}/bulanan/{bulan}/{tahun}', [ReportController::class, 'individuBulan']);
    Route::get('/sekolah/bulanan/{bulan}/{tahun}', [ReportController::class, 'sekolahBulan']);
});


// ==========================================
// 3. GROUP ADMIN (Manajemen Data Master)
// ==========================================
Route::prefix('admin')->group(function () {
    Route::get('/kelas', [AdminController::class, 'getKelas']);
    Route::put('/kelas/{id}/reset-pin', [AdminController::class, 'resetPinKelas']);
    Route::post('/libur', [AdminController::class, 'storeLibur']);
    Route::post('/kelas/store', [AdminController::class, 'storeKelas']);
});


// ==========================================
// 4. GROUP ADMIN (Login & CRUD)
// ==========================================
Route::prefix('admin')->group(function () {
    // Halaman Login
    Route::get('/login', [AdminController::class, 'loginForm'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'authenticate']);
    Route::get('/logout', [AdminController::class, 'logout']);

    // Dashboard & Manajemen (Hanya bisa diakses jika sudah login)
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::post('/kelas/{id}/reset-pin', [AdminController::class, 'resetPinKelas']);
    
    // TAMBAHAN FASE 2.5: Route Halaman Laporan Grafik
    Route::get('/laporan', [AdminController::class, 'halamanLaporan']);
});