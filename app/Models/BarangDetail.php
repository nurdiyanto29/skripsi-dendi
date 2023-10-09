<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangDetail extends Model
{
    use HasFactory;
    protected $table = 'barang_detail';
    protected $guarded = [];

    public function barang(){
        return $this->belongsTo(Barang::class);
    }
    public function penyewa(){
        return $this->belongsTo(User::class , 'penyewa');
    }
    public function pesanan(){
        return $this->hasMany(User::class , 'barang_detail_id');
    }
}
