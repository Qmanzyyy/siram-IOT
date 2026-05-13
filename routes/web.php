<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceControlController;
use App\Http\Controllers\JadwalController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Jadwal management page
    Route::get('kelola-jadwal', [JadwalController::class, 'manage'])->name('jadwal.manage');
    Route::post('kelola-jadwal', [JadwalController::class, 'upsert'])->name('jadwal.upsert');
    Route::get('jadwal/render-flow', [JadwalController::class, 'renderFlow'])->name('jadwal.renderFlow');
    Route::get('jadwal/{jadwal}', [JadwalController::class, 'show'])->name('jadwal.show');
    Route::post('jadwal/{jadwal}/run-now', [JadwalController::class, 'toggleRunNow'])->name('jadwal.runNow');
    Route::delete('jadwal/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');

    // Device Control management page
    Route::get('kelola-device', [DeviceControlController::class, 'manage'])->name('device.manage');
    Route::put('device/{deviceControl}', [DeviceControlController::class, 'update'])->name('device.update');
    Route::post('device/{deviceControl}/toggle', [DeviceControlController::class, 'toggleActive'])->name('device.toggle');
    Route::post('device/{deviceControl}/calibration/start', [DeviceControlController::class, 'startCalibration'])->name('device.calibration.start');
    Route::post('device/{deviceControl}/calibration/save', [DeviceControlController::class, 'saveCalibration'])->name('device.calibration.save');
});

require __DIR__.'/settings.php';
