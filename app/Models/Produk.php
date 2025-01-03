<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';

    protected $fillable = [
        'nama',
        'deskripsi',
        'gambar',
        'harga',
        'stok',
        'aktif'
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'harga' => 'decimal:2'
    ];
}