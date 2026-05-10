<?php

namespace App\Http\Controllers;

use App\Models\DeviceControl;
use App\Models\Jadwal;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $activeJadwal = Jadwal::where('aktif', true)->first();
        $devices = DeviceControl::orderBy('id')->get();

        return view('dashboard', compact('activeJadwal', 'devices'));
    }
}
