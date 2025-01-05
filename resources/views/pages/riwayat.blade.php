@extends('layouts.app')

@section('title', 'Riwayat Penjualan')

@section('header', 'Riwayat Penjualan')

@section('content')
    {{-- Main wrapper with Alpine data --}}
    <div x-data="{
        showModal: false,
        selectedItem: null,
        async showDetail(id) {
            this.showModal = true;
            const response = await fetch(`/riwayat/${id}`);
            const data = await response.json();
            this.selectedItem = data;
        }
    }">
        {{-- Main content container --}}
        <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-2xl p-4 md:p-8">
            {{-- Filter & Actions --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                <form action="{{ route('riwayat.index') }}" method="GET" class="flex flex-wrap gap-4">
                    <div class="flex items-center gap-2">
                        <label for="start_date" class="text-sm font-medium text-gray-700">Dari:</label>
                        <input type="date" name="start_date" id="start_date"
                            value="{{ request('start_date', now()->startOfWeek()->format('Y-m-d')) }}"
                            class="rounded-lg border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200">
                    </div>
                    <div class="flex items-center gap-2">
                        <label for="end_date" class="text-sm font-medium text-gray-700">Sampai:</label>
                        <input type="date" name="end_date" id="end_date"
                            value="{{ request('end_date', now()->endOfWeek()->format('Y-m-d')) }}"
                            class="rounded-lg border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200">
                    </div>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Filter
                    </button>
                </form>

                <div class="flex gap-2">
                    <a href="{{ route('riwayat.print') }}?start_date={{ request('start_date', now()->startOfWeek()->format('Y-m-d')) }}&end_date={{ request('end_date', now()->endOfWeek()->format('Y-m-d')) }}"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print
                    </a>

                    <a href="{{ route('riwayat.export') }}?start_date={{ request('start_date', now()->startOfWeek()->format('Y-m-d')) }}&end_date={{ request('end_date', now()->endOfWeek()->format('Y-m-d')) }}"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-amber-50">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">No. Invoice</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Tanggal</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Kasir</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Metode Pembayaran</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">Total</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Status</th>
                            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($riwayat as $item)
                            <tr class="hover:bg-amber-50/50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-700">{{ $item->nomor_invoice }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $item->user->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $item->metode_pembayaran }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-right font-medium">
                                    Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if ($item->status === 'pending')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @elseif ($item->status === 'lunas')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Lunas
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Batal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button @click="showDetail({{ $item->id }})"
                                        class="inline-flex items-center px-3 py-1 bg-amber-500 text-white text-xs font-medium rounded-lg hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-1 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-3"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                        </svg>
                                        <p class="text-gray-600 mb-1">Belum ada data riwayat</p>
                                        <p class="text-gray-500 text-xs">Coba ubah filter tanggal untuk melihat data
                                            lainnya
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($riwayat->isNotEmpty())
                        <tfoot>
                            <tr class="bg-amber-50">
                                <td colspan="4" class="px-4 py-3 text-sm font-semibold text-gray-600 text-right">
                                    Total Penjualan:</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-700 text-right">
                                    Rp {{ number_format($riwayat->sum('total_harga'), 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Modal (Fixed positioning for full screen overlay) --}}
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>

            {{-- Modal Container --}}
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-screen items-center justify-center p-4">
                    <div class="relative w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        @click.outside="showModal = false">

                        {{-- Loading State --}}
                        <template x-if="!selectedItem">
                            <div class="flex justify-center items-center h-64">
                                <div
                                    class="animate-spin rounded-full h-12 w-12 border-4 border-amber-500 border-t-transparent">
                                </div>
                            </div>
                        </template>

                        {{-- Modal Content when data is loaded --}}
                        <template x-if="selectedItem">
                            <div>
                                {{-- Modal Header --}}
                                <div class="border-b border-gray-200 px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="h-12 w-12 rounded-xl bg-amber-100 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-amber-600" xmlns="http://www.w3.svg"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900"
                                                    x-text="'Invoice #' + selectedItem.nomor_invoice"></h3>
                                                <p class="text-sm text-gray-500"
                                                    x-text="new Date(selectedItem.created_at).toLocaleString('id-ID', {
                                                        weekday: 'long',
                                                        year: 'numeric',
                                                        month: 'long',
                                                        day: 'numeric',
                                                        hour: '2-digit',
                                                        minute: '2-digit'
                                                    })">
                                                </p>
                                            </div>
                                        </div>
                                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                                            <span class="sr-only">Close</span>
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Modal Body --}}
                                <div class="p-6">
                                    {{-- Info Cards --}}
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                        {{-- Status Card --}}
                                        <div class="bg-gray-50 rounded-xl p-4">
                                            <p class="text-sm font-medium text-gray-500 mb-2">Status Pembayaran</p>
                                            <div>
                                                <template x-if="selectedItem.status === 'pending'">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-yellow-400"
                                                            fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        Pending
                                                    </span>
                                                </template>
                                                <template x-if="selectedItem.status === 'lunas'">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400"
                                                            fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        Lunas
                                                    </span>
                                                </template>
                                                <template x-if="selectedItem.status === 'batal'">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-red-400"
                                                            fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3" />
                                                        </svg>
                                                        Batal
                                                    </span>
                                                </template>
                                            </div>
                                        </div>

                                        {{-- Payment Method Card --}}
                                        <div class="bg-gray-50 rounded-xl p-4">
                                            <p class="text-sm font-medium text-gray-500 mb-2">Metode Pembayaran</p>
                                            <div class="flex items-center gap-2">
                                                <template
                                                    x-if="selectedItem.metode_pembayaran.toLowerCase().includes('cash')">
                                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                </template>
                                                <template
                                                    x-if="!selectedItem.metode_pembayaran.toLowerCase().includes('cash')">
                                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                    </svg>
                                                </template>
                                                <span class="text-sm font-medium text-gray-900"
                                                    x-text="selectedItem.metode_pembayaran"></span>
                                            </div>
                                            <template x-if="selectedItem.payment_reference">
                                                <p class="mt-1 text-xs text-gray-500">
                                                    Ref: <span class="font-medium"
                                                        x-text="selectedItem.payment_reference"></span>
                                                </p>
                                            </template>
                                        </div>

                                        {{-- Cashier Card --}}
                                        <div class="bg-gray-50 rounded-xl p-4">
                                            <p class="text-sm font-medium text-gray-500 mb-2">Kasir</p>
                                            <div class="flex items-center gap-2">
                                                <span
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-amber-100">
                                                    <span class="text-sm font-medium text-amber-600"
                                                        x-text="selectedItem.user.name.charAt(0)"></span>
                                                </span>
                                                <span class="text-sm font-medium text-gray-900"
                                                    x-text="selectedItem.user.name"></span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Order Details --}}
                                    <div class="rounded-xl border border-gray-200">
                                        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                                            <h4 class="font-medium text-gray-900">Detail Pesanan</h4>
                                        </div>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th
                                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                            Produk</th>
                                                        <th
                                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                                            Jumlah</th>
                                                        <th
                                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                                            Harga</th>
                                                        <th
                                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                                            Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 bg-white">
                                                    <template x-for="item in selectedItem.detail" :key="item.id">
                                                        <tr>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <div class="flex items-center gap-3">
                                                                    <span
                                                                        class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100">
                                                                        <span class="text-lg font-medium text-amber-600"
                                                                            x-text="item.produk.nama.charAt(0).toUpperCase()"></span>
                                                                    </span>
                                                                    <div>
                                                                        <div class="text-sm font-medium text-gray-900"
                                                                            x-text="item.produk.nama"></div>
                                                                        <div x-show="item.produk.deskripsi"
                                                                            class="text-sm text-gray-500"
                                                                            x-text="item.produk.deskripsi"></div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                                <span
                                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                                                    x-text="item.jumlah"></span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500"
                                                                x-text="'Rp ' + Number(item.harga).toLocaleString('id-ID')">
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900"
                                                                x-text="'Rp ' + Number(item.subtotal).toLocaleString('id-ID')">
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                                <tfoot class="bg-gray-50">
                                                    <tr>
                                                        <th scope="row" colspan="3"
                                                            class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                                            Total</th>
                                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900"
                                                            x-text="'Rp ' + Number(selectedItem.total_harga).toLocaleString('id-ID')">
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Footer --}}
                                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                                    <div class="flex justify-end">
                                        <button type="button" @click="showModal = false"
                                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                                            Tutup
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
