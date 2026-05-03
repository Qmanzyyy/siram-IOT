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
        $devices = DeviceControl::orderBy('device_id')->get();

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
            'device_id' => 'required|string|max:255|unique:device_controls,device_id',
            'mode' => 'required|in:auto,manual',
            'manual_on' => 'boolean',
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
     * Update the specified device control.
     */
    public function update(Request $request, DeviceControl $deviceControl): JsonResponse
    {
        $validatedData = $request->validate([
            'device_id' => 'nullable|string|max:255|unique:device_controls,device_id,'.$deviceControl->id,
            'mode' => 'required|in:auto,manual',
            'manual_on' => 'boolean',
        ]);

        $deviceControl->update($validatedData);

        return response()->json([
            'message' => 'Device berhasil diperbarui',
            'data' => $deviceControl,
        ]);
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
        $deviceList = DeviceControl::orderBy('device_id')->get();

        return view('device.manage', compact('deviceList'));
    }

    /**
     * Upsert device control (create or update).
     */
    public function upsert(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'nullable|exists:device_controls,id',
            'device_id' => 'required|string|max:255',
            'mode' => 'required|in:auto,manual',
        ]);

        $validatedData['manual_on'] = $request->has('manual_on');
        $validatedData['last_heartbeat'] = now();

        if ($request->filled('id')) {
            $device = DeviceControl::findOrFail($request->id);
            $device->update($validatedData);
            $message = 'Device berhasil diperbarui!';
        } else {
            $existing = DeviceControl::where('device_id', $validatedData['device_id'])->first();
            if ($existing) {
                $existing->update($validatedData);
                $message = 'Device berhasil diperbarui!';
            } else {
                DeviceControl::create($validatedData);
                $message = 'Device berhasil dibuat!';
            }
        }

        return redirect()->route('device.manage')->with('success', $message);
    }

    /**
     * Get device by device_id for ESP32.
     */
    public function getByDeviceId(string $deviceId): JsonResponse
    {
        $device = DeviceControl::where('device_id', $deviceId)->first();

        if (! $device) {
            return response()->json([
                'error' => 'Device not found',
                'message' => "Device with ID '{$deviceId}' not found",
            ], 404);
        }

        return response()->json([
            'message' => 'Device retrieved',
            'data' => $device,
        ]);
    }

    /**
     * Update device status from ESP32.
     */
    public function updateStatus(Request $request, string $deviceId): JsonResponse
    {
        $device = DeviceControl::where('device_id', $deviceId)->firstOrFail();

        $validatedData = $request->validate([
            'manual_on' => 'boolean',
        ]);

        $device->update([
            'manual_on' => $validatedData['manual_on'] ?? $device->manual_on,
            'last_heartbeat' => now(),
        ]);

        return response()->json([
            'message' => 'Device status updated',
            'data' => $device,
        ]);
    }

    /**
     * Toggle manual device on/off.
     */
    public function toggleManual(Request $request, DeviceControl $deviceControl)
    {
        if ($deviceControl->mode !== 'manual') {
            return back()->with('error', 'Device harus dalam mode manual untuk toggle ON/OFF!');
        }

        $deviceControl->update([
            'manual_on' => !$deviceControl->manual_on,
        ]);

        $status = $deviceControl->manual_on ? 'ON' : 'OFF';

        return back()->with('success', "Device {$deviceControl->device_id} berhasil di-{$status}!");
    }
}
