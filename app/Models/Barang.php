<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    protected $table = 'barang';
    protected $guarded = [];

    public function barangDetail()
    {
        return $this->hasMany(BarangDetail::class);
    }
    public function gambar()
    {
        return $this->hasMany(Gambar::class, 'id_barang');
    }
    public function waiting()
    {
        return $this->hasMany(WaitingList::class, 'barang_id');
    }

    public function barangDisewa()
    {
        return $this->barangDetail()->where('status_sewa', 1)->count();
    }
    public function barangReady()
    {
        return $this->barangDetail()->where('status_sewa', 0)->count();
    }
}
