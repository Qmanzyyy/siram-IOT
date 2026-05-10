<?php

namespace App\Http\Controllers;

use App\Models\DeviceControl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceControlController extends Controller
{
    /**
     * Display a listing of device controls.
     */
    public function index(): JsonResponse
    {
        $devices = DeviceControl::orderBy('device_type')->get();

        return response()->json([
            'message' => 'Daftar device berhasil diambil',
            'data' => $devices,
        ]);
    }

    /**
     * Store a newly created device control.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'device_type' => 'required|string|max:255|unique:device_controls,device_type',
            'name' => 'required|string|max:255',
            'mode' => 'required|in:auto,manual',
            'speed' => 'nullable|integer|min:0|max:255',
            'servo_angle' => 'nullable|integer|min:0|max:180',
            'relay_state' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validatedData['last_heartbeat'] = now();

        $device = DeviceControl::create($validatedData);

        return response()->json([
            'message' => 'Device berhasil dibuat',
            'data' => $device,
        ], 201);
    }

    /**
     * Display the specified device control.
     */
    public function show(DeviceControl $deviceControl): JsonResponse
    {
        return response()->json([
            'message' => 'Detail device berhasil diambil',
            'data' => $deviceControl,
        ]);
    }

    /**
     * Update device configuration (for web form).
     */
    public function update(Request $request, DeviceControl $deviceControl)
    {
        $validated = $request->validate([
            'mode' => 'required|in:auto,manual',
            'speed' => 'nullable|integer|min:0|max:255',
            'servo_angle' => 'nullable|integer|min:0|max:180',
            'relay_state' => 'boolean',
            'calibration_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $deviceControl->update($validated);

        return redirect()->route('device.manage')->with('success', 'Konfigurasi device berhasil diperbarui!');
    }

    /**
     * Remove the specified device control.
     */
    public function destroy(DeviceControl $deviceControl)
    {
        $deviceId = $deviceControl->device_id;
        $deviceControl->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Device berhasil dihapus',
            ]);
        }

        return redirect()->route('device.manage')->with('success', "Device '{$deviceId}' berhasil dihapus!");
    }

    /**
     * Show the form for managing device controls.
     */
    public function manage()
    {
        $devices = DeviceControl::orderBy('device_type')->get();

        return view('device.manage', compact('devices'));
    }

    /**
     * Toggle device active status.
     */
    public function toggleActive(DeviceControl $deviceControl)
    {
        $deviceControl->update(['is_active' => ! $deviceControl->is_active]);

        $status = $deviceControl->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('device.manage')->with('success', "{$deviceControl->name} berhasil {$status}!");
    }

    /**
     * Start calibration for motor devices.
     */
    public function startCalibration(DeviceControl $deviceControl)
    {
        if (! $deviceControl->isMotor()) {
            return redirect()->route('device.manage')->with('error', 'Kalibrasi hanya untuk device motor!');
        }

        $deviceControl->update([
            'current_position' => 0,
            'calibration_percentage' => 0,
        ]);

        return redirect()->route('device.manage')->with('success', 'Kalibrasi dimulai. Jalankan device hingga titik maksimal.');
    }

    /**
     * Save calibration data.
     */
    public function saveCalibration(Request $request, DeviceControl $deviceControl)
    {
        if (! $deviceControl->isMotor()) {
            return redirect()->route('device.manage')->with('error', 'Kalibrasi hanya untuk device motor!');
        }

        $validated = $request->validate([
            'calibration_percentage' => 'required|numeric|min:0|max:100',
        ]);

        if ($validated['calibration_percentage'] > 0) {
            $maxSteps = (int) ($deviceControl->current_position / ($validated['calibration_percentage'] / 100));

            $deviceControl->update([
                'calibration_max_steps' => $maxSteps,
                'calibration_percentage' => $validated['calibration_percentage'],
            ]);

            return redirect()->route('device.manage')->with('success', "Kalibrasi berhasil! Max steps: {$maxSteps}");
        }

        return redirect()->route('device.manage')->with('error', 'Persentase harus lebih dari 0!');
    }

    /**
     * Get device by device_type (for ESP32).
     */
    public function getByDeviceId(string $deviceId): JsonResponse
    {
        $device = DeviceControl::where('device_type', $deviceId)->first();

        if (! $device) {
            return response()->json([
                'message' => 'Device not found',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'message' => 'Device retrieved successfully',
            'data' => $device,
        ]);
    }

    /**
     * Update device heartbeat (for ESP32).
     */
    public function heartbeat(string $deviceId): JsonResponse
    {
        $device = DeviceControl::where('device_type', $deviceId)->first();

        if (! $device) {
            return response()->json([
                'message' => 'Device not found',
            ], 404);
        }

        $device->update(['last_heartbeat' => now()]);

        return response()->json([
            'message' => 'Heartbeat updated',
            'data' => $device,
        ]);
    }

    /**
     * Update device status from ESP32 (for feedback).
     */
    public function updateStatus(Request $request, string $deviceId): JsonResponse
    {
        $device = DeviceControl::where('device_type', $deviceId)->first();

        if (! $device) {
            return response()->json([
                'message' => 'Device not found',
            ], 404);
        }

        $validated = $request->validate([
            'current_position' => 'nullable|integer|min:0',
            'relay_state' => 'nullable|boolean',
            'servo_angle' => 'nullable|integer|min:0|max:180',
            'is_active' => 'nullable|boolean',
        ]);

        $device->update(array_merge($validated, ['last_heartbeat' => now()]));

        return response()->json([
            'message' => 'Device status updated',
            'data' => $device,
        ]);
    }
}
