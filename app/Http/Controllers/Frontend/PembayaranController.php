<?php

namespace App\Http\Controllers\Frontend;

use App\Models\BarangDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Frontend\Controller;
use App\Models\Pesanan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use DateTime;

class PembayaranController extends Controller
{
    function create(Request $req)
    {
        $b = $req->brg_dtl;
        $data = Pesanan::findOrFail($b);
        if(Auth::check()){
            if((Auth::user()->id != $data->user_id)) abort(403, 'Anda tidak memiliki akses untuk halaman ini' );
        }
    
        $opt = [
            'head' => 'Pembayaran'
        ];
        return $this->view('frontend.fe_layout.pembayaran', compact('data', 'opt'));
    }

    function store(Request $req)
    {
        $e = Pesanan::find($req->_id);

        // dd($e);

        if ($req->hasFile('file')) {
            $imageName = time().'.'.$req->file->extension();
            $req->file->move(public_path('uploads/bukti_bayar'), $imageName);
        } else {
            // Jika gambar tidak diunggah, atur $imageName menjadi null atau nilai default yang sesuai
            $imageName = null;
        }

        $data = [
            'bukti_bayar' => $imageName,
            'status' => 'terbayar belum terkonfirmasi',
            'tipe_bayar' => $req->tipe_bayar,
            'mulai' => $e->mulai,
            'kembali' => $e->kembali,
        ];

        // dd($data);
        $e->update($data);

        return redirect()->back()->with('success', 'Barang Berhasil terbayar');
    }
}
