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

        $data = [
            'bukti_bayar' => $req->file,
            'status' => 1,
            'tipe_bayar' => $req->tipe_bayar,
            'mulai' => $now,
            'kembali' => $now->addDays($days),
        ];
        $e->update($data);

        $image = [];

        if ($req->hasFile('file')) {
            $file = $req->file('file');
            // dd(1);
            $image_name = md5(rand(1000, 10000));
            $ext = strtolower($file->getClientOriginalExtension());
            $image_full_name = $image_name . '.' . $ext;
            $uploade_path = 'uploads/bukti_bayar/';
            $image_url = $uploade_path . $image_full_name;
            $file->move($uploade_path, $image_full_name);
            $image[] = $image_url;
        }

        $pesanan = Pesanan::where('user_id', Auth::user()->id)->get();


        dd('$sukses terbayar');

        // return redirect()->route('home.pesanan.index' ,['data' => $pesanan]);
    }
}
