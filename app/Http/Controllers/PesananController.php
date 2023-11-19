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

    public function create()
    {
        return view('barang.create');
    }


    public function store(Request $request)
    {
        $data = new Barang();
        $data->nama = $request->nama;
        $data->deskripsi = $request->deskripsi;
        $data->harga_sewa = $request->harga_sewa;
        $data->stok = $request->stok;
        $data->save();

        $b_id = Barang::find($data->id);
        // membuat kode barang
        $nama = $data->nama;
        $id = $data->id;
        $in = 'AT';
        $ven = strtoupper($nama);
        $sub_kalimat = substr($ven, 0, 2);
        $kode = $in . $sub_kalimat;
        $kd = $kode . sprintf("%03s", $id);

        Barang::where('id', $data->id)
            ->update([
                'kode_barang' => $kd,
            ]);
        $image = array();
        if ($file = $request->file('file')) {
            $jum = count($request->file('file'));
            foreach ($file as $f) {
                $image_name = md5(rand(1000, 10000));
                $ext = strtolower($f->getClientOriginalExtension());
                $image_full_name = $image_name . '.' . $ext;
                $uploade_path = 'uploads/images/';
                $image_url = $uploade_path . $image_full_name;
                $f->move($uploade_path, $image_full_name);
                $image[] = $image_url;
            }
            for ($i = 0; $i < $jum; $i++) {
                Gambar::create([
                    'id_barang' => $data->id,
                    'file' => $image[$i]
                ]);
            }
        }

        for ($i = 0; $i < $data->stok; $i++) {
            $barangDetail = new BarangDetail();
            $barangDetail->barang_id = $data->id;


            // Setel atribut-atribut lainnya untuk BarangDetail jika ada

            $barangDetail->save();
        }

        return redirect()->route('barang.index')
            ->with(['t' =>  'success', 'm' => 'Data berhasil ditambah']);
    }

    public function show(Barang $barang)
    {
        $data = Barang::find($barang->id);
        // dd($data);
        return view('barang.detail', compact('data'));
    }
    public function edit(Request $request, $id)
    {
        $data = [
            'barang' => Barang::findOrfail($id)

        ];
        return view('barang.edit', compact('data'));
    }



    public function update(Request $request, $id)
    {
        $data = Barang::find($id);
        $updt = $data->update([
            'nama' => $request->get('nama'),
            'harga_sewa' => $request->get('harga_sewa'),
            'deskripsi' => $request->get('deskripsi'),
            'stok' => $request->get('stok'),
        ]);

        $nama = $request->get('nama');
        $id = $id;
        $in = 'AT';
        $ven = strtoupper($nama);
        $sub_kalimat = substr($ven, 0, 2);
        $kode = $in . $sub_kalimat;
        $kd = $kode . sprintf("%03s", $id);
        Barang::where('id', $id)
            ->update([
                'kode_barang' => $kd,
            ]);

        $image = array();
        if ($file = $request->file('file')) {
            $jum = count($request->file('file'));
            foreach ($file as $f) {
                $image_name = md5(rand(1000, 10000));
                $ext = strtolower($f->getClientOriginalExtension());
                $image_full_name = $image_name . '.' . $ext;
                $uploade_path = 'uploads/images/';
                $image_url = $uploade_path . $image_full_name;
                $f->move($uploade_path, $image_full_name);
                $image[] = $image_url;
            }

            for ($i = 0; $i < $jum; $i++) {
                Gambar::create([
                    'id_barang' => $data->id,
                    'file' => $image[$i]
                ]);
            }
        }
        return redirect()->route('barang.index')
            ->with(['t' =>  'success', 'm' => 'Data berhasil diupdate']);
    }

    public function destroy(Request $request)
    {
        $data = Barang::findorFail($request->id)->update(['status' => 0]);
        return redirect()->route('barang.index')
            ->with(['t' =>  'success', 'm' => 'Data berhasil dihapus']);
    }
}
