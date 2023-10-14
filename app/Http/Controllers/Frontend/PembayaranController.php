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
        $data = Pesanan::find($b);
    
        $opt = [
            'head' => 'Pembayaran'
        ];
        return $this->view('frontend.fe_layout.pembayaran', compact('data', 'opt'));
    }

    function store(Request $req)
    {
        $e = Pesanan::find($req->_id);
        $datetime1 = new DateTime($e->barangDetail->mulai);
        $datetime2 = new DateTime($e->barangDetail->kembali);

        $interval = $datetime1->diff($datetime2);
        $days = $interval->days;
        $now = Carbon::now();



        if ($req->hasFile('file')) {
            $imageName = time().'.'.$req->file->extension();
    
            $req->file->move(public_path('uploads/bukti_bayar'), $imageName);
        } else {
            // Jika gambar tidak diunggah, atur $imageName menjadi null atau nilai default yang sesuai
            $imageName = null;
        }

        $data = [
            'bukti_bayar' => $imageName,
            'status' => 1,
            'tipe_bayar' => $req->tipe_bayar,
            'mulai' => $now,
            'kembali' => $now->addDays($days),
        ];
        $e->update($data);

        return redirect()->back()->with('success', 'Barang Berhasil terbayar');
    }
}
