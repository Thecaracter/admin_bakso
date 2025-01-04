<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
});

// Midtrans Callback (No Auth Required)
Route::post('/midtrans/callback', [KasirController::class, 'midtransCallback'])
    ->name('midtrans.callback');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Produk Routes
    Route::prefix('produk')->group(function () {
        Route::get('/', [ProdukController::class, 'index'])->name('produk.index');
        Route::post('/store', [ProdukController::class, 'store'])->name('produk.store');
        Route::put('/update/{id}', [ProdukController::class, 'update'])->name('produk.update');
        Route::delete('/delete/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
        Route::post('/toggle/{id}', [ProdukController::class, 'toggleStatus'])->name('produk.toggle');
    });

    // Kasir Routes
    Route::prefix('kasir')->group(function () {
        Route::get('/', [KasirController::class, 'index'])->name('kasir.index');
        Route::post('/process-sale', [KasirController::class, 'processSale'])->name('kasir.process-sale');
        Route::get('/check-status/{invoice}', [KasirController::class, 'checkStatus'])->name('kasir.check-status');
        Route::post('/payment-success', [KasirController::class, 'paymentSuccess'])->name('kasir.payment-success');
    });

    // Dummy Routes untuk fitur yang belum diimplementasi
    Route::get('/transaksi', function () {
        return view('pages.dummy', [
            'title' => 'Riwayat Transaksi',
            'message' => 'Halaman Riwayat Transaksi sedang dalam pengembangan'
        ]);
    })->name('transaksi.index');

    Route::prefix('laporan')->group(function () {
        Route::get('/penjualan', function () {
            return view('pages.dummy', [
                'title' => 'Laporan Penjualan',
                'message' => 'Halaman Laporan Penjualan sedang dalam pengembangan'
            ]);
        })->name('laporan.penjualan');

        Route::get('/laba-rugi', function () {
            return view('pages.dummy', [
                'title' => 'Laporan Laba Rugi',
                'message' => 'Halaman Laporan Laba Rugi sedang dalam pengembangan'
            ]);
        })->name('laporan.laba-rugi');
    });
});