<?php

namespace App\Http\Controllers;

use App\Models\Variasi;
use Illuminate\Http\Request;

class VariasiController extends Controller
{

    public function store(Request $request)
    {

        // dd($_POST);
        $data = new Variasi(); //variabel data akan menampung fungsi untuk menyimpan data variasi baru
        $data->id_kategori = $request->id_kategori; //data kolom id kategori akan di isi request dari id kategori pada form
        $data->nama = $request->nama;//data kolom nama akan di isi request dari nama pada form
        $data->save();
        return redirect()->back() ->with(['t' =>  'success', 'm'=> 'Data berhasil ditambah']);;
    }

    public function update(Request $request)
    {
        $data =Variasi::where('id', $request->get('id')) //variabel data menyimpan model variasi jika kondisinya id variasi = request form id
        ->update([
            'nama'=>$request->get('nama'),
        ]);
        return redirect()->back()
        ->with(['t' =>  'success', 'm'=> 'Data berhasil update']);
    }
    public function destroy(Request $request)
    {
        $data =Variasi::findorFail($request->id);//variabel data menyimpan model variasi sesuai id yang dipilih
        $data->delete();//jika sudah sesuai kemudian langsung akan di delete
        return redirect()->back()//kemudian akan dikembalikan ke halaman sebelumnya
        ->with(['t' =>  'success', 'm'=> 'Data berhasil dihapus']);
    }
}
