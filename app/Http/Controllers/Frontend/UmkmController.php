<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Frontend\Controller;
use App\Models\Umkm;

class UmkmController extends Controller
{
    function index()
    {
        $data = $this->get_data();
        return $this->view('frontend.umkm',$data);
    }
   
    private function get_data($id=null){
        if($id && !Str::isUuid($id)) abort(403);
       
        $item = Umkm::orderBy('created_at', 'desc');
        $sidebar_data = $item->take(4)->get();
                
        if($id){
            $item = $item->where('id',$id)->first();
        }else{
            $item =  $item->paginate(8);
        }
        

        $header = 'UMKM';
        $base = "/umkm";
        return [
            'show_date' => false,
            'no_search' => true,
            'base_url' => $base,
            'header' => $header,
            'breadcrumbs' => [
                'Beranda' => '/',
                $header => $base
            ],
            'data' => $item,
            'sidebar_data' => $sidebar_data,
        ];
    }


    function detail($id){
        $data = $this->get_data( $id);
        return $this->view('frontend.fe_layout.detail_page',$data);
    }



}
