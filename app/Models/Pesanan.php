<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;
    protected $table = 'pesanan';
    protected $guarded = [];

    public function barangDetail(){
    return $this->belongsTo(BarangDetail::class, 'barang_detail_id');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
