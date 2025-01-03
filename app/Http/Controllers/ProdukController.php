<?php
namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            if ($request->hasFile('gambar')) {
                $gambar = $request->file('gambar');
                $namaFile = time() . '_' . Str::slug($request->nama) . '.' . $gambar->getClientOriginalExtension();
                $gambar->move(public_path('fotoProduk'), $namaFile);
            }

            Produk::create([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'gambar' => $namaFile ?? null,
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
                // Hapus gambar lama
                if ($produk->gambar && file_exists(public_path('fotoProduk/' . $produk->gambar))) {
                    unlink(public_path('fotoProduk/' . $produk->gambar));
                }

                // Upload gambar baru
                $gambar = $request->file('gambar');
                $namaFile = time() . '_' . Str::slug($request->nama) . '.' . $gambar->getClientOriginalExtension();
                $gambar->move(public_path('fotoProduk'), $namaFile);

                $produk->gambar = $namaFile;
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
            if ($produk->gambar && file_exists(public_path('fotoProduk/' . $produk->gambar))) {
                unlink(public_path('fotoProduk/' . $produk->gambar));
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