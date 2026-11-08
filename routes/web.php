<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Kelola\LaporanController as KelolaLaporanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard: warga melihat riwayat laporannya; admin/eksekutif diarahkan ke monitoring.
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Warga
Route::middleware(['auth', 'role:warga'])->group(function () {
    Route::get('/lapor', [LaporanController::class, 'create'])->name('laporan.create');
    Route::post('/lapor', [LaporanController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');
    Route::delete('/laporan/{id}', [LaporanController::class, 'destroy'])->name('laporan.destroy');
});

// Admin & eksekutif: monitoring dan daftar laporan (hanya lihat)
Route::middleware(['auth', 'role:admin,eksekutif'])->group(function () {
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/kelola/laporan', [KelolaLaporanController::class, 'index'])->name('kelola.laporan.index');
    Route::get('/kelola/laporan/{laporan}', [KelolaLaporanController::class, 'show'])->name('kelola.laporan.show');
});

// Admin saja: memberi tanggapan dan mengubah status
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/kelola/laporan/{laporan}/tanggapan', [KelolaLaporanController::class, 'tanggapi'])
        ->name('kelola.laporan.tanggapi');
});

require __DIR__.'/auth.php';
