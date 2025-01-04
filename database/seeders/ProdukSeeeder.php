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

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0777, true);
        }

        $filepath = $directory . '/' . $filename;

        try {
            $response = Http::timeout(30)->get('https://api.dicebear.com/7.x/shapes/png?seed=' . urlencode($filename));
            if ($response->successful()) {
                File::put($filepath, $response->body());
                return true;
            }
            echo "Failed with status: " . $response->status() . "\n";
            return false;
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
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
                'gambar_url' => 'bakso-original',
                'gambar' => 'fotoProduk/bakso-original.jpg',
                'aktif' => true
            ],
            [
                'nama' => 'Bakso Urat',
                'deskripsi' => 'Bakso dengan tambahan urat sapi yang kenyal, disajikan dengan kuah kaldu sapi. Cocok untuk pencinta tekstur bakso yang lebih bervariasi.',
                'harga' => 18000,
                'stok' => 100,
                'gambar_url' => 'bakso-urat',
                'gambar' => 'fotoProduk/bakso-urat.jpg',
                'aktif' => true
            ],
            [
                'nama' => 'Mie Bakso Special',
                'deskripsi' => 'Mie bakso dengan topping lengkap termasuk bakso besar, bakso urat, tahu bakso, dan pangsit. Disajikan dengan kuah kaldu sapi premium.',
                'harga' => 25000,
                'stok' => 50,
                'gambar_url' => 'mie-bakso-special',
                'gambar' => 'fotoProduk/mie-bakso-special.jpg',
                'aktif' => true
            ]
        ];

        foreach ($products as $product) {
            echo "Generating image for {$product['nama']}...\n";
            $downloaded = $this->downloadImage($product['gambar_url'], basename($product['gambar']));

            if (!$downloaded) {
                echo "Failed to generate image for {$product['nama']}\n";
                continue;
            }

            unset($product['gambar_url']);

            Produk::create($product);
            echo "Created product: {$product['nama']}\n";
        }
    }
}