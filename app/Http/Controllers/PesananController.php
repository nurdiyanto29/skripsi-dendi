<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\BarangDetail;
use App\Models\Gambar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\WaitingList;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Carbon;


class PesananController extends Controller
{

    public function index()
    {
        $data = Pesanan::orderBy('id', 'desc')->get();
        return view('pesanan.index', compact('data'));
    }

    public function konfirmasi(Request $req)
    {
        $data_index = [
            'id' => $req->_i,
            'status' => $req->_status,
        ];
        $data = Pesanan::find($data_index['id']);
        if (!$data) return redirect()->back()->with(['t' =>  'error', 'm' => 'Data tidak valid']);;
        $data->update($data_index);


        // if($data_index['status'] == '') // 
        if ($data_index['status'] == 'terbayar terkonfirmasi') return redirect()->back()->with(['t' =>  'success', 'm' => 'Pesanan sukses di konfirmasi']);

        if ($data_index['status'] == 'dikembalikan') {
            $barang_detail = $data->barangDetail;
            // dd($barang_id);
            $now = Carbon::now();
            $no = Carbon::now();
            $add = $no->addHour(1);

            $data->update($data_index);

            $item = $data->barangDetail->update([
                'kembali' => $now->subMinutes(5),
            ]);
            $waiting = WaitingList::where('barang_id', $barang_detail->barang_id)
            ->whereNull('notif_date')
            ->whereNull('kadaluarsa')
            ->orderBy('created_at', 'ASC')->first();

            if ($waiting) {
                if ($waiting->notif_date == null) {
                    Telegram::sendMessage([
                        'chat_id' => $waiting->user->telegram_id,
                        'parse_mode' => 'HTML',
                        'text' => ' Halo ' . $waiting->user->name . ' Barang ' . $barang_detail->barang->nama . ' Sudah tersedia. Jika kamu serius untuk melanjutkan pemesanan kamu bisa klik /JADIPESAN_' . $barang_detail->id . ' jika tak kunjung ada respon selama 1 jam setelah chat ini dikirim maka datamu di waiting list akan terhapus dan akan di lempar ke pelanggang yang lain'
                    ]);

                    $waiting->update([
                        'notif_date' => $now,
                        'kadaluarsa' => $add
                    ]);

                    $barang_detail->update([
                        'waiting_id' => $waiting->id
                    ]);
                }
            }

            if(!$waiting){
                $barang_detail->update([
                    'penyewa' => NULL,
                    'mulai' => NULL,
                    'kembali' => NULL,
                    'status_sewa' => 0,
                    'waiting_id' => NULL,
                ]);
            }
        }

        return redirect()->back()->with(['t' =>  'success', 'm' => 'Pesanan sukses di konfirmasi']);
    }
}
