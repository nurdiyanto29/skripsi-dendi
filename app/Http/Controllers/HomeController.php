<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Jenis;
use App\Models\Kategori;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Rak;
use App\Models\Suppliyer;
use App\Models\User;
use App\Models\WaitingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {

        $brg = Barang::orderBy('id', 'DESC')->get();
        $barang = Barang::orderBy('id', 'DESC')->count();
        $user = User::where('role', 'Penyewa')->count();
        $waiting = WaitingList::count();
        $br = Barang::join('barang_detail as b', 'b.barang_id', 'barang.id')
            ->where('b.status_sewa', 0)->count();

        if (Auth::check()) {
            if (Auth::user()->role == 'Pemilik') return view('dasboard', compact('barang', 'brg', 'br', 'waiting', 'user'));
        }

        return redirect()->route('home.index');
    }
}
