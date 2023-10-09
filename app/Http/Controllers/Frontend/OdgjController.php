<?php

namespace App\Http\Controllers\Frontend;



use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Frontend\Controller;
use App\Models\Odgj;

class OdgjController extends Controller
{
    function index()
    {
        $data = $this->get_data();
        return $this->view('frontend.fe_layout.paginate',$data);
        // return $this->view('frontend.odgj_list',$data);
    }
   
    private function get_data($id=null){
        if($id && !Str::isUuid($id)) abort(403);
       
        $item = Odgj::orderBy('created_at', 'desc');
        $sidebar_data = $item->take(4)->get();
        
        $search = request('search');
        
        if($id){
            $item = $item->where('id',$id)->first();
        }else{
            if($search) $item->where('nama','LIKE', "%{$search}%");
            $item =  $item->paginate(10);
        }
        

        $header = 'ODGJ';
        $base = "/odgj";
        return [
            'search' => $search,
            'show_date' => false,
            'base_url' => $base,
            'header' => $header,
            'breadcrumbs' => [
                'Beranda' => '/',
                $header => $base
            ],
            'data' => $item,
            'sidebar_data' => $sidebar_data,
            'item_list' => [
                'Umur' => 'umur',
                'Alamat' => 'alamat'
            ]
        ];
    }


    function detail($id){
        $data = $this->get_data( $id);
        return $this->view('frontend.fe_layout.detail_page',$data);
    }



}
