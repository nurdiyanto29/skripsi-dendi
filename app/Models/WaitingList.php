<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitingList extends Model
{
    use HasFactory;
    protected $table = 'waiting_list';
    protected $guarded = [];

    public function barang(){
        return $this->belongsTo(Barang::class);
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
