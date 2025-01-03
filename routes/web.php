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
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// routes/web.php
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

//Dashboard Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

//Produk Routes
Route::prefix('produk')->group(function () {
    Route::get('/', [ProdukController::class, 'index'])->name('produk.index');
    Route::post('/store', [ProdukController::class, 'store'])->name('produk.store');
    Route::put('/update/{id}', [ProdukController::class, 'update'])->name('produk.update');
    Route::delete('/delete/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');
    Route::post('/toggle/{id}', [ProdukController::class, 'toggleStatus'])->name('produk.toggle');
});

Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
Route::post('/kasir/process-sale', [KasirController::class, 'processSale'])->name('kasir.process-sale');


