<?php

use App\Http\Controllers\JadwalController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Jadwal management page
    Route::get('kelola-jadwal', [JadwalController::class, 'manage'])->name('jadwal.manage');
    Route::post('kelola-jadwal', [JadwalController::class, 'upsert'])->name('jadwal.upsert');
    Route::delete('jadwal/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');

    // Jadwal API routes
    Route::apiResource('jadwal', JadwalController::class)->except(['destroy']);

    // Device Control management page
    Route::get('kelola-device', [\App\Http\Controllers\DeviceControlController::class, 'manage'])->name('device.manage');
    Route::post('kelola-device', [\App\Http\Controllers\DeviceControlController::class, 'upsert'])->name('device.upsert');
    Route::delete('device-control/{deviceControl}', [\App\Http\Controllers\DeviceControlController::class, 'destroy'])->name('device.destroy');

    // Device Control API routes
    Route::apiResource('device-control', \App\Http\Controllers\DeviceControlController::class)->except(['destroy']);
    Route::post('device-control/{deviceId}/heartbeat', [\App\Http\Controllers\DeviceControlController::class, 'heartbeat'])->name('device.heartbeat');

    // Quick toggle for manual device
    Route::post('device-control/{deviceControl}/toggle', [\App\Http\Controllers\DeviceControlController::class, 'toggleManual'])->name('device.toggle');
});

require __DIR__.'/settings.php';
