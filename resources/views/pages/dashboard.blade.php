@extends('layouts.app')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')
    <div class="space-y-6">
        {{-- Filter --}}
        <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-2xl p-4">
            <form action="{{ route('dashboard') }}" method="GET" class="space-y-4">
                <div class="flex flex-wrap items-end gap-4">
                    <div>
                        <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                        <select name="date_range" id="date_range"
                            class="rounded-lg border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200"
                            onchange="toggleCustomDate(this.value)">
                            <option value="today" {{ request('date_range', 'today') === 'today' ? 'selected' : '' }}>Hari
                                Ini</option>
                            <option value="week" {{ request('date_range') === 'week' ? 'selected' : '' }}>Minggu Ini
                            </option>
                            <option value="month" {{ request('date_range') === 'month' ? 'selected' : '' }}>Bulan Ini
                            </option>
                            <option value="custom" {{ request('date_range') === 'custom' ? 'selected' : '' }}>Kustom
                            </option>
                        </select>
                    </div>

                    <div id="custom_date" class="flex gap-4"
                        style="{{ request('date_range') === 'custom' ? '' : 'display: none;' }}">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Dari</label>
                            <input type="date" name="start_date" id="start_date"
                                value="{{ request('start_date', now()->format('Y-m-d')) }}"
                                class="rounded-lg border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai</label>
                            <input type="date" name="end_date" id="end_date"
                                value="{{ request('end_date', now()->format('Y-m-d')) }}"
                                class="rounded-lg border-gray-300 focus:border-amber-500 focus:ring focus:ring-amber-200">
                        </div>
                    </div>

                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Total Penjualan --}}
            <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-2xl p-6 hover:scale-105 transition-transform">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-amber-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Penjualan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalPenjualan }}</p>
                        <p class="text-sm text-gray-500">Transaksi</p>
                    </div>
                </div>
            </div>

            {{-- Total Pendapatan --}}
            <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-2xl p-6 hover:scale-105 transition-transform">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-green-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-gray-500">Periode ini</p>
                    </div>
                </div>
            </div>

            {{-- Rata-rata Transaksi --}}
            <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-2xl p-6 hover:scale-105 transition-transform">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Rata-rata Transaksi</p>
                        <p class="text-2xl font-bold text-gray-900">Rp
                            {{ number_format($averageTransaction, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500">Per transaksi</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Sales Chart --}}
            <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-2xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Grafik Penjualan</h3>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span class="text-sm text-gray-600">Pendapatan</span>
                        <span class="w-3 h-3 rounded-full bg-blue-500 ml-3"></span>
                        <span class="text-sm text-gray-600">Transaksi</span>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            {{-- Payment Methods Chart --}}
            <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-2xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Metode Pembayaran</h3>
                </div>
                <div class="h-80">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Top Products --}}
        <div class="bg-white/80 backdrop-blur-xl shadow-xl rounded-2xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Produk Terlaris</h3>
                <span class="text-sm text-gray-500">Top 5 produk dengan penjualan tertinggi</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 rounded-l-lg">
                                Produk</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">
                                Jumlah Terjual</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 rounded-r-lg">
                                Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($topProducts as $product)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-lg bg-amber-100 flex items-center justify-center">
                                            <span
                                                class="text-lg font-medium text-amber-600">{{ substr($product->nama, 0, 1) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $product->nama }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        {{ number_format($product->total_quantity, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                    Rp {{ number_format($product->total_revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="text-gray-600 mb-1">Belum ada data penjualan</p>
                                        <p class="text-gray-500 text-xs">Coba ubah filter tanggal untuk melihat data
                                            lainnya</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            function toggleCustomDate(value) {
                const customDateDiv = document.getElementById('custom_date');
                customDateDiv.style.display = value === 'custom' ? 'flex' : 'none';
            }

            function formatRupiah(number) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
            }

            // Format tanggal ke Indonesia
            function formatDate(dateString) {
                const options = {
                    day: 'numeric',
                    month: 'short'
                };
                return new Date(dateString).toLocaleDateString('id-ID', options);
            }

            // Data untuk chart
            const salesData = {!! json_encode($salesChart) !!};
            const dates = salesData.map(item => formatDate(item.date));
            const sales = salesData.map(item => item.total_sales);
            const orders = salesData.map(item => item.total_orders);

            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                            label: 'Pendapatan',
                            data: sales,
                            borderColor: '#F59E0B',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y-axis-1'
                        },
                        {
                            label: 'Jumlah Transaksi',
                            data: orders,
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y-axis-2'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        'y-axis-1': {
                            position: 'left',
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return formatRupiah(value);
                                },
                                font: {
                                    size: 11
                                }
                            }
                        },
                        'y-axis-2': {
                            position: 'right',
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#1F2937',
                            titleFont: {
                                size: 13,
                                weight: 'bold'
                            },
                            bodyColor: '#4B5563',
                            bodyFont: {
                                size: 12
                            },
                            borderColor: '#E5E7EB',
                            borderWidth: 1,
                            padding: 12,
                            boxPadding: 4,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.datasetIndex === 0) {
                                        label += formatRupiah(context.raw);
                                    } else {
                                        label += context.raw + ' transaksi';
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // Payment Methods Chart
            const paymentData = {!! json_encode($paymentMethods) !!};
            const paymentCtx = document.getElementById('paymentChart').getContext('2d');
            new Chart(paymentCtx, {
                type: 'doughnut',
                data: {
                    labels: paymentData.map(item => item.metode_pembayaran),
                    datasets: [{
                        data: paymentData.map(item => item.total),
                        backgroundColor: [
                            '#F59E0B',
                            '#3B82F6',
                            '#10B981',
                            '#6366F1',
                            '#EC4899'
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#1F2937',
                            bodyColor: '#4B5563',
                            borderColor: '#E5E7EB',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection
