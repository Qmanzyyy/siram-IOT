<x-layouts::app title="Kelola Device">
    <div class="mx-auto max-w-7xl space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Kelola Device Control</flux:heading>
                <flux:subheading class="mt-1">Atur device IoT untuk sistem penyiraman otomatis</flux:subheading>
            </div>
            <flux:button type="button" variant="primary" onclick="resetForm()">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Device Baru
            </flux:button>
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

        @if ($errors->any())
            <flux:card class="border-l-4 border-red-500 bg-red-50 dark:bg-red-900/20">
                <div class="flex gap-3">
                    <svg class="h-6 w-6 flex-shrink-0 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="font-medium text-red-800 dark:text-red-400">Terdapat kesalahan:</p>
                        <ul class="mt-2 list-disc pl-5 text-sm text-red-700 dark:text-red-300">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </flux:card>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Device List Sidebar -->
            <div class="lg:col-span-1">
                <flux:card>
                    <flux:heading size="lg">Daftar Device</flux:heading>
                    <flux:subheading class="mt-1">Klik untuk edit</flux:subheading>

                    <div class="mt-4 space-y-2">
                        @forelse ($deviceList as $device)
                            <button
                                type="button"
                                onclick="loadDevice({{ $device->id }})"
                                class="w-full rounded-lg border border-zinc-200 p-4 text-left transition hover:border-zinc-400 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:border-zinc-500 dark:hover:bg-zinc-800"
                            >
                                <div class="flex-1">
                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $device->device_id }}</p>
                                    <div class="mt-2 flex items-center gap-2">
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $device->mode === 'auto' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' }}">
                                            {{ ucfirst($device->mode) }}
                                        </span>
                                        @if($device->mode === 'manual')
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $device->manual_on ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                                {{ $device->manual_on ? 'ON' : 'OFF' }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($device->last_heartbeat)
                                        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-500">
                                            Last seen: {{ $device->last_heartbeat->diffForHumans() }}
                                        </p>
                                    @endif
                                </div>
                            </button>
                        @empty
                            <div class="rounded-lg border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-700">
                                <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Belum ada device</p>
                                <p class="mt-1 text-xs text-zinc-500">Klik "Tambah Device Baru" untuk memulai</p>
                            </div>
                        @endforelse
                    </div>
                </flux:card>
            </div>

            <!-- Form Section -->
            <div class="lg:col-span-2">
                <flux:card>
                    <flux:heading size="lg" id="form-title">Tambah Device Baru</flux:heading>
                    <flux:subheading class="mt-1">Isi form di bawah untuk membuat atau mengubah device</flux:subheading>

                    <form action="{{ route('device.upsert') }}" method="POST" id="device-form" class="mt-6 space-y-6">
                        @csrf
                        <input type="hidden" name="id" id="device-id" value="{{ old('id') }}">

                        <flux:input
                            name="device_id"
                            id="device_id"
                            label="Device ID"
                            placeholder="Contoh: pompa_01, pompa_02"
                            value="{{ old('device_id') }}"
                            required
                        />

                        <!-- Mode Selection -->
                        <div class="space-y-3">
                            <label class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Mode Operasi</label>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <label class="flex items-center gap-3 rounded-lg border border-zinc-200 p-4 cursor-pointer hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                                    <input type="radio" name="mode" id="mode-auto" value="auto" class="h-4 w-4 border-zinc-300 text-blue-600 focus:ring-blue-500" {{ old('mode', 'auto') === 'auto' ? 'checked' : '' }} required>
                                    <div>
                                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Auto</span>
                                        <p class="text-xs text-zinc-500">Mengikuti jadwal</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3 rounded-lg border border-zinc-200 p-4 cursor-pointer hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                                    <input type="radio" name="mode" id="mode-manual" value="manual" class="h-4 w-4 border-zinc-300 text-blue-600 focus:ring-blue-500" {{ old('mode') === 'manual' ? 'checked' : '' }} required>
                                    <div>
                                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Manual</span>
                                        <p class="text-xs text-zinc-500">Kontrol manual</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Manual ON/OFF Switch -->
                        <div id="manual-control" class="hidden rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="manual_on" class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Status Pompa</label>
                                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Nyalakan atau matikan pompa secara manual</p>
                                </div>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" name="manual_on" id="manual_on" class="peer sr-only" {{ old('manual_on') ? 'checked' : '' }}>
                                    <div class="peer h-6 w-11 rounded-full bg-zinc-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-zinc-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:border-zinc-600 dark:bg-zinc-700 dark:peer-focus:ring-green-800"></div>
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-3 border-t border-zinc-200 pt-6 dark:border-zinc-700">
                            <button type="submit" class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan Device
                            </button>
                            <button type="button" onclick="resetForm()" class="flex items-center justify-center gap-2 rounded-lg border border-zinc-300 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reset Form
                            </button>
                        </div>

                        <!-- Delete Button -->
                        <div id="delete-section" class="hidden border-t border-zinc-200 pt-4 dark:border-zinc-700">
                            <p class="mb-3 text-sm text-zinc-600 dark:text-zinc-400">Hapus device ini secara permanen</p>
                            <button type="button" onclick="showDeleteModal()" class="flex items-center justify-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-2.5 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:border-red-800 dark:bg-zinc-900 dark:text-red-400 dark:hover:bg-red-900/20">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus Device
                            </button>
                        </div>
                    </form>

                    <!-- Hidden delete form -->
                    <form id="delete-form" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </flux:card>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="mx-4 w-full max-w-md rounded-lg bg-white p-6 shadow-xl dark:bg-zinc-900">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Hapus Device</h3>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                            Yakin ingin menghapus device "<span id="modal-device-id" class="font-semibold"></span>"?
                        </p>
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                            Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button type="button" onclick="hideDeleteModal()" class="flex flex-1 items-center justify-center gap-2 rounded-lg border border-zinc-300 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                        Batal
                    </button>
                    <button type="button" onclick="confirmDelete()" class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadDevice(id) {
            if (!id) {
                resetForm();
                return;
            }

            fetch(`/device-control/${id}`)
                .then(res => res.json())
                .then(data => {
                    const device = data.data;
                    document.getElementById('form-title').textContent = 'Edit Device: ' + device.device_id;
                    document.getElementById('device-id').value = device.id;
                    document.getElementById('device_id').value = device.device_id || '';

                    // Set mode
                    if (device.mode === 'auto') {
                        document.getElementById('mode-auto').checked = true;
                    } else {
                        document.getElementById('mode-manual').checked = true;
                    }

                    // Set manual_on
                    document.getElementById('manual_on').checked = device.manual_on;

                    // Show/hide manual control
                    toggleManualControl();

                    // Show delete section
                    document.getElementById('delete-section').classList.remove('hidden');
                    document.getElementById('delete-form').action = `/device-control/${device.id}`;

                    // Scroll to form
                    document.getElementById('form-title').scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
        }

        function resetForm() {
            document.getElementById('form-title').textContent = 'Tambah Device Baru';
            document.getElementById('device-id').value = '';
            document.getElementById('device_id').value = '';
            document.getElementById('mode-auto').checked = true;
            document.getElementById('manual_on').checked = false;

            toggleManualControl();
            document.getElementById('delete-section').classList.add('hidden');
        }

        function toggleManualControl() {
            const manualControl = document.getElementById('manual-control');
            const modeManual = document.getElementById('mode-manual').checked;

            if (modeManual) {
                manualControl.classList.remove('hidden');
            } else {
                manualControl.classList.add('hidden');
            }
        }

        function showDeleteModal() {
            const deviceId = document.getElementById('device_id').value;
            document.getElementById('modal-device-id').textContent = deviceId;
            const modal = document.getElementById('delete-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function hideDeleteModal() {
            const modal = document.getElementById('delete-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function confirmDelete() {
            document.getElementById('delete-form').submit();
        }

        // Listen to mode changes
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[name="mode"]').forEach(radio => {
                radio.addEventListener('change', toggleManualControl);
            });

            // Close modal when clicking outside
            const modal = document.getElementById('delete-modal');
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    hideDeleteModal();
                }
            });
        });
    </script>
</x-layouts::app>
