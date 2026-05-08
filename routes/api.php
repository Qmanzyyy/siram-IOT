<?php

use App\Http\Controllers\DeviceControlController;
use App\Http\Controllers\JadwalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes for ESP32
|--------------------------------------------------------------------------
|
| These routes are protected with API key authentication and rate limiting.
| ESP32 must send X-API-Key header with every request.
|
*/
 
Route::middleware(['api.key', 'throttle:esp'])->group(function () {
    // Get active schedule
    Route::get('/jadwal/active', [JadwalController::class, 'getActive']);

    // Get device control settings
    Route::get('/device/{deviceId}', [DeviceControlController::class, 'getByDeviceId']);

    // Update heartbeat
    Route::post('/device/{deviceId}/heartbeat', [DeviceControlController::class, 'heartbeat']);

    // Update device status (for manual mode feedback)
    Route::post('/device/{deviceId}/status', [DeviceControlController::class, 'updateStatus']);
});
