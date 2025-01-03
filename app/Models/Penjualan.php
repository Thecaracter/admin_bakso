<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';

    protected $fillable = [
        'nomor_invoice',
        'user_id',
        'total_harga',
        'metode_pembayaran',
        'payment_reference',
        'status'
    ];

    protected $casts = [
        'total_harga' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detail()
    {
        return $this->hasMany(DetailPenjualan::class);
    }
}