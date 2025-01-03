<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProdukSeeeder extends Seeder
{
    private function downloadImage($url, $filename)
    {
        $directory = public_path('fotoProduk');

        // Buat direktori jika belum ada
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0777, true);
        }

        $filepath = $directory . '/' . $filename;

        // Download gambar
        try {
            $response = Http::get($url);
            if ($response->successful()) {
                File::put($filepath, $response->body());
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function run()
    {
        $products = [
            [
                'nama' => 'Bakso Original',
                'deskripsi' => 'Bakso daging sapi pilihan dengan kuah kaldu sapi yang gurih. Disajikan dengan mie kuning, bihun, dan sayuran segar.',
                'harga' => 15000,
                'stok' => 100,
                'gambar_url' => 'https://assets.pikiran-rakyat.com/crop/0x0:0x0/x/photo/2021/09/30/1963197229.jpg',
                'gambar' => 'fotoProduk/bakso-original.jpg',
                'aktif' => true
            ],
            [
                'nama' => 'Bakso Urat',
                'deskripsi' => 'Bakso dengan tambahan urat sapi yang kenyal, disajikan dengan kuah kaldu sapi. Cocok untuk pencinta tekstur bakso yang lebih bervariasi.',
                'harga' => 18000,
                'stok' => 100,
                'gambar_url' => 'https://img.kurio.network/ZiE4tqVds1GjHHWvL0GJw8nPR7A=/1200x900/filters:quality(80)/https://kurio-img.kurioapps.com/21/02/22/8e8ed492-1686-4472-95be-fc19e52baba1.jpe',
                'gambar' => 'fotoProduk/bakso-urat.jpg',
                'aktif' => true
            ],
            [
                'nama' => 'Mie Bakso Special',
                'deskripsi' => 'Mie bakso dengan topping lengkap termasuk bakso besar, bakso urat, tahu bakso, dan pangsit. Disajikan dengan kuah kaldu sapi premium.',
                'harga' => 25000,
                'stok' => 50,
                'gambar_url' => 'https://assets.pikiran-rakyat.com/crop/0x0:0x0/x/photo/2021/09/07/1055334839.jpg',
                'gambar' => 'fotoProduk/mie-bakso-special.jpg',
                'aktif' => true
            ]
        ];

        foreach ($products as $product) {
            // Download gambar
            echo "Downloading image for {$product['nama']}...\n";
            $downloaded = $this->downloadImage($product['gambar_url'], basename($product['gambar']));

            if (!$downloaded) {
                echo "Failed to download image for {$product['nama']}\n";
                continue;
            }

            // Hapus gambar_url dari data yang akan disimpan ke database
            unset($product['gambar_url']);

            // Buat produk
            Produk::create($product);
            echo "Created product: {$product['nama']}\n";
        }
    }
}