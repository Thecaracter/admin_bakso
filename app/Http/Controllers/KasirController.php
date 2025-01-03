<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KasirController extends Controller
{
    public function index()
    {
        $products = Produk::where('aktif', true)
            ->where('stok', '>', 0)
            ->get();

        return view('pages.kasir', compact('products'));
    }

    public function processSale(Request $request)
    {
        // Validasi input
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:produk,id',
            'items.*.quantity' => 'required|integer|min:1',
            'metode_pembayaran' => 'required|in:TUNAI,QRIS,BANK_TRANSFER'
        ]);

        // Generate nomor invoice
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . Str::random(5);

        // Hitung total
        $total = 0;
        foreach ($request->items as $item) {
            $product = Produk::findOrFail($item['id']);
            $total += $product->harga * $item['quantity'];
        }

        // Buat record penjualan
        $sale = Penjualan::create([
            'nomor_invoice' => $invoiceNumber,
            'user_id' => auth()->id(),
            'total_harga' => $total,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status' => 'pending'
        ]);

        // Buat detail penjualan
        foreach ($request->items as $item) {
            $product = Produk::findOrFail($item['id']);

            DetailPenjualan::create([
                'penjualan_id' => $sale->id,
                'produk_id' => $product->id,
                'jumlah' => $item['quantity'],
                'harga' => $product->harga,
                'subtotal' => $product->harga * $item['quantity']
            ]);

            // Update stok
            $product->decrement('stok', $item['quantity']);
        }

        // Update status jika pembayaran tunai
        if ($request->metode_pembayaran === 'TUNAI') {
            $sale->update(['status' => 'lunas']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Penjualan berhasil dibuat',
            'invoice' => $invoiceNumber
        ]);
    }
}