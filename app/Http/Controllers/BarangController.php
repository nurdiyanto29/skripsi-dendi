<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangDetail;
use App\Models\Gambar;
use Illuminate\Http\Request;
class BarangController extends Controller
{

    public function index()
    {
        $data = Barang::orderBy('id', 'desc')->where('status', 1)->get();
        return view('barang.index', compact('data'));
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
        ->with(['t' =>  'success', 'm'=> 'Data berhasil ditambah']);
    }

    public function show(Barang $barang)
    {
        $data = Barang::find($barang->id);
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
        Barang::where('id',$id)
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
        ->with(['t' =>  'success', 'm'=> 'Data berhasil diupdate']);
    }

    public function destroy(Request $request)
    {
        $data = Barang::findorFail($request->id)->update(['status' => 0]);
        return redirect()->route('barang.index')
        ->with(['t' =>  'success', 'm'=> 'Data berhasil dihapus']);
    }
}
