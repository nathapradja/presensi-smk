<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;

Route::prefix('wali-kelas')->group(function () {
    Route::post('/verify-pin', [AbsensiController::class, 'verifyPin']);
});