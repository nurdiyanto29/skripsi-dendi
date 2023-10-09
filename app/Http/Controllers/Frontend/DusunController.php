<?php

namespace App\Http\Controllers\frontend;
use App\Http\Controllers\Frontend\Controller;
use Illuminate\Http\Request;
use App\Models\Dusun;
use App\Models\Penduduk;

class DusunController extends Controller
{
    function index(){

        $data = [];
        $opt = [];
        return $this->view('frontend.dusun', compact('data', 'opt'));
    }

    function ajax(Request $req){
        $dusun = Dusun::orderBy('nama','ASC')->get();
        if(!$dusun) return set_res('Data kosong!');
       
        $item = [];
        $jiwa = $lk = $pr = $rt = $jml_kk = 0;
        foreach ($dusun as $key => $val) {
            
            $e = [
                'dusun' => $val->nama,
                'kadus' => $val->kadus,
                'rt' => $val->jumlah_rt,
                'jiwa' => $this->penduduk($val->id,'jiwa'),
                'jml_kk' => $this->jml_kk($val->id,'KEPALA KELUARGA'),
                'lk' => $this->penduduk($val->id,'lk'),
                'pr' => $this->penduduk($val->id,'pr'),
            ];

            $rt += $val->jumlah_rt;
            $jiwa += $e['jiwa'];
            $lk += $e['lk'];
            $pr += $e['pr'];
            $jml_kk += $e['jml_kk'];
            $item[] = $e;
        }
        $total = compact('rt','jiwa','lk','pr','jml_kk');
        return set_res('',true, compact('item','total') );

    }

    private function penduduk($dusun_id,$type){
        
        $e = Penduduk::where('dusun_id',$dusun_id);
        if($type == 'lk') $e->where('jk','L');
        if($type == 'pr') $e->where('jk','P');
        return $e->count();
    }
    private function jml_kk($dusun_id,$type){
        
        $e = Penduduk::where('dusun_id',$dusun_id);
        if($type == 'KEPALA KELUARGA') $e->where('hub_keluarga','KEPALA KELUARGA');
        return $e->count();
    }
}
