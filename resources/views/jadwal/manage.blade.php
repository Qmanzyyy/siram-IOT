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

