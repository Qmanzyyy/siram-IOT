<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    /**
     * Display a listing of jadwal.
     */
    public function index(): JsonResponse
    {
        $jadwal = Jadwal::orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Daftar jadwal berhasil diambil',
            'data' => $jadwal,
        ]);
    }

    /**
     * Store or update jadwal based on nama.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'waktu_aktif_pertama' => 'required|date_format:H:i',
            'waktu_aktif_kedua' => 'nullable|date_format:H:i',
            'lama_operasi' => 'required|integer|min:1',
            'aktif' => 'boolean',
            'hari' => 'nullable|array',
            'hari.*' => 'string|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
        ]);

        // If this jadwal is being activated, deactivate all others
        if (isset($validatedData['aktif']) && $validatedData['aktif']) {
            Jadwal::query()->update(['aktif' => false]);
        }

        // Check if jadwal with same nama exists
        $jadwal = Jadwal::where('nama', $validatedData['nama'])->first();

        if ($jadwal) {
            // Update existing jadwal
            $jadwal->update($validatedData);

            return response()->json([
                'message' => 'Jadwal berhasil diperbarui',
                'data' => $jadwal,
            ]);
        }

        // Create new jadwal
        $jadwal = Jadwal::create($validatedData);

        return response()->json([
            'message' => 'Jadwal berhasil dibuat',
            'data' => $jadwal,
        ], 201);
    }

    /**
     * Display the specified jadwal.
     */
    public function show(Jadwal $jadwal): JsonResponse
    {
        return response()->json([
            'message' => 'Detail jadwal berhasil diambil',
            'data' => $jadwal,
        ]);
    }

    /**
     * Update the specified jadwal.
     */
    public function update(Request $request, Jadwal $jadwal): JsonResponse
    {
        $validatedData = $request->validate([
            'nama' => 'nullable|string|max:255',
            'waktu_aktif_pertama' => 'required|date_format:H:i',
            'waktu_aktif_kedua' => 'nullable|date_format:H:i',
            'lama_operasi' => 'required|integer|min:1',
            'aktif' => 'boolean',
            'hari' => 'nullable|array',
            'hari.*' => 'string|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
        ]);

        // If this jadwal is being activated, deactivate all others
        if (isset($validatedData['aktif']) && $validatedData['aktif']) {
            Jadwal::where('id', '!=', $jadwal->id)->update(['aktif' => false]);
        }

        $jadwal->update($validatedData);

        return response()->json([
            'message' => 'Jadwal berhasil diperbarui',
            'data' => $jadwal,
        ]);
    }

    /**
     * Remove the specified jadwal.
     */
    public function destroy(Jadwal $jadwal)
    {
        $nama = $jadwal->nama;
        $jadwal->delete();

        // Check if request expects JSON (API call)
        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Jadwal berhasil dihapus',
            ]);
        }

        // Otherwise redirect back (web call)
        return redirect()->route('jadwal.manage')->with('success', "Jadwal '{$nama}' berhasil dihapus!");
    }

    /**
     * Show the form for managing jadwal.
     */
    public function manage()
    {
        $jadwalList = Jadwal::orderBy('nama')->get();

        // Initialize empty flow for new jadwal
        $flowStepsForRender = [];

        return view('jadwal.manage', compact('jadwalList', 'flowStepsForRender'));
    }

    /**
     * Get active jadwal for ESP32.
     */
    public function getActive(): JsonResponse
    {
        $jadwal = Jadwal::where('aktif', true)->first();

        if (! $jadwal) {
            return response()->json([
                'message' => 'No active schedule',
                'data' => null,
            ]);
        }

        return response()->json([
            'message' => 'Active schedule retrieved',
            'data' => $jadwal,
        ]);
    }

    /**
     * Upsert jadwal (create or update).
     */
    public function upsert(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'nullable|exists:jadwal,id',
            'nama' => 'required|string|max:255',
            'waktu_aktif_pertama' => 'required|date_format:H:i',
            'waktu_aktif_kedua' => 'nullable|date_format:H:i',
            'lama_operasi' => 'required|integer|min:1',
            'hari' => 'nullable|array',
            'hari.*' => 'string|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'automation_flow' => 'nullable|json',
        ]);

        // Handle checkbox aktif - unchecked checkbox doesn't send value
        $validatedData['aktif'] = $request->has('aktif');
        $validatedData['hari'] = $request->input('hari', []);

        // Parse automation_flow JSON
        if (isset($validatedData['automation_flow'])) {
            $validatedData['automation_flow'] = json_decode($validatedData['automation_flow'], true);
        }

        // If this jadwal is being activated, deactivate all others
        if ($validatedData['aktif']) {
            Jadwal::query()->update(['aktif' => false]);
        }

        if ($request->filled('id')) {
            // Update existing by ID
            $jadwal = Jadwal::findOrFail($request->id);
            $jadwal->update($validatedData);
            $message = 'Jadwal berhasil diperbarui!';
        } else {
            // Check if nama exists
            $existing = Jadwal::where('nama', $validatedData['nama'])->first();
            if ($existing) {
                $existing->update($validatedData);
                $message = 'Jadwal berhasil diperbarui!';
            } else {
                Jadwal::create($validatedData);
                $message = 'Jadwal berhasil dibuat!';
            }
        }

        return redirect()->route('jadwal.manage')->with('success', $message);
    }

    /**
     * Render flow builder canvas for AJAX requests.
     */
    public function renderFlow(Request $request)
    {
        $steps = [];
        if ($request->has('steps')) {
            $steps = json_decode(urldecode($request->steps), true) ?? [];
        }

        return view('components.flow-builder-canvas', ['steps' => $steps])->render();
    }
}
