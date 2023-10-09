<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Frontend\Controller;
use App\Models\Konten;
use Illuminate\Support\Str;

class KontenController extends Controller
{
    function index($tipe)
    {
        // dd($tipe);
        $data = Konten::whereNama($tipe)->first();

        if (!$data) {
            $q = array(
                'visi_misi',
                'profil_dan_wilayah',
                'pamong_kalurahan',
                'kepala_desa',
                'badan_permusyawaratan',
                'sejarah_kalurahan',
                'gambaran_umum',
                'layanan_kesehatan'
            );
            if (in_array($tipe, $q)) {
                $data['nama'] = $tipe;
                $replace = str_replace('_', ' ', $data['nama']);
                $data['nama'] = Str::title($replace);
                $data['foto'] = '';
            }else{
                abort('404');
            }
        } else {
            $replace = str_replace('_', ' ', $data->nama);
            $data->nama = Str::title($replace);
        }
        $opt = [];
        return $this->view('frontend.konten', compact('data', 'opt'));
    }
}
