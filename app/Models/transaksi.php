<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi';
    protected $fillable = ['tgl_transaksi', 'status_order', 'harga_id', 'kg', 'hari', 'harga', 'harga_akhir'];
}