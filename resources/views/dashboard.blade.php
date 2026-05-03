<x-layouts::app :title="__('Dashboard')">
    <div class="mx-auto max-w-7xl space-y-6">
        <!-- Header -->
        <div>
            <flux:heading size="xl">Dashboard</flux:heading>
            <flux:subheading class="mt-1">Monitoring sistem penyiraman IoT</flux:subheading>
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
            <flux:card class="border-l-4 border-green-500 bg-green-50 dark:bg-green-900/20">
                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-medium text-green-800 dark:text-green-400">{{ session('success') }}</p>
                </div>
            </flux:card>
        @endif

        @if (session('error'))
            <flux:card class="border-l-4 border-red-500 bg-red-50 dark:bg-red-900/20">
                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="font-medium text-red-800 dark:text-red-400">{{ session('error') }}</p>
                </div>
            </flux:card>
        @endif

        <div class="grid gap-6 md:grid-cols-2">
            <!-- Active Schedule Card -->
            <flux:card>
                <div class="flex items-start justify-between">
                    <div>
                        <flux:heading size="lg">Jadwal Aktif</flux:heading>
                        <flux:subheading class="mt-1">Jadwal yang sedang berjalan</flux:subheading>
                    </div>
                    <a href="{{ route('jadwal.manage') }}" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        Kelola →
                    </a>
                </div>

                @if($activeJadwal)
                    <div class="mt-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $activeJadwal->nama }}</p>
                                <div class="mt-2 space-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                                    <p>
                                        <span class="font-medium">Waktu:</span>
                                        {{ $activeJadwal->waktu_aktif_pertama }}
                                        @if($activeJadwal->waktu_aktif_kedua)
                                            & {{ $activeJadwal->waktu_aktif_kedua }}
                                        @endif
                                    </p>
                                    <p><span class="font-medium">Durasi:</span> {{ $activeJadwal->lama_operasi }} menit</p>
                                </div>
                                @if($activeJadwal->hari && count($activeJadwal->hari) > 0)
                                    <div class="mt-3 flex flex-wrap gap-1">
                                        @foreach($activeJadwal->hari as $hari)
                                            <span class="inline-flex items-center rounded bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                {{ ucfirst($hari) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <span class="ml-2 inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                Aktif
                            </span>
                        </div>
                    </div>
                @else
                    <div class="mt-4 rounded-lg border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-700">
                        <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Tidak ada jadwal aktif</p>
                        <a href="{{ route('jadwal.manage') }}" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400">
                            Aktifkan jadwal →
                        </a>
                    </div>
                @endif
            </flux:card>

            <!-- Device Status Card -->
            <flux:card>
                <div class="flex items-start justify-between">
                    <div>
                        <flux:heading size="lg">Status Device</flux:heading>
                        <flux:subheading class="mt-1">Monitoring perangkat IoT</flux:subheading>
                    </div>
                    <a href="{{ route('device.manage') }}" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        Kelola →
                    </a>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse($devices as $device)
                        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $device->device_id }}</p>
                                    <div class="mt-1 flex items-center gap-2">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $device->mode === 'auto' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                            {{ ucfirst($device->mode) }}
                                        </span>
                                        @if($device->last_heartbeat)
                                            <span class="text-xs text-zinc-500 dark:text-zinc-500">
                                                {{ $device->last_heartbeat->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if($device->mode === 'manual')
                                    <form action="{{ route('device.toggle', $device) }}" method="POST">
                                        @csrf
                                        <label class="relative inline-flex cursor-pointer items-center">
                                            <input
                                                type="checkbox"
                                                class="peer sr-only"
                                                {{ $device->manual_on ? 'checked' : '' }}
                                                onchange="this.form.submit()"
                                            >
                                            <div class="peer h-6 w-11 rounded-full bg-zinc-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-zinc-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:border-zinc-600 dark:bg-zinc-700 dark:peer-focus:ring-green-800"></div>
                                        </label>
                                    </form>
                                @else
                                    <span class="text-xs text-zinc-500 dark:text-zinc-500">Mode Auto</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-700">
                            <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Belum ada device terdaftar</p>
                            <a href="{{ route('device.manage') }}" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                Tambah device →
                            </a>
                        </div>
                    @endforelse
                </div>
            </flux:card>
        </div>

        <!-- Summary Stats -->
        <div class="grid gap-6 md:grid-cols-3">
            <flux:card>
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Jadwal</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ \App\Models\Jadwal::count() }}</p>
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900/30">
                        <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Device</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $devices->count() }}</p>
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Device Manual</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $devices->where('mode', 'manual')->count() }}</p>
                    </div>
                </div>
            </flux:card>
        </div>
    </div>
</x-layouts::app>
