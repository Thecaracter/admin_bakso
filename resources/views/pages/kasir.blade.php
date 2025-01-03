@extends('layouts.app')

@section('title', 'Kasir')

@section('header', 'Kasir')

@section('content')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('kasir', () => ({
                products: @json($products),
                cart: [],
                search: '',
                metodePembayaran: '',
                uangTunai: '',
                showPaymentModal: false,

                get filteredProducts() {
                    return this.products.filter(product =>
                        product.nama.toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                get total() {
                    return this.cart.reduce((sum, item) => sum + (item.harga * item.quantity), 0);
                },

                get kembalian() {
                    if (!this.uangTunai) return null;
                    return this.uangTunai - this.total;
                },

                get canProcessPayment() {
                    if (this.cart.length === 0 || !this.metodePembayaran) return false;
                    if (this.metodePembayaran === 'TUNAI') {
                        return this.uangTunai >= this.total;
                    }
                    return true;
                },

                formatRupiah(amount) {
                    return 'Rp ' + amount.toLocaleString('id-ID');
                },

                addToCart(product) {
                    const existingItem = this.cart.find(item => item.id === product.id);
                    if (existingItem) {
                        this.updateQuantity(product.id, 1);
                    } else {
                        this.cart.push({
                            ...product,
                            quantity: 1
                        });
                    }
                },

                removeFromCart(productId) {
                    this.cart = this.cart.filter(item => item.id !== productId);
                    if (this.cart.length === 0) {
                        this.metodePembayaran = '';
                        this.uangTunai = '';
                    }
                },

                updateQuantity(productId, delta) {
                    const index = this.cart.findIndex(item => item.id === productId);
                    if (index > -1) {
                        const newQuantity = this.cart[index].quantity + delta;
                        if (newQuantity > 0) {
                            this.cart[index].quantity = newQuantity;
                        } else {
                            this.removeFromCart(productId);
                        }
                    }
                },

                processSale() {
                    if (this.metodePembayaran === 'TUNAI') {
                        this.confirmPayment();
                    } else {
                        this.showPaymentModal = true;
                    }
                },

                async confirmPayment() {
                    try {
                        const response = await fetch('/kasir/process-sale', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                items: this.cart.map(item => ({
                                    id: item.id,
                                    quantity: item.quantity
                                })),
                                metode_pembayaran: this.metodePembayaran,
                                uang_tunai: this.metodePembayaran === 'TUNAI' ? this
                                    .uangTunai : null
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.cart = [];
                            this.metodePembayaran = '';
                            this.uangTunai = '';
                            this.showPaymentModal = false;

                            alert(`Pembayaran berhasil!\nNomor Invoice: ${result.invoice}`);
                            window.location.reload();
                        }
                    } catch (error) {
                        alert('Terjadi kesalahan saat memproses pembayaran');
                        console.error('Error processing payment:', error);
                    }
                }
            }))
        });
    </script>

    <div x-data="kasir" class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-6">
            {{-- Products Section --}}
            <div class="w-full lg:w-2/3 space-y-4">
                <div class="sticky top-0 bg-white/80 backdrop-blur-sm p-4 rounded-lg shadow-sm z-10">
                    <input type="text" placeholder="Cari produk..."
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        x-model="search">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div
                            class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-shadow h-full flex flex-col">
                            <div class="p-4 space-y-4 flex-1 flex flex-col">
                                {{-- Image container dengan aspect ratio tetap --}}
                                <div class="relative pt-[75%] rounded-lg overflow-hidden bg-gray-100">
                                    <img :src="product.gambar" :alt="product.nama"
                                        class="absolute inset-0 w-full h-full object-cover">
                                </div>

                                {{-- Content container --}}
                                <div class="flex-1 flex flex-col justify-between space-y-4">
                                    <div>
                                        <h3 class="font-semibold text-gray-800 text-lg" x-text="product.nama"></h3>
                                        <p class="text-sm text-gray-600 mt-2 line-clamp-2" x-text="product.deskripsi"></p>
                                    </div>

                                    <div class="flex items-center justify-between mt-auto pt-4">
                                        <span class="font-bold text-amber-600 text-lg"
                                            x-text="formatRupiah(product.harga)"></span>
                                        <button @click="addToCart(product)"
                                            class="p-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Cart Section --}}
            <div class="w-full lg:w-1/3">
                <div class="sticky top-0 space-y-4">
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <div class="flex items-center space-x-2 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h2 class="text-lg font-semibold">Keranjang</h2>
                        </div>

                        <div class="space-y-3 max-h-[calc(100vh-20rem)] overflow-y-auto">
                            <template x-if="cart.length === 0">
                                <div class="p-4 bg-amber-50 text-amber-700 rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Keranjang masih kosong</span>
                                    </div>
                                </div>
                            </template>

                            <template x-for="item in cart" :key="item.id">
                                <div
                                    class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                                    <div class="flex items-center space-x-3">
                                        <div class="relative w-16 h-16 rounded-lg overflow-hidden bg-gray-100">
                                            <img :src="item.gambar" :alt="item.nama"
                                                class="absolute inset-0 w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-800" x-text="item.nama"></h4>
                                            <p class="text-sm text-gray-600" x-text="formatRupiah(item.harga)"></p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <button @click="updateQuantity(item.id, -1)"
                                            class="p-1 text-gray-500 hover:text-amber-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <span class="w-8 text-center font-medium" x-text="item.quantity"></span>
                                        <button @click="updateQuantity(item.id, 1)"
                                            class="p-1 text-gray-500 hover:text-amber-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                        <button @click="removeFromCart(item.id)"
                                            class="p-1 text-red-500 hover:text-red-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="flex justify-between mb-4">
                                <span class="text-lg font-medium text-gray-800">Total:</span>
                                <span class="text-xl font-bold text-amber-600" x-text="formatRupiah(total)"></span>
                            </div>

                            <div class="space-y-3">
                                <select x-model="metodePembayaran"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                    <option value="" disabled>Pilih Metode Pembayaran</option>
                                    <option value="TUNAI">Tunai</option>
                                    <option value="QRIS">QRIS</option>
                                    <option value="BANK_TRANSFER">Transfer Bank</option>
                                </select>

                                {{-- Form Uang Tunai akan muncul jika metode pembayaran adalah TUNAI --}}
                                <template x-if="metodePembayaran === 'TUNAI'">
                                    <div class="space-y-3">
                                        <input type="number" x-model="uangTunai"
                                            placeholder="Masukkan jumlah uang tunai"
                                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                        <template x-if="kembalian !== null">
                                            <div class="p-3 bg-amber-50 rounded-lg">
                                                <div class="flex justify-between items-center">
                                                    <span class="text-amber-700 font-medium">Kembalian:</span>
                                                    <span class="font-bold text-amber-700"
                                                        x-text="formatRupiah(kembalian)"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <button @click="processSale" :disabled="!canProcessPayment"
                                    class="w-full py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed hover:from-amber-600 hover:to-orange-600 transition-colors flex items-center justify-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>Proses Pembayaran</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Konfirmasi Modal untuk QRIS dan Transfer Bank --}}
        <template x-if="showPaymentModal">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg p-6 max-w-md w-full space-y-4 relative">
                    <h3 class="text-xl font-semibold text-gray-900">Konfirmasi Pembayaran</h3>

                    <div class="p-4 bg-amber-50 rounded-lg">
                        <p class="text-amber-700 font-medium">Total Pembayaran:</p>
                        <p class="text-2xl font-bold text-amber-600" x-text="formatRupiah(total)"></p>
                    </div>

                    <div class="space-y-2">
                        <template x-if="metodePembayaran === 'QRIS'">
                            <div class="text-center space-y-4">
                                <p class="text-gray-600">Silakan scan QR Code berikut untuk melakukan pembayaran.</p>
                                <div class="bg-gray-100 w-48 h-48 mx-auto rounded-lg flex items-center justify-center">
                                    <span class="text-gray-400">QR Code</span>
                                </div>
                            </div>
                        </template>

                        <template x-if="metodePembayaran === 'BANK_TRANSFER'">
                            <div class="space-y-4">
                                <p class="text-gray-600">Silakan transfer ke rekening berikut:</p>
                                <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Bank</span>
                                        <span class="font-medium">BCA</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">No. Rekening</span>
                                        <span class="font-medium">1234567890</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Atas Nama</span>
                                        <span class="font-medium">Bakso Boled</span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                        <button @click="showPaymentModal = false"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                            Batal
                        </button>
                        <button @click="confirmPayment"
                            class="px-6 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-lg font-medium hover:from-amber-600 hover:to-orange-600 transition-colors">
                            Konfirmasi Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection
