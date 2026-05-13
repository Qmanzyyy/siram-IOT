<x-layouts::app title="Kelola Jadwal">
    <div class="mx-auto max-w-7xl space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Kelola Jadwal Penyiraman</flux:heading>
                <flux:subheading class="mt-1">Atur jadwal otomatis untuk sistem penyiraman IoT</flux:subheading>
            </div>
            <flux:button type="button" variant="primary" onclick="resetForm()">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Jadwal Baru
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
            <!-- Jadwal List Sidebar -->
            <div class="lg:col-span-1">
                <flux:card>
                    <flux:heading size="lg">Daftar Jadwal</flux:heading>
                    <flux:subheading class="mt-1">Klik untuk edit</flux:subheading>

                    <div class="mt-4 space-y-2">
                        @forelse ($jadwalList as $j)
                            <button
                                type="button"
                                onclick="loadJadwal({{ $j->id }})"
                                class="w-full rounded-lg border border-zinc-200 p-4 text-left transition hover:border-zinc-400 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:border-zinc-500 dark:hover:bg-zinc-800"
                            >
                                <div class="flex-1">
                                    <p class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $j->nama }}</p>
                                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                                        {{ $j->waktu_aktif_pertama }}
                                        @if($j->waktu_aktif_kedua)
                                            & {{ $j->waktu_aktif_kedua }}
                                        @endif
                                    </p>
                                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-500">
                                        {{ $j->lama_operasi }} menit
                                    </p>
                                    <span class="mt-2 inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $j->aktif ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-400' }}">
                                        {{ $j->aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                                @if($j->hari && count($j->hari) > 0)
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        @foreach($j->hari as $hari)
                                            <span class="inline-flex items-center rounded bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                {{ ucfirst($hari) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </button>
                        @empty
                            <div class="rounded-lg border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-700">
                                <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Belum ada jadwal</p>
                                <p class="mt-1 text-xs text-zinc-500">Klik "Buat Jadwal Baru" untuk memulai</p>
                            </div>
                        @endforelse
                    </div>
                </flux:card>
            </div>

            <!-- Form Section -->
            <div class="lg:col-span-2">
                <flux:card>
                    <flux:heading size="lg" id="form-title">Buat Jadwal Baru</flux:heading>
                    <flux:subheading class="mt-1">Isi form di bawah untuk membuat atau mengubah jadwal</flux:subheading>

                    <form action="{{ route('jadwal.upsert') }}" method="POST" id="jadwal-form" class="mt-6 space-y-6">
                        @csrf
                        <input type="hidden" name="id" id="jadwal-id" value="{{ old('id') }}">

                        <flux:input
                            name="nama"
                            id="nama"
                            label="Nama Jadwal"
                            placeholder="Contoh: Jadwal Pagi, Jadwal Sore"
                            value="{{ old('nama') }}"
                            required
                        />

                        <div class="grid gap-6 md:grid-cols-2">
                            <flux:input
                                name="waktu_aktif_pertama"
                                id="waktu_aktif_pertama"
                                type="time"
                                label="Waktu Aktif Pertama"
                                value="{{ old('waktu_aktif_pertama') }}"
                                required
                            />
                            <flux:input
                                name="waktu_aktif_kedua"
                                id="waktu_aktif_kedua"
                                type="time"
                                label="Waktu Aktif Kedua (Opsional)"
                                value="{{ old('waktu_aktif_kedua') }}"
                            />
                        </div>

                        <flux:input
                            name="lama_operasi"
                            id="lama_operasi"
                            type="number"
                            label="Lama Operasi (menit)"
                            min="1"
                            value="{{ old('lama_operasi', 30) }}"
                            required
                        />

                        <!-- Switch Aktif/Nonaktif -->
                        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label for="aktif" class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Jadwal Aktif</label>
                                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Nonaktifkan jika jadwal tidak ingin dijalankan sementara</p>
                                </div>
                                <label class="relative inline-flex cursor-pointer items-center">
                                    <input type="checkbox" name="aktif" id="aktif" class="peer sr-only" {{ old('aktif', true) ? 'checked' : '' }}>
                                    <div class="peer h-6 w-11 rounded-full bg-zinc-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-zinc-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:border-zinc-600 dark:bg-zinc-700 dark:peer-focus:ring-blue-800"></div>
                                </label>
                            </div>
                        </div>

                        <!-- Automation Flow Configuration -->
                        <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Konfigurasi Otomatis</label>
                                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Atur alur otomatis untuk mengontrol device</p>
                                </div>
                                <button type="button" onclick="openFlowBuilder()" class="flex items-center gap-2 rounded-lg bg-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-purple-700">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                    </svg>
                                    Buat Flow
                                </button>
                            </div>
                            <div id="flow-preview" class="mt-3 hidden">
                                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Flow yang dikonfigurasi:</p>
                                <div id="flow-steps" class="mt-2 space-y-2"></div>
                            </div>
                            <input type="hidden" name="automation_flow" id="automation_flow" value="{{ old('automation_flow') }}">
                        </div>

                        <!-- Hari Operasi -->
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Hari Operasi</label>
                                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Pilih hari-hari ketika jadwal ini akan berjalan</p>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-4">
                                <label class="flex items-center gap-2 rounded-lg border border-zinc-200 p-3 cursor-pointer hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                                    <input type="checkbox" name="hari[]" id="hari-senin" value="senin" class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700 dark:ring-offset-zinc-800 dark:focus:ring-blue-600" {{ in_array('senin', old('hari', [])) ? 'checked' : '' }}>
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Senin</span>
                                </label>
                                <label class="flex items-center gap-2 rounded-lg border border-zinc-200 p-3 cursor-pointer hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                                    <input type="checkbox" name="hari[]" id="hari-selasa" value="selasa" class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700 dark:ring-offset-zinc-800 dark:focus:ring-blue-600" {{ in_array('selasa', old('hari', [])) ? 'checked' : '' }}>
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Selasa</span>
                                </label>
                                <label class="flex items-center gap-2 rounded-lg border border-zinc-200 p-3 cursor-pointer hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                                    <input type="checkbox" name="hari[]" id="hari-rabu" value="rabu" class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700 dark:ring-offset-zinc-800 dark:focus:ring-blue-600" {{ in_array('rabu', old('hari', [])) ? 'checked' : '' }}>
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Rabu</span>
                                </label>
                                <label class="flex items-center gap-2 rounded-lg border border-zinc-200 p-3 cursor-pointer hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                                    <input type="checkbox" name="hari[]" id="hari-kamis" value="kamis" class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700 dark:ring-offset-zinc-800 dark:focus:ring-blue-600" {{ in_array('kamis', old('hari', [])) ? 'checked' : '' }}>
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Kamis</span>
                                </label>
                                <label class="flex items-center gap-2 rounded-lg border border-zinc-200 p-3 cursor-pointer hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                                    <input type="checkbox" name="hari[]" id="hari-jumat" value="jumat" class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700 dark:ring-offset-zinc-800 dark:focus:ring-blue-600" {{ in_array('jumat', old('hari', [])) ? 'checked' : '' }}>
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Jumat</span>
                                </label>
                                <label class="flex items-center gap-2 rounded-lg border border-zinc-200 p-3 cursor-pointer hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                                    <input type="checkbox" name="hari[]" id="hari-sabtu" value="sabtu" class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700 dark:ring-offset-zinc-800 dark:focus:ring-blue-600" {{ in_array('sabtu', old('hari', [])) ? 'checked' : '' }}>
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Sabtu</span>
                                </label>
                                <label class="flex items-center gap-2 rounded-lg border border-zinc-200 p-3 cursor-pointer hover:bg-zinc-50 dark:border-zinc-700 dark:hover:bg-zinc-800">
                                    <input type="checkbox" name="hari[]" id="hari-minggu" value="minggu" class="h-4 w-4 rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700 dark:ring-offset-zinc-800 dark:focus:ring-blue-600" {{ in_array('minggu', old('hari', [])) ? 'checked' : '' }}>
                                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Minggu</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-3 border-t border-zinc-200 pt-6 dark:border-zinc-700">
                            <button type="submit" class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-600 dark:hover:bg-blue-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan Jadwal
                            </button>
                            <button type="button" onclick="resetForm()" class="flex items-center justify-center gap-2 rounded-lg border border-zinc-300 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reset Form
                            </button>
                        </div>

                        <!-- Delete Button (only show when editing) -->
                        <div id="delete-section" class="hidden border-t border-zinc-200 pt-4 dark:border-zinc-700">
                            <p class="mb-3 text-sm text-zinc-600 dark:text-zinc-400">Hapus jadwal ini secara permanen</p>
                            <button type="button" onclick="showDeleteModal()" class="flex items-center justify-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-2.5 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:border-red-800 dark:bg-zinc-900 dark:text-red-400 dark:hover:bg-red-900/20">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus Jadwal
                            </button>
                        </div>
                    </form>

                    <!-- Hidden delete form (OUTSIDE main form) -->
                    <form id="delete-form" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </flux:card>
            </div>
        </div>

        <!-- Flow Builder Modal -->
        <div id="flow-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm overflow-y-auto">
            <div class="mx-4 my-8 w-full max-w-6xl rounded-lg bg-white p-6 shadow-xl dark:bg-zinc-900">
                <div class="flex items-center justify-between border-b border-zinc-200 pb-4 dark:border-zinc-700">
                    <div>
                        <h3 class="text-xl font-semibold text-zinc-900 dark:text-zinc-100">Automation Flow Builder</h3>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Buat alur otomatis untuk mengontrol device</p>
                    </div>
                    <button type="button" onclick="closeFlowBuilder()" class="rounded-lg p-2 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-800 dark:hover:text-zinc-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Flow Canvas -->
                <div id="flow-canvas-container" class="mt-4">
                    <x-flow-builder-canvas :steps="[]" />
                </div>

                <!-- Add Step Button -->
                <div class="mt-4">
                    <button type="button" onclick="addFlowStep()" class="flex items-center gap-2 rounded-lg bg-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-purple-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Step
                    </button>
                </div>

                <div class="mt-6 flex gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                    <button type="button" onclick="closeFlowBuilder()" class="flex flex-1 items-center justify-center gap-2 rounded-lg border border-zinc-300 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                        Batal
                    </button>
                    <button type="button" onclick="saveFlow()" class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-purple-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Flow
                    </button>
                </div>
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
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Hapus Jadwal</h3>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                            Yakin ingin menghapus jadwal "<span id="modal-jadwal-nama" class="font-semibold"></span>"?
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
                    <button type="button" onclick="confirmDelete()" class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:bg-red-600 dark:hover:bg-red-700">
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
        // Initialize window variables
        window.flowSteps = [];
        window.stepCounter = 0;
        window.connections = [];
        window.selectedConnection = null;
        window.draggedNode = null;
        window.dragOffset = { x: 0, y: 0 };
        window.animationFrameId = null;

        /**
         * Generate optimized lightning path with organic zig-zag
         */
        function generateSimpleLightning(x1, y1, x2, y2) {
            const points = [{ x: x1, y: y1 }];
            const dx = x2 - x1;
            const dy = y2 - y1;
            const distance = Math.sqrt(dx * dx + dy * dy);
            const segments = Math.max(4, Math.ceil(distance / 30));

            for (let i = 1; i < segments; i++) {
                const t = i / segments;
                const x = x1 + dx * t;
                const y = y1 + dy * t;
                const perpX = -dy / distance;
                const perpY = dx / distance;
                const randomFactor = Math.sin(t * Math.PI) * (Math.random() - 0.5);
                const offset = distance * 0.08 * randomFactor;
                points.push({ x: x + perpX * offset, y: y + perpY * offset });
            }
            points.push({ x: x2, y: y2 });

            let pathData = `M ${points[0].x.toFixed(1)} ${points[0].y.toFixed(1)}`;
            for (let i = 1; i < points.length; i++) {
                pathData += ` L ${points[i].x.toFixed(1)} ${points[i].y.toFixed(1)}`;
            }
            return pathData;
        }

        /**
         * Initialize drag system with smooth 60fps updates
         */
        function initializeDragSystem() {
            const canvas = document.getElementById('flow-canvas');
            if (!canvas) return;

            canvas.addEventListener('mousedown', function(e) {
                const node = e.target.closest('.flow-node');
                const header = e.target.closest('.node-header');
                if (node && header) {
                    window.draggedNode = node;
                    const rect = node.getBoundingClientRect();
                    window.dragOffset = { x: e.clientX - rect.left, y: e.clientY - rect.top };
                    node.style.cursor = 'grabbing';
                    e.preventDefault();
                }
            });

            document.addEventListener('mousemove', function(e) {
                if (!window.draggedNode) return;
                const canvas = document.getElementById('flow-canvas');
                const canvasRect = canvas.getBoundingClientRect();
                const newX = e.clientX - canvasRect.left + canvas.scrollLeft - window.dragOffset.x;
                const newY = e.clientY - canvasRect.top + canvas.scrollTop - window.dragOffset.y;
                window.draggedNode.style.left = Math.max(0, newX) + 'px';
                window.draggedNode.style.top = Math.max(0, newY) + 'px';

                // Use RAF for smooth updates
                if (window.animationFrameId) cancelAnimationFrame(window.animationFrameId);
                window.animationFrameId = requestAnimationFrame(() => {
                    drawElectricLines();
                });
            });

            document.addEventListener('mouseup', function(e) {
                if (window.draggedNode) {
                    window.draggedNode.style.cursor = '';
                    window.draggedNode = null;
                    drawElectricLines();
                }
            });
        }

        /**
         * Draw electric lines with persistent connection handles
         */
        function drawElectricLines() {
            const svg = document.getElementById('flow-lines');
            if (!svg) return;
            const defs = svg.querySelector('defs');
            svg.innerHTML = '';
            if (defs) svg.appendChild(defs);

            // Mark all connected handles
            document.querySelectorAll('.connection-handle').forEach(handle => {
                handle.classList.remove('connected');
            });

            window.connections.forEach(conn => {
                if (!conn.enabled) return;
                const fromNode = document.querySelector(`[data-index="${conn.from}"]`);
                const toNode = document.querySelector(`[data-index="${conn.to}"]`);
                if (!fromNode || !toNode) return;

                const fromHandle = fromNode.querySelector(`[data-side="${conn.fromSide}"]`);
                const toHandle = toNode.querySelector(`[data-side="${conn.toSide}"]`);

                // Mark handles as connected (stays visible)
                if (fromHandle) fromHandle.classList.add('connected');
                if (toHandle) toHandle.classList.add('connected');

                const fromRect = fromNode.getBoundingClientRect();
                const toRect = toNode.getBoundingClientRect();
                const svgRect = svg.getBoundingClientRect();
                const canvas = document.getElementById('flow-canvas');
                const scrollLeft = canvas.scrollLeft;
                const scrollTop = canvas.scrollTop;

                let x1, y1, x2, y2;
                switch(conn.fromSide) {
                    case 'right': x1 = fromRect.right - svgRect.left + scrollLeft; y1 = fromRect.top + fromRect.height / 2 - svgRect.top + scrollTop; break;
                    case 'left': x1 = fromRect.left - svgRect.left + scrollLeft; y1 = fromRect.top + fromRect.height / 2 - svgRect.top + scrollTop; break;
                    case 'top': x1 = fromRect.left + fromRect.width / 2 - svgRect.left + scrollLeft; y1 = fromRect.top - svgRect.top + scrollTop; break;
                    case 'bottom': x1 = fromRect.left + fromRect.width / 2 - svgRect.left + scrollLeft; y1 = fromRect.bottom - svgRect.top + scrollTop; break;
                }
                switch(conn.toSide) {
                    case 'right': x2 = toRect.right - svgRect.left + scrollLeft; y2 = toRect.top + toRect.height / 2 - svgRect.top + scrollTop; break;
                    case 'left': x2 = toRect.left - svgRect.left + scrollLeft; y2 = toRect.top + toRect.height / 2 - svgRect.top + scrollTop; break;
                    case 'top': x2 = toRect.left + toRect.width / 2 - svgRect.left + scrollLeft; y2 = toRect.top - svgRect.top + scrollTop; break;
                    case 'bottom': x2 = toRect.left + toRect.width / 2 - svgRect.left + scrollLeft; y2 = toRect.bottom - svgRect.top + scrollTop; break;
                }

                const lightningPath = generateSimpleLightning(x1, y1, x2, y2);

                // Glow layer (subtle)
                const glowPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                glowPath.setAttribute('d', lightningPath);
                glowPath.setAttribute('stroke', '#06b6d4');
                glowPath.setAttribute('stroke-width', '4');
                glowPath.setAttribute('fill', 'none');
                glowPath.setAttribute('opacity', '0.15');
                glowPath.setAttribute('stroke-linecap', 'round');
                glowPath.setAttribute('stroke-linejoin', 'round');
                svg.appendChild(glowPath);

                // Core lightning
                const corePath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                corePath.setAttribute('d', lightningPath);
                corePath.setAttribute('stroke', '#0ea5e9');
                corePath.setAttribute('stroke-width', '2');
                corePath.setAttribute('fill', 'none');
                corePath.setAttribute('opacity', '0.9');
                corePath.setAttribute('stroke-linecap', 'round');
                corePath.setAttribute('stroke-linejoin', 'round');
                svg.appendChild(corePath);
            });
        }

        /**
         * Start connection from handle
         */
        function startConnection(event, nodeIndex, side) {
            event.stopPropagation();
            const handle = event.target;
            if (!window.selectedConnection) {
                window.selectedConnection = { fromNode: nodeIndex, fromSide: side, element: handle };
                handle.classList.add('active');
            } else {
                if (window.selectedConnection.fromNode !== nodeIndex) {
                    window.connections.push({
                        from: window.selectedConnection.fromNode,
                        fromSide: window.selectedConnection.fromSide,
                        to: nodeIndex,
                        toSide: side,
                        enabled: true
                    });
                    drawElectricLines();
                }
                window.selectedConnection.element.classList.remove('active');
                window.selectedConnection = null;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initializeDragSystem();
            drawElectricLines();
        });

        // Flow builder UI functions
        function openFlowBuilder() {
            const existingFlow = document.getElementById('automation_flow').value;
            if (existingFlow) {
                try {
                    window.flowSteps = JSON.parse(existingFlow);
                    window.stepCounter = window.flowSteps.length;
                } catch (e) {
                    window.flowSteps = [];
                    window.stepCounter = 0;
                }
            }

            renderFlowSteps();
            const modal = document.getElementById('flow-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeFlowBuilder() {
            const modal = document.getElementById('flow-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function addFlowStep() {
            const step = {
                id: window.stepCounter++,
                device: 'dinamo_x',
                action: 'move',
                position: 50,
                speed: 128,
                pump: false,
                duration: 0
            };
            window.flowSteps.push(step);
            renderFlowSteps();
        }

        function removeFlowStep(index) {
            window.flowSteps.splice(index, 1);
            renderFlowSteps();
        }

        function updateStepField(index, field, value) {
            if (field === 'pump' || field === 'relay_state') {
                window.flowSteps[index][field] = value === 'true' || value === true;
            } else if (field === 'position' || field === 'speed' || field === 'servo_angle' || field === 'duration') {
                window.flowSteps[index][field] = parseInt(value) || 0;
            } else {
                window.flowSteps[index][field] = value;
            }
            renderFlowSteps();
        }

        function renderFlowSteps() {
            const container = document.getElementById('flow-canvas-container');
            const stepsJson = encodeURIComponent(JSON.stringify(window.flowSteps));

            fetch(`/jadwal/render-flow?steps=${stepsJson}`)
                .then(res => res.text())
                .then(html => {
                    container.innerHTML = html;
                    setTimeout(() => {
                        initializeDragSystem();
                        drawElectricLines();
                    }, 100);
                })
                .catch(err => {
                    console.error('Failed to render flow:', err);
                });
        }

        function saveFlow() {
            document.getElementById('automation_flow').value = JSON.stringify(window.flowSteps);
            updateFlowPreview();
            closeFlowBuilder();
        }

        function updateFlowPreview() {
            const preview = document.getElementById('flow-preview');
            const stepsContainer = document.getElementById('flow-steps');

            if (window.flowSteps.length === 0) {
                preview.classList.add('hidden');
                return;
            }

            preview.classList.remove('hidden');
            stepsContainer.innerHTML = '';

            window.flowSteps.forEach((step, index) => {
                let deviceName = '';
                let actionText = '';

                switch(step.device) {
                    case 'dinamo_x': deviceName = 'Dinamo X'; break;
                    case 'dinamo_y': deviceName = 'Dinamo Y'; break;
                    case 'relay_pump': deviceName = 'Pompa'; break;
                    case 'servo_nozzle': deviceName = 'Servo'; break;
                }

                if (step.device === 'dinamo_x' || step.device === 'dinamo_y') {
                    actionText = `Posisi ${step.position}%, Speed ${step.speed}, Pompa ${step.pump ? 'ON' : 'OFF'}`;
                } else if (step.device === 'servo_nozzle') {
                    actionText = `Sudut ${step.servo_angle}°`;
                } else if (step.device === 'relay_pump') {
                    actionText = `${step.relay_state ? 'ON' : 'OFF'}`;
                }

                if (step.duration > 0) {
                    actionText += ` (${step.duration}s)`;
                }

                const stepHtml = `
                    <div class="flex items-center gap-2 rounded bg-purple-50 px-3 py-2 text-sm dark:bg-purple-900/20">
                        <span class="font-semibold text-purple-700 dark:text-purple-400">${index + 1}.</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">${deviceName}:</span>
                        <span class="text-zinc-600 dark:text-zinc-400">${actionText}</span>
                    </div>
                `;
                stepsContainer.insertAdjacentHTML('beforeend', stepHtml);
            });
        }

        function loadJadwal(id) {
            if (!id) {
                resetForm();
                return;
            }

            fetch(`/jadwal/${id}`)
                .then(res => res.json())
                .then(data => {
                    const jadwal = data.data;
                    document.getElementById('form-title').textContent = 'Edit Jadwal: ' + jadwal.nama;
                    document.getElementById('jadwal-id').value = jadwal.id;
                    document.getElementById('nama').value = jadwal.nama || '';
                    document.getElementById('waktu_aktif_pertama').value = jadwal.waktu_aktif_pertama || '';
                    document.getElementById('waktu_aktif_kedua').value = jadwal.waktu_aktif_kedua || '';
                    document.getElementById('lama_operasi').value = jadwal.lama_operasi || 30;
                    document.getElementById('aktif').checked = jadwal.aktif;

                    // Load automation flow
                    if (jadwal.automation_flow) {
                        document.getElementById('automation_flow').value = JSON.stringify(jadwal.automation_flow);
                        window.flowSteps = jadwal.automation_flow;
                        window.stepCounter = window.flowSteps.length;
                        updateFlowPreview();
                    } else {
                        document.getElementById('automation_flow').value = '';
                        window.flowSteps = [];
                        window.stepCounter = 0;
                        document.getElementById('flow-preview').classList.add('hidden');
                    }

                    // Uncheck all hari first
                    document.querySelectorAll('input[name="hari[]"]').forEach(cb => cb.checked = false);
                    // Check selected hari
                    if (jadwal.hari) {
                        jadwal.hari.forEach(hari => {
                            const checkbox = document.getElementById(`hari-${hari}`);
                            if (checkbox) checkbox.checked = true;
                        });
                    }

                    // Show delete section and set delete form action
                    document.getElementById('delete-section').classList.remove('hidden');
                    document.getElementById('delete-form').action = `/jadwal/${jadwal.id}`;

                    // Scroll to form
                    document.getElementById('form-title').scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
        }

        function resetForm() {
            document.getElementById('form-title').textContent = 'Buat Jadwal Baru';
            document.getElementById('jadwal-id').value = '';
            document.getElementById('nama').value = '';
            document.getElementById('waktu_aktif_pertama').value = '';
            document.getElementById('waktu_aktif_kedua').value = '';
            document.getElementById('lama_operasi').value = '30';
            document.getElementById('aktif').checked = true;
            document.querySelectorAll('input[name="hari[]"]').forEach(cb => cb.checked = false);

            // Reset automation flow
            document.getElementById('automation_flow').value = '';
            window.flowSteps = [];
            window.stepCounter = 0;
            document.getElementById('flow-preview').classList.add('hidden');

            // Hide delete section
            document.getElementById('delete-section').classList.add('hidden');
        }

        function showDeleteModal() {
            const nama = document.getElementById('nama').value;
            document.getElementById('modal-jadwal-nama').textContent = nama;
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

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('delete-modal');
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    hideDeleteModal();
                }
            });
        });
    </script>
</x-layouts::app>
