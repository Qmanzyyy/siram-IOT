<x-layouts::app :title="__('Kelola Device')">
    <div class="flex flex-col gap-6">
        <div>
            <flux:heading size="xl">{{ __('Kelola Device') }}</flux:heading>
            <flux:subheading>{{ __('Konfigurasi dan kontrol device IoT Smart Farm') }}</flux:subheading>
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

        <!-- Device Cards Grid -->
        <div class="grid gap-6 md:grid-cols-2">
            @foreach($devices as $device)
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6">
                    <!-- Device Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-lg
                                {{ $device->isMotor() ? 'bg-blue-100 dark:bg-blue-900/30' : '' }}
                                {{ $device->isRelay() ? 'bg-green-100 dark:bg-green-900/30' : '' }}
                                {{ $device->isServo() ? 'bg-purple-100 dark:bg-purple-900/30' : '' }}">
                                @if($device->isMotor())
                                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                @elseif($device->isRelay())
                                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                    </svg>
                                @else
                                    <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <flux:heading size="lg">{{ $device->name }}</flux:heading>
                                <flux:subheading class="text-sm">
                                    <flux:badge :variant="$device->mode === 'auto' ? 'info' : 'warning'" size="sm">
                                        {{ ucfirst($device->mode) }}
                                    </flux:badge>
                                </flux:subheading>
                            </div>
                        </div>

                        <!-- Toggle Active -->
                        <form action="{{ route('device.toggle', $device) }}" method="POST" class="inline">
                            @csrf
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input
                                    type="checkbox"
                                    class="peer sr-only"
                                    {{ $device->is_active ? 'checked' : '' }}
                                    onchange="this.form.submit()"
                                >
                                <div class="peer h-6 w-11 rounded-full bg-zinc-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-zinc-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:border-zinc-600 dark:bg-zinc-700 dark:peer-focus:ring-green-800"></div>
                            </label>
                        </form>
                    </div>

                    <!-- Device Info -->
                    <div class="space-y-3 mb-4">
                        @if($device->isMotor())
                            <!-- Motor Info -->
                            <div class="flex justify-between items-center">
                                <flux:subheading class="text-sm">Kecepatan (PWM)</flux:subheading>
                                <flux:badge>{{ $device->speed ?? 0 }}</flux:badge>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:subheading class="text-sm">Posisi</flux:subheading>
                                <flux:badge>{{ number_format($device->calibration_percentage, 1) }}%</flux:badge>
                            </div>
                            <div class="flex justify-between items-center">
                                <flux:subheading class="text-sm">Max Steps</flux:subheading>
                                <flux:badge>{{ $device->calibration_max_steps ?? 0 }}</flux:badge>
                            </div>
                        @elseif($device->isRelay())
                            <!-- Relay Info -->
                            <div class="flex justify-between items-center">
                                <flux:subheading class="text-sm">Status Pompa</flux:subheading>
                                <flux:badge :variant="$device->relay_state ? 'success' : 'default'">
                                    {{ $device->relay_state ? 'ON' : 'OFF' }}
                                </flux:badge>
                            </div>
                        @elseif($device->isServo())
                            <!-- Servo Info -->
                            <div class="flex justify-between items-center">
                                <flux:subheading class="text-sm">Sudut Nozzle</flux:subheading>
                                <flux:badge>{{ $device->servo_angle ?? 90 }}°</flux:badge>
                            </div>
                        @endif

                        @if($device->last_heartbeat)
                            <div class="flex justify-between items-center">
                                <flux:subheading class="text-sm">Last Heartbeat</flux:subheading>
                                <flux:badge size="sm" variant="default">
                                    {{ $device->last_heartbeat->diffForHumans() }}
                                </flux:badge>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <flux:modal.trigger name="config-{{ $device->id }}">
                            <flux:button size="sm" variant="ghost" class="flex-1">
                                <flux:icon.cog-6-tooth class="size-4" />
                                Konfigurasi
                            </flux:button>
                        </flux:modal.trigger>

                        @if($device->isMotor())
                            <flux:modal.trigger name="calibration-{{ $device->id }}">
                                <flux:button size="sm" variant="ghost">
                                    <flux:icon.adjustments-horizontal class="size-4" />
                                    Kalibrasi
                                </flux:button>
                            </flux:modal.trigger>
                        @endif
                    </div>

                    <!-- Configuration Modal -->
                    <flux:modal name="config-{{ $device->id }}" class="space-y-6">
                        <form action="{{ route('device.update', $device) }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <flux:heading size="lg">Konfigurasi {{ $device->name }}</flux:heading>
                                <flux:subheading>Atur parameter operasional device</flux:subheading>
                            </div>

                            <!-- Mode Selection -->
                            <flux:radio.group name="mode" label="Mode Operasi">
                                <flux:radio value="manual" label="Manual" description="Kontrol manual melalui dashboard" :checked="$device->mode === 'manual'" />
                                <flux:radio value="auto" label="Otomatis" description="Mengikuti jadwal yang telah diatur" :checked="$device->mode === 'auto'" />
                            </flux:radio.group>

                            @if($device->isMotor())
                                <!-- Motor Speed -->
                                <flux:input
                                    name="speed"
                                    label="Kecepatan Motor (PWM)"
                                    type="number"
                                    min="0"
                                    max="255"
                                    :value="$device->speed"
                                    description="Nilai 0-255 untuk mengatur kecepatan putaran motor"
                                />

                                <!-- Position Slider -->
                                <flux:field>
                                    <flux:label>Posisi Target (%)</flux:label>
                                    <flux:description>Geser untuk mengatur posisi target device</flux:description>
                                    <input
                                        type="range"
                                        name="calibration_percentage"
                                        min="0"
                                        max="100"
                                        step="0.1"
                                        value="{{ $device->calibration_percentage }}"
                                        class="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer dark:bg-zinc-700"
                                        oninput="this.nextElementSibling.querySelector('span').textContent = parseFloat(this.value).toFixed(1)"
                                    />
                                    <div class="flex justify-between text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                        <span>0%</span>
                                        <span class="font-medium"><span>{{ number_format($device->calibration_percentage, 1) }}</span>%</span>
                                        <span>100%</span>
                                    </div>
                                </flux:field>
                            @elseif($device->isRelay())
                                <!-- Relay State -->
                                <flux:switch
                                    name="relay_state"
                                    label="Status Pompa"
                                    description="Aktifkan untuk menyalakan pompa"
                                    :checked="$device->relay_state"
                                />
                            @elseif($device->isServo())
                                <!-- Servo Angle -->
                                <flux:field>
                                    <flux:label>Sudut Servo (°)</flux:label>
                                    <flux:description>Atur sudut nozzle (0-180 derajat)</flux:description>
                                    <input
                                        type="range"
                                        name="servo_angle"
                                        min="0"
                                        max="180"
                                        step="1"
                                        value="{{ $device->servo_angle }}"
                                        class="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer dark:bg-zinc-700"
                                        oninput="this.nextElementSibling.querySelector('span').textContent = this.value"
                                    />
                                    <div class="flex justify-between text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                                        <span>0°</span>
                                        <span class="font-medium"><span>{{ $device->servo_angle }}</span>°</span>
                                        <span>180°</span>
                                    </div>
                                </flux:field>
                            @endif

                            <div class="flex gap-2 justify-end">
                                <flux:modal.close>
                                    <flux:button type="button" variant="ghost">Batal</flux:button>
                                </flux:modal.close>
                                <flux:button type="submit" variant="primary">Simpan Konfigurasi</flux:button>
                            </div>
                        </form>
                    </flux:modal>

                    <!-- Calibration Modal -->
                    @if($device->isMotor())
                        <flux:modal name="calibration-{{ $device->id }}" class="space-y-6">
                            <div>
                                <flux:heading size="lg">Kalibrasi {{ $device->name }}</flux:heading>
                                <flux:subheading>Tentukan batas maksimal pergerakan device</flux:subheading>
                            </div>

                            <flux:card>
                                <div class="space-y-4">
                                    <flux:heading size="sm">Langkah Kalibrasi:</flux:heading>
                                    <ol class="list-decimal list-inside space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                                        <li>Klik tombol "Mulai Kalibrasi" untuk reset posisi</li>
                                        <li>Jalankan device hingga mencapai titik maksimal</li>
                                        <li>Catat posisi saat push button tertekan</li>
                                        <li>Masukkan persentase posisi dan simpan</li>
                                    </ol>
                                </div>
                            </flux:card>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <flux:subheading class="text-sm mb-1">Posisi Saat Ini</flux:subheading>
                                    <flux:badge size="lg">{{ $device->current_position }} steps</flux:badge>
                                </div>
                                <div>
                                    <flux:subheading class="text-sm mb-1">Max Steps</flux:subheading>
                                    <flux:badge size="lg">{{ $device->calibration_max_steps ?? 0 }} steps</flux:badge>
                                </div>
                            </div>

                            <form action="{{ route('device.calibration.save', $device) }}" method="POST" class="space-y-4">
                                @csrf
                                <flux:input
                                    name="calibration_percentage"
                                    label="Persentase Posisi Saat Ini"
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.1"
                                    :value="$device->calibration_percentage"
                                    description="Masukkan persentase posisi device saat ini (0-100%)"
                                />

                                <div class="flex gap-2 justify-end">
                                    <flux:modal.close>
                                        <flux:button type="button" variant="ghost">Tutup</flux:button>
                                    </flux:modal.close>
                                    <form action="{{ route('device.calibration.start', $device) }}" method="POST" class="inline">
                                        @csrf
                                        <flux:button type="submit" variant="outline">Mulai Kalibrasi</flux:button>
                                    </form>
                                    <flux:button type="submit" variant="primary">Simpan Kalibrasi</flux:button>
                                </div>
                            </form>
                        </flux:modal>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-layouts::app>
