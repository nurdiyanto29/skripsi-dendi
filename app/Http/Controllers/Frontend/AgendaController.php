<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Umkm;
use App\Models\Agenda;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Frontend\Controller;

class AgendaController extends Controller
{
    function index()
    {
        $data = $this->get_data();
        return $this->view('frontend.agenda_list',$data);
    }
   
    private function get_data($id=null){
        if($id && !Str::isUuid($id)) abort(403);
       
        $item = Agenda::orderBy('mulai', 'desc');
        $sidebar_data = $item->take(4)->get();
        $search = request('search');
                
        if($id){
            $item = $item->where('id',$id)->first();
        }else{
            if($search) $item->where('judul','LIKE', "%{$search}%");
            $item =  $item->paginate(10);
        }
        

        $header = 'Agenda';
        $base = "/agenda";
        return [
            'search' => $search,
            'show_date' => 'mulai',
            'base_url' => $base,
            'header' => $header,
            'breadcrumbs' => [
                'Beranda' => '/',
                $header => $base
            ],
            'data' => $item,
            'sidebar_data' => $sidebar_data,
            'kalender' => true
        ];
    }


    function detail($id){
        $data = $this->get_data( $id);
        return $this->view('frontend.agenda_detail',$data);
    }

    function ajax(Request $req){

		$tanggal = explode('-', $req->month);
		if(count($tanggal) != 2) return set_res();
		
        $tahun = (int)$tanggal[0];
        $bulan = (int)$tanggal[1];
		if(!$tahun || !$bulan || $tahun <1900 || $tahun >2100) return set_res();
		$bulan = sprintf("%02d", $bulan);
        $e = Agenda::whereMonth('mulai',$bulan)
            ->whereYear('mulai',$tahun)
            ->get();
        $data['event'] = [];
        foreach($e as $val){
            $data['event'][] = [
                'id' => $val->id,
                'start' => Carbon::createFromFormat('Y-m-d H:i:s', $val->mulai)->format('Y-m-d'),
                'end' => Carbon::createFromFormat('Y-m-d H:i:s', $val->selesai)->format('Y-m-d'),
                'title' => $val->judul,
                'tempat' => $val->tempat
            ];
        }

        return set_res('',$data);
    }



}
