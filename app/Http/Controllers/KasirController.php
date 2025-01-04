<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Exception;

class KasirController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function index()
    {
        try {
            $products = Produk::where('aktif', true)
                ->where('stok', '>', 0)
                ->get();

            return view('pages.kasir', compact('products'));
        } catch (Exception $e) {
            Log::error('Error in KasirController@index: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->with('error', 'Terjadi kesalahan saat memuat data produk');
        }
    }

    public function processSale(Request $request)
    {
        try {
            DB::beginTransaction();

            Log::info('Incoming sale request:', [
                'items' => $request->items,
                'metode_pembayaran' => $request->metode_pembayaran
            ]);

            // Validasi input
            $validated = $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'required|exists:produk,id',
                'items.*.quantity' => 'required|integer|min:1',
                'metode_pembayaran' => 'required|string'
            ]);

            // Generate nomor invoice
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . Str::random(5);

            // Hitung total dan validasi stok
            $total = 0;
            $orderItems = [];
            foreach ($request->items as $item) {
                $product = Produk::lockForUpdate()->findOrFail($item['id']);

                if ($product->stok < $item['quantity']) {
                    throw new Exception("Stok tidak mencukupi untuk produk {$product->nama}");
                }

                $total += $product->harga * $item['quantity'];
                $orderItems[] = [
                    'id' => strval($product->id),
                    'price' => (int) $product->harga,
                    'quantity' => $item['quantity'],
                    'name' => $product->nama
                ];
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
                $product = Produk::find($item['id']);
                DetailPenjualan::create([
                    'penjualan_id' => $sale->id,
                    'produk_id' => $item['id'],
                    'jumlah' => $item['quantity'],
                    'harga' => $product->harga,
                    'subtotal' => $product->harga * $item['quantity']
                ]);

                $product->decrement('stok', $item['quantity']);
            }

            // Jika pembayaran online
            if ($request->metode_pembayaran !== 'TUNAI') {
                $params = [
                    'transaction_details' => [
                        'order_id' => $invoiceNumber,
                        'gross_amount' => (int) $total,
                    ],
                    'item_details' => $orderItems,
                    'customer_details' => [
                        'first_name' => auth()->user()->name,
                        'email' => auth()->user()->email,
                    ],
                    'callbacks' => [
                        'finish' => route('kasir.index'),
                        'error' => route('kasir.index'),
                        'cancel' => route('kasir.index')
                    ]
                ];

                $snapToken = Snap::getSnapToken($params);
                $sale->update(['payment_reference' => $snapToken]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'snap_token' => $snapToken,
                    'invoice' => $invoiceNumber
                ]);
            }

            // Update status jika pembayaran tunai
            $sale->update(['status' => 'lunas']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Penjualan berhasil dibuat',
                'invoice' => $invoiceNumber
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in KasirController@processSale: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses penjualan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function midtransCallback(Request $request)
    {
        try {
            $notif = new \Midtrans\Notification();

            $transaction_status = $notif->transaction_status;
            $payment_type = $notif->payment_type;
            $order_id = $notif->order_id;
            $fraud_status = $notif->fraud_status;
            $transaction_id = $notif->transaction_id;
            $va_number = $notif->va_numbers[0]->va_number ?? null;
            $bank = $notif->va_numbers[0]->bank ?? null;
            $payment_code = $notif->payment_code ?? null;
            $bill_key = $notif->bill_key ?? null;
            $biller_code = $notif->biller_code ?? null;

            Log::info('Midtrans callback received:', [
                'order_id' => $order_id,
                'transaction_status' => $transaction_status,
                'payment_type' => $payment_type,
                'fraud_status' => $fraud_status,
                'transaction_id' => $transaction_id,
                'va_number' => $va_number,
                'bank' => $bank,
                'payment_code' => $payment_code,
                'bill_key' => $bill_key,
                'biller_code' => $biller_code
            ]);

            $penjualan = Penjualan::where('nomor_invoice', $order_id)->first();

            if (!$penjualan) {
                throw new Exception('Transaksi tidak ditemukan');
            }

            // Format metode pembayaran dengan detail lengkap
            $payment_method = match ($payment_type) {
                'credit_card' => 'Kartu Kredit',
                'bank_transfer' => $bank ? "Transfer Bank {$bank}" . ($va_number ? " (VA: {$va_number})" : "") : 'Transfer Bank',
                'echannel' => "Mandiri Bill (Bill Key: {$bill_key}, Biller Code: {$biller_code})",
                'gopay' => 'GoPay',
                'shopeepay' => 'ShopeePay',
                'qris' => 'QRIS',
                'cstore' => $payment_code ? "Convenience Store (Kode: {$payment_code})" : 'Convenience Store',
                default => ucfirst(str_replace('_', ' ', $payment_type))
            };

            switch ($transaction_status) {
                case 'capture':
                    if ($payment_type == 'credit_card') {
                        if ($fraud_status == 'challenge') {
                            $status = 'pending';
                        } else {
                            $status = 'lunas';
                        }
                    }
                    break;
                case 'settlement':
                    $status = 'lunas';
                    break;
                case 'pending':
                    $status = 'pending';
                    break;
                case 'deny':
                case 'expire':
                case 'cancel':
                    $status = 'batal';
                    break;
                default:
                    $status = 'pending';
                    break;
            }

            $penjualan->update([
                'status' => $status,
                'payment_reference' => $transaction_id,
                'metode_pembayaran' => $payment_method
            ]);

            // Jika pembayaran dibatalkan, kembalikan stok
            if ($status === 'batal') {
                foreach ($penjualan->detailPenjualan as $detail) {
                    $detail->produk->increment('stok', $detail->jumlah);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Callback processed successfully'
            ]);

        } catch (Exception $e) {
            Log::error('Error in midtransCallback: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function paymentSuccess(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'order_id' => 'required|string',
                'payment_type' => 'required|string',
                'transaction_id' => 'required|string',
                'va_number' => 'nullable|string',
                'bank' => 'nullable|string',
                'payment_code' => 'nullable|string',
                'bill_key' => 'nullable|string',
                'biller_code' => 'nullable|string'
            ]);

            $penjualan = Penjualan::where('nomor_invoice', $request->order_id)->firstOrFail();

            $payment_method = match ($request->payment_type) {
                'credit_card' => 'Kartu Kredit',
                'bank_transfer' => $request->bank ?
                "Transfer Bank {$request->bank}" . ($request->va_number ? " (VA: {$request->va_number})" : "") :
                'Transfer Bank',
                'echannel' => "Mandiri Bill (Bill Key: {$request->bill_key}, Biller Code: {$request->biller_code})",
                'gopay' => 'GoPay',
                'shopeepay' => 'ShopeePay',
                'qris' => 'QRIS',
                'cstore' => $request->payment_code ?
                "Convenience Store (Kode: {$request->payment_code})" :
                'Convenience Store',
                default => ucfirst(str_replace('_', ' ', $request->payment_type))
            };

            $penjualan->update([
                'status' => 'lunas',
                'payment_reference' => $request->transaction_id,
                'metode_pembayaran' => $payment_method
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'payment_method' => $payment_method,
                'message' => 'Payment status updated successfully'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error in paymentSuccess: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function checkStatus($invoice)
    {
        try {
            $penjualan = Penjualan::where('nomor_invoice', $invoice)->firstOrFail();

            return response()->json([
                'success' => true,
                'status' => $penjualan->status,
                'payment_method' => $penjualan->metode_pembayaran,
                'reference' => $penjualan->payment_reference
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }
    }
}