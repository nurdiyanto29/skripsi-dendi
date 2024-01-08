<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Frontend\Controller;
use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangDetail;
use App\Models\Pesanan;
use App\Models\WaitingList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use Telegram\Bot\Laravel\Facades\Telegram;
use DateTime;
use Illuminate\Support\Carbon;

class PesananController extends Controller

{
    function index()
    {

        $data = Pesanan::where(['user_id' => Auth::user()->id])->get();
        $opt = [
            'head' => 'Data Pesanan'
        ];
        return $this->view('frontend.pesanan.index', compact('data', 'opt'));
    }
    function waiting_index()
    {
        $data = WaitingList::where(['user_id' => Auth::user()->id])->orderBy('created_at', 'desc')->get();
        $opt = [
            'head' => 'Data Waiting List'
        ];
        return $this->view('frontend.pesanan.waiting', compact('data', 'opt'));
    }

    function store(Request $req)
    {

        $data = [];
        $opt = [
            'head' => 'Tambahkan Pesanan'
        ];
        $barang = Barang::find($req->_id);
        // dd($barang);
        if ($barang) {
            $bd = BarangDetail::where([
                'barang_id' => $barang->id,
                'status_sewa' => 0,
            ])->orderBy('id', 'ASC')->first();

            // dd($bd);
            if ($bd) {
                $tanggal_jam = $req->tanggal . ' ' . $req->jam;

                // Create a DateTime object from the combined date and time
                $datetime = new DateTime($tanggal_jam);

                // Add 2 days to the DateTime object
                $datetime->modify('+' . $req->hari . 'days');

                // Retrieve the new date and time
                $new_tanggal = $datetime->format('Y-m-d');
                $new_jam = $datetime->format('H:i:s');

                $kembali = $new_tanggal . ' ' . $new_jam;

                // dd($kembali, $tanggal_jam);


                $bd->update([
                    'mulai' => $tanggal_jam,
                    'status_sewa' => 1,
                    'kembali' => $kembali,
                    'penyewa' => Auth::user()->id,
                ]);
            } else {
                // $br = BarangDetail::where([
                //     'barang_id' => $barang->id,
                //     'status_sewa' => 1,
                // ])->orderBy('kembali', 'ASC')->first();

                // $tgl_a = $tgl . ':00';
                // $e = $barang->kode_barang . '_' . $tgl_a . '_' . $hari;

                // $ee = base64_encode($e);

                // $r = str_replace('=', '', $ee);

                // $link = "/wt_" . $r;
                // $responseText = "Yahh........., barang yang kamu inginkan saat ini sedang sedang full booked. Ada 1 barang yang paling dekat ready di tanggal "  . ". Gimana? kalau masih minat dengan barang ini kamu bisa klik link berikut agar di daftarkan di data waitinglist oleh admin" . "\n"  . "\n" . "nanti admin kabari kalo barangnya ready";
            }

            $data_order = Pesanan::create([
                'barang_detail_id' => $bd->id,
                'user_id' => Auth::user()->id,
                'tipe_bayar' => NULL,
                'bukti_bayar' => NULL,
                'status' => 'belum bayar',
                
                'mulai' => $tanggal_jam,
                'kembali' => $kembali,
                'total' => $bd->barang->harga_sewa * $req->hari,


            ]);

            $responseText = 'Data Penyewaan berhasil di tambahkan berikut adalah informasi sewa anda' . "\n";
            $responseText .= "\n";
            $responseText .= "Nama Barang: $barang->nama\n";
            $responseText .= "Kode Barang: $barang->kode_barang\n";
            $responseText .= "Mulai sewa: $tanggal_jam \n";
            $responseText .= "Kembali sewa: $kembali \n";
            $link = config('base.url') . '/dashboard/pembayaran/create?brg_dtl=' . $data_order->id;

            $responseText .= "Anda dapat segera melakukan pembayaran melalui link berikut ini " . $link . "\n";
            $responseText .= "\n";
        } else {
            // $responseText = 'Format yang anda masukkan salah . kode barang ' . $kode . 'tidak di temukan' . "\n";
        }

        $this->sendTelegramMessage(Auth::user()->telegram_id, $responseText);


        $url = url('dashboard/pembayaran/create?brg_dtl=' . $data_order->id);

        return redirect()->to($url);
    }
    function waiting_store(Request $req)
    {

        // dd($_POST);
        $barang = Barang::find($req->barang_id);
        $now = Carbon::now();
        $add = $now->addHour(1);

        if ($barang) {
            $data_order = WaitingList::create([
                'barang_id' => $barang->id,
                'user_id' => Auth::user()->id,
                'status_sewa' => 0,

            ]);

            $responseText = "Terimakasih Data Waiting untuk barang $barang->nama dengan kode barang $barang->kode_barang sudah terdaftar" . "\n";
        } else {
        }

        $this->sendTelegramMessage(Auth::user()->telegram_id, $responseText);

        // $url = url('dashboard/pembayaran/create?brg_dtl=' . $data_order->id);

        return redirect()->to('/dashboard/waiting')->with('success', 'Data waiting berhasil di tambahkan');;
        // dd('ok');
    }



    private function sendTelegramMessage($chatId, $text)
    {
        Telegram::sendMessage([
            'chat_id' => trim($chatId),
            'text' => $text,
        ]);
    }
}
