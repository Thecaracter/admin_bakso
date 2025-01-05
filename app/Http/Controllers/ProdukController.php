<?php
namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::latest()->get();
        return view('pages.produk', compact('produk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        try {
            $namaFile = null;
            if ($request->hasFile('gambar')) {
                $gambar = $request->file('gambar');
                $namaFile = Str::slug($request->nama) . '.' . $gambar->getClientOriginalExtension();

                // Pastikan direktori 'fotoProduk' ada
                $destinationPath = public_path('fotoProduk');
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $gambar->move($destinationPath, $namaFile);
                // Tambahkan 'fotoProduk/' ke nama file untuk disimpan di database
                $namaFile = 'fotoProduk/' . $namaFile;
            }

            Produk::create([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'gambar' => $namaFile,
                'harga' => $request->harga,
                'stok' => $request->stok,
                'aktif' => true
            ]);

            return back()->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        try {
            $produk = Produk::findOrFail($id);

            if ($request->hasFile('gambar')) {
                // Hapus gambar lama jika ada
                if ($produk->gambar && file_exists(public_path($produk->gambar))) {
                    unlink(public_path($produk->gambar));
                }

                // Upload gambar baru
                $gambar = $request->file('gambar');
                $namaFile = Str::slug($request->nama) . '.' . $gambar->getClientOriginalExtension();

                // Pastikan direktori 'fotoProduk' ada
                $destinationPath = public_path('fotoProduk');
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $gambar->move($destinationPath, $namaFile);
                $produk->gambar = 'fotoProduk/' . $namaFile; // Simpan dengan path lengkap
            }

            $produk->update([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'harga' => $request->harga,
                'stok' => $request->stok,
            ]);

            return back()->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $produk = Produk::findOrFail($id);

            // Hapus gambar jika ada
            if ($produk->gambar && file_exists(public_path($produk->gambar))) {
                unlink(public_path($produk->gambar));
            }

            $produk->delete();

            return back()->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $produk = Produk::findOrFail($id);
            $produk->update(['aktif' => !$produk->aktif]);

            $status = $produk->aktif ? 'diaktifkan' : 'dinonaktifkan';
            return back()->with('success', "Produk berhasil {$status}!");
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}