<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'petani_id',
        'tanggal',
        'produk_id',
        'jumlah',
        'total_harga',
    ];
}
