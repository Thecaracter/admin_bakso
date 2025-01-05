<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\DetailPenjualan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Menentukan range tanggal berdasarkan filter
        $dateRange = $request->input('date_range', 'today');

        switch ($dateRange) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'custom':
                $startDate = $request->filled('start_date')
                    ? Carbon::parse($request->start_date)->startOfDay()
                    : Carbon::today();
                $endDate = $request->filled('end_date')
                    ? Carbon::parse($request->end_date)->endOfDay()
                    : Carbon::today()->endOfDay();
                break;
            default:
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
        }

        // Query dasar untuk penjualan dalam periode
        $query = Penjualan::where('status', 'lunas')
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Statistik Utama dengan single query
        $statistics = $query->select([
            DB::raw('COUNT(*) as total_penjualan'),
            DB::raw('SUM(total_harga) as total_pendapatan'),
            DB::raw('AVG(total_harga) as rata_rata')
        ])->first();

        $totalPenjualan = $statistics->total_penjualan;
        $totalPendapatan = $statistics->total_pendapatan ?? 0;
        $averageTransaction = $statistics->rata_rata ?? 0;

        // Data untuk grafik penjualan (fixed)
        $salesChart = DB::table('penjualan')
            ->where('status', 'lunas')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_harga) as total_sales')
            ])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get();

        // Produk Terlaris (optimized with joins)
        $topProducts = DetailPenjualan::select(
            'produk.nama',
            DB::raw('SUM(detail_penjualan.jumlah) as total_quantity'),
            DB::raw('SUM(detail_penjualan.subtotal) as total_revenue')
        )
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->join('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->where('penjualan.status', 'lunas')
            ->whereBetween('penjualan.created_at', [$startDate, $endDate])
            ->groupBy('produk.id', 'produk.nama')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Metode Pembayaran dengan persentase
        $paymentMethods = DB::table('penjualan')
            ->where('status', 'lunas')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select([
                'metode_pembayaran',
                DB::raw('COUNT(*) as total'),
                DB::raw('ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM penjualan WHERE status = "lunas" AND created_at BETWEEN ? AND ?), 2) as percentage')
            ])
            ->groupBy('metode_pembayaran')
            ->orderByDesc('total')
            ->setBindings(array_merge($query->getBindings(), [$startDate, $endDate]))
            ->get();

        // Statistik tambahan
        $productCount = Produk::where('aktif', true)->count();
        $averageItemsPerTransaction = DetailPenjualan::whereHas('penjualan', function ($q) use ($startDate, $endDate) {
            $q->where('status', 'lunas')
                ->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->avg('jumlah') ?? 0;

        return view('pages.dashboard', compact(
            'dateRange',
            'startDate',
            'endDate',
            'totalPenjualan',
            'totalPendapatan',
            'averageTransaction',
            'salesChart',
            'topProducts',
            'paymentMethods',
            'productCount',
            'averageItemsPerTransaction'
        ));
    }
}