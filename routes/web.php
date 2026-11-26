<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/home', fn () => redirect()->route('dashboard'))->name('home');

Route::get('/barangs/{barang}/qr', [BarangController::class, 'qr'])->name('barangs.qr');
Route::resource('barangs', BarangController::class);

Route::get('/riwayat', [ActivityLogController::class, 'index'])->name('riwayat.index');

Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
Route::get('/laporan/export/pdf', [ReportController::class, 'exportPdf'])->name('laporan.export.pdf');
Route::get('/laporan/export/excel', [ReportController::class, 'exportExcel'])->name('laporan.export.excel');

Route::get('/pengaturan', [SettingController::class, 'index'])->name('pengaturan.index');
Route::put('/pengaturan/profil', [SettingController::class, 'updateProfile'])->name('pengaturan.profile.update');
Route::put('/pengaturan/aplikasi', [SettingController::class, 'updateApp'])->name('pengaturan.app.update');
