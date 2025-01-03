{{-- resources/views/produk/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Daftar Menu')

@section('content')
    <!-- Alpine Store untuk handling edit -->
    <div x-data>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('produk', {
                    edit(item) {
                        const modal = document.querySelector('[x-ref="editModal"]');
                        const form = document.getElementById('formEdit');
                        const previewContainer = document.getElementById('previewContainerEdit');
                        const previewImage = previewContainer.querySelector('img');

                        // Update form action
                        form.action = `/produk/update/${item.id}`;

                        // Populate form fields
                        document.getElementById('editNama').value = item.nama;
                        document.getElementById('editDeskripsi').value = item.deskripsi;
                        document.getElementById('editHarga').value = item.harga;
                        document.getElementById('editStok').value = item.stok;

                        // Set current image preview
                        if (item.gambar) {
                            previewImage.src = `/fotoProduk/${item.gambar}`;
                            previewContainer.classList.remove('hidden');
                        }

                        modal.showModal();
                    }
                });
            });
        </script>

        <div class="bg-white/80 backdrop-blur-xl rounded-2xl p-6 shadow-xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 font-serif">Daftar Menu</h2>
                <button @click="$refs.tambahModal.showModal()"
                    class="bg-gradient-to-r from-amber-500 to-orange-500 text-white px-4 py-2 rounded-xl hover:shadow-lg hover:shadow-amber-500/30 transition-all duration-300 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Menu
                </button>
            </div>

            <!-- Tabel Produk -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-amber-50 text-gray-700">
                            <th class="px-6 py-3 text-left text-sm font-semibold rounded-l-lg">#</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Gambar</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Nama Menu</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Harga</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Stok</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-100">
                        @forelse ($produk as $item)
                            <tr class="hover:bg-amber-50/50 transition-colors duration-200">
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">
                                    <img src="{{ asset('fotoProduk/' . $item->gambar) }}" alt="{{ $item->nama }}"
                                        class="w-16 h-16 object-cover rounded-lg shadow-sm">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-800">{{ $item->nama }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($item->deskripsi, 50) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-800">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 text-sm rounded-full {{ $item->stok > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $item->stok }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('produk.toggle', $item->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $item->aktif ? 'bg-amber-500' : 'bg-gray-200' }}">
                                            <span
                                                class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform duration-200 ease-in-out {{ $item->aktif ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <button @click="$store.produk.edit({{ $item }})"
                                            class="p-2 rounded-lg bg-amber-100 text-amber-600 hover:bg-amber-200 transition-all duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('produk.destroy', $item->id) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition-all duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    Belum ada data menu
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Tambah -->

        <dialog id="tambahModal" x-ref="tambahModal"
            class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl backdrop:bg-gray-900/50">
            <form method="POST" action="{{ route('produk.store') }}" enctype="multipart/form-data"
                class="relative overflow-hidden" id="tambahForm">
                @csrf
                <!-- Header -->
                <div class="px-8 py-6 border-b bg-gradient-to-br from-amber-50 to-orange-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-gray-800">Tambah Menu Baru</h3>
                        <button type="button" @click="$refs.tambahModal.close()" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-8 space-y-6">
                    <div>
                        <label class="text-base font-semibold text-gray-700 mb-2 block">Nama Menu</label>
                        <input type="text" name="nama"
                            class="w-full h-12 px-4 rounded-xl border-2 border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200"
                            placeholder="Masukkan nama menu..." required>
                    </div>

                    <div>
                        <label class="text-base font-semibold text-gray-700 mb-2 block">Deskripsi Menu</label>
                        <textarea name="deskripsi" rows="4"
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200"
                            placeholder="Masukkan deskripsi menu..."></textarea>
                    </div>

                    <div>
                        <label class="text-base font-semibold text-gray-700 mb-2 block">Gambar Menu</label>
                        <div x-data="{
                            isHovered: false,
                            imageUrl: null,
                            handleFiles(files) {
                                if (files.length === 0) return;
                                const file = files[0];
                                if (!file.type.startsWith('image/')) return;
                        
                                // Update file input
                                this.$refs.fileInput.files = files;
                        
                                // Show preview
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    this.imageUrl = e.target.result;
                                };
                                reader.readAsDataURL(file);
                            }
                        }" class="mt-2">
                            <div @dragover.prevent="isHovered = true" @dragleave.prevent="isHovered = false"
                                @drop.prevent="isHovered = false; handleFiles($event.dataTransfer.files)"
                                :class="{ 'border-amber-500 bg-amber-50/50': isHovered }"
                                class="flex flex-col justify-center p-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-amber-500 transition-colors duration-200">

                                <!-- Preview Image -->
                                <div x-show="imageUrl" class="mb-4 text-center">
                                    <img :src="imageUrl" alt="Preview"
                                        class="mx-auto h-32 w-32 object-cover rounded-lg shadow-sm">
                                </div>

                                <!-- Upload Icon & Text -->
                                <div x-show="!imageUrl" class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>

                                <div class="mt-4 text-center">
                                    <div class="flex text-sm justify-center">
                                        <label
                                            class="relative cursor-pointer rounded-md font-medium text-amber-600 hover:text-amber-500">
                                            <span>Upload gambar</span>
                                            <input type="file" x-ref="fileInput" name="gambar" class="sr-only"
                                                accept="image/*" required @change="handleFiles($event.target.files)">
                                        </label>
                                        <p class="pl-1 text-gray-600">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2 text-center">PNG, JPG, JPEG hingga 2MB</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-base font-semibold text-gray-700 mb-2 block">Harga</label>
                            <div class="relative" x-data="{
                                price: '',
                                formatPrice() {
                                    const value = this.price.replace(/\D/g, '');
                                    this.price = value ? parseInt(value).toLocaleString('id-ID') : '';
                                    $refs.realPrice.value = value;
                                }
                            }">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-500">Rp</span>
                                </div>
                                <input type="text" x-model="price" @input="formatPrice()"
                                    class="w-full h-12 pl-12 pr-4 rounded-xl border-2 border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200"
                                    placeholder="0">
                                <input type="hidden" x-ref="realPrice" name="harga" required>
                            </div>
                        </div>

                        <div>
                            <label class="text-base font-semibold text-gray-700 mb-2 block">Stok</label>
                            <input type="number" name="stok"
                                class="w-full h-12 px-4 rounded-xl border-2 border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200"
                                placeholder="0" min="0" required>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-8 py-6 bg-gray-50 border-t flex justify-end space-x-3">
                    <button type="button" @click="$refs.tambahModal.close()"
                        class="px-6 py-3 text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 font-medium transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 text-white bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl hover:shadow-lg hover:shadow-amber-500/30 font-medium transition-all duration-200">
                        Simpan Menu
                    </button>
                </div>
            </form>
        </dialog>

        <!-- Modal Edit -->
        <dialog id="editModal" x-ref="editModal"
            class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl backdrop:bg-gray-900/50">
            <form method="POST" id="formEdit" enctype="multipart/form-data" class="relative overflow-hidden">
                @csrf
                @method('PUT')
                <!-- Header -->
                <div class="px-8 py-6 border-b bg-gradient-to-br from-amber-50 to-orange-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-gray-800">Edit Menu</h3>
                        <button type="button" @click="$refs.editModal.close()"
                            class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-8 space-y-6">
                    <div>
                        <label class="text-base font-semibold text-gray-700 mb-2 block">Nama Menu</label>
                        <input type="text" name="nama" id="editNama"
                            class="w-full h-12 px-4 rounded-xl border-2 border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200"
                            placeholder="Masukkan nama menu..." required>
                    </div>

                    <div>
                        <label class="text-base font-semibold text-gray-700 mb-2 block">Deskripsi Menu</label>
                        <textarea name="deskripsi" id="editDeskripsi" rows="4"
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200"
                            placeholder="Masukkan deskripsi menu..."></textarea>
                    </div>
                    <div>
                        <label class="text-base font-semibold text-gray-700 mb-2 block">Gambar Menu</label>
                        <div x-data="{
                            isHovered: false,
                            imageUrl: null,
                            handleFiles(files) {
                                if (files.length === 0) return;
                                const file = files[0];
                                if (!file.type.startsWith('image/')) return;
                        
                                this.$refs.fileInput.files = files;
                        
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    this.imageUrl = e.target.result;
                                    // Update preview image
                                    const previewImage = document.getElementById('previewContainerEdit').querySelector('img');
                                    previewImage.src = e.target.result;
                                };
                                reader.readAsDataURL(file);
                            }
                        }" class="mt-2">
                            <div @dragover.prevent="isHovered = true" @dragleave.prevent="isHovered = false"
                                @drop.prevent="isHovered = false; handleFiles($event.dataTransfer.files)"
                                :class="{ 'border-amber-500 bg-amber-50/50': isHovered }"
                                class="flex flex-col justify-center p-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-amber-500 transition-colors duration-200">

                                <!-- Container Preview -->
                                <div id="previewContainerEdit" class="text-center mb-4">
                                    <p class="text-sm text-gray-600 mb-2">Preview Gambar:</p>
                                    <img src="" alt="Preview"
                                        class="mx-auto h-32 w-32 object-cover rounded-lg shadow-sm">
                                </div>

                                <!-- Upload Area -->
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>

                                    <div class="mt-4 text-center">
                                        <div class="flex text-sm justify-center">
                                            <label
                                                class="relative cursor-pointer rounded-md font-medium text-amber-600 hover:text-amber-500">
                                                <span>Upload gambar baru</span>
                                                <input type="file" x-ref="fileInput" name="gambar" class="sr-only"
                                                    accept="image/*" @change="handleFiles($event.target.files)">
                                            </label>
                                            <p class="pl-1 text-gray-600">atau drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2 text-center">PNG, JPG, JPEG hingga 2MB</p>
                                        <p class="text-xs text-gray-500 mt-1 text-center">Kosongkan jika tidak ingin
                                            mengubah gambar</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="text-base font-semibold text-gray-700 mb-2 block">Harga</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-500">Rp</span>
                                </div>
                                <input type="number" name="harga" id="editHarga"
                                    class="w-full h-12 pl-12 pr-4 rounded-xl border-2 border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200"
                                    placeholder="0" min="0" required>
                            </div>
                        </div>

                        <div>
                            <label class="text-base font-semibold text-gray-700 mb-2 block">Stok</label>
                            <input type="number" name="stok" id="editStok"
                                class="w-full h-12 px-4 rounded-xl border-2 border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200"
                                placeholder="0" min="0" required>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-8 py-6 bg-gray-50 border-t flex justify-end space-x-3">
                    <button type="button" @click="$refs.editModal.close()"
                        class="px-6 py-3 text-gray-700 bg-white border-2 border-gray-300 rounded-xl hover:bg-gray-50 font-medium transition-colors duration-200">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-6 py-3 text-white bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl hover:shadow-lg hover:shadow-amber-500/30 font-medium transition-all duration-200">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </dialog>
    </div>

    @push('scripts')
        <script>
            // Fungsi untuk setup drag and drop
            function setupDropZone(dropZoneId, inputId, previewContainerId) {
                const dropZone = document.getElementById(dropZoneId);
                const input = document.getElementById(inputId);
                const previewContainer = document.getElementById(previewContainerId);
                const previewImage = previewContainer.querySelector('img');

                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                    });
                });

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.classList.add('border-amber-500', 'bg-amber-50/50');
                    });
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, () => {
                        dropZone.classList.remove('border-amber-500', 'bg-amber-50/50');
                    });
                });

                dropZone.addEventListener('drop', (e) => {
                    const file = e.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        input.files = e.dataTransfer.files;
                        showPreview(file, previewContainer, previewImage);
                    }
                });

                input.addEventListener('change', (e) => {
                    if (input.files && input.files[0]) {
                        showPreview(input.files[0], previewContainer, previewImage);
                    }
                });
            }

            // Fungsi untuk menampilkan preview gambar
            function showPreview(file, container, imgElement) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    imgElement.src = e.target.result;
                    container.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }

            // Setup dropzone untuk form tambah dan edit
            document.addEventListener('DOMContentLoaded', () => {
                setupDropZone('dropZoneTambah', 'gambarTambah', 'previewContainerTambah');
                setupDropZone('dropZoneEdit', 'gambarEdit', 'previewContainerEdit');

                // Reset form dan preview saat modal ditutup
                ['tambahModal', 'editModal'].forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    const form = modal.querySelector('form');
                    const previewContainer = modal.querySelector('[id^="previewContainer"]');

                    modal.addEventListener('close', () => {
                        form.reset();
                        previewContainer.classList.add('hidden');
                    });
                });
            });
        </script>
    @endpush
@endsection
