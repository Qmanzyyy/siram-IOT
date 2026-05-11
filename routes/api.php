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

// All API routes protected with API key
Route::middleware(['api.key', 'throttle:esp'])->group(function () {
    // Jadwal API routes (specific routes first, then resource routes)
    Route::get('/jadwal/active', [JadwalController::class, 'getActive']);
    Route::apiResource('jadwal', JadwalController::class)->except(['destroy']);

    // Device Control API routes (specific routes first, then resource routes)
    Route::get('/device/{deviceId}', [DeviceControlController::class, 'getByDeviceId']);
    Route::post('/device/{deviceId}/heartbeat', [DeviceControlController::class, 'heartbeat']);
    Route::post('/device/{deviceId}/status', [DeviceControlController::class, 'updateStatus']);
    Route::apiResource('device-control', DeviceControlController::class)->except(['destroy']);
});
