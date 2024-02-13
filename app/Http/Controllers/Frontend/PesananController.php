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

    public function get_data(Request $request)
    {
        // dd($request->_i);
        $barangId = $request->_i;

        $barang = Barang::findOrFail($barangId);

        $result = [];
        $barangDetails = BarangDetail::where('barang_id', $barang->id)->whereNotNull('mulai')->whereNotNull('kembali')->get();
        foreach ($barangDetails as $detail) {
            $start = strtotime($detail->mulai);
            $end = strtotime($detail->kembali);

            while ($start <= $end) {
                $result[] = date('Y-m-d', $start);
                $start = strtotime('+1 day', $start);
            }
        }
        $result = array_unique($result);
        // dd($result);

        $rentalDates = $result;
        return response()->json($rentalDates);
    }

    function store(Request $req)
    {

        // dd($_POST);

        $data = [];
        $opt = [
            'head' => 'Tambahkan Pesanan'
        ];
        $barang = Barang::find($req->_id);
        if ($barang) {
            $bd = BarangDetail::where([
                'barang_id' => $barang->id,
                'status_sewa' => 0,
            ])->orderBy('id', 'ASC')->first();

            if ($bd) {
                // Pisahkan rentang tanggal yang diberikan oleh pengguna
                $tanggalRentang = explode(' - ', $req->tanggal);
                $tanggalMulai = new DateTime($tanggalRentang[0]);
                $tanggalAkhir = new DateTime($tanggalRentang[1]);
                
                $tanggalMulaiString = $tanggalMulai->format('Y-m-d');
                $tanggalAkhirString = $tanggalAkhir->format('Y-m-d');
                
                $selisih = $tanggalAkhir->diff($tanggalMulai);
                
                $selisihHari = $selisih->days;
                

                // Set nilai mulai dan kembali
                $bd->update([
                    'mulai' =>  $tanggalMulaiString. ' ' . $req->jam,
                    'status_sewa' => 1,
                    'kembali' => $tanggalAkhirString . ' ' . $req->jam,
                    'penyewa' => Auth::user()->id,
                ]);
                $data_order = Pesanan::create([
                    'barang_detail_id' => $bd->id,
                    'user_id' => Auth::user()->id,
                    'tipe_bayar' => NULL,
                    'bukti_bayar' => NULL,
                    'status' => 'belum bayar',

                    'mulai' => $tanggalMulaiString . ' ' . $req->jam,
                    'kembali' => $tanggalAkhirString . ' ' . $req->jam,
                    'total' => $bd->barang->harga_sewa * $selisihHari,

                ]);

                $responseText = 'Data Penyewaan berhasil di tambahkan berikut adalah informasi sewa anda' . "\n";
                $responseText .= "\n";
                $responseText .= "Nama Barang: $barang->nama\n";
                $responseText .= "Kode Barang: $barang->kode_barang\n";
                $responseText .= "Mulai sewa: $tanggalMulaiString . ' ' . $req->jam \n";
                $responseText .= "Kembali sewa: $tanggalAkhirString . ' ' . $req->jam \n";
                $link = config('base.url') . '/dashboard/pembayaran/create?brg_dtl=' . $data_order->id;

                $responseText .= "Anda dapat segera melakukan pembayaran melalui link berikut ini " . $link . "\n";
                $responseText .= "\n";
            }
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
