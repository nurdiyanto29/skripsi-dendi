<?php

namespace App\Http\Controllers\Frontend;


use App\Models\ProdukHukum;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Frontend\Controller;

class ProdukHukumController extends Controller
{
    function index()
    {
        $data = $this->get_data();
        return $this->view('frontend.fe_layout.paginate',$data);
    }
   
    private function get_data($id=null){
        if($id && !Str::isUuid($id)) abort(403);
       
        $item = ProdukHukum::orderBy('created_at', 'desc');
        $sidebar_data = $item->take(4)->get();
        
        $search = request('search');
        
        if($id){
            $item = $item->where('id',$id)->first();
        }else{
            if($search) $item->where('nama','LIKE', "%{$search}%");
            $item =  $item->paginate(10);
        }
        

        $header = 'Produk Hukum';
        return [
            'search' => $search,
            'show_date' => false,
            'base_url' => "/produk_hukum",
            'header' => $header,
            'breadcrumbs' => [
                'Beranda' => '/',
                $header => 'javascript:void(0)'
            ],
            'data' => $item,
            'sidebar_data' => $sidebar_data,
        ];
    }


    function detail($id){
        $data = $this->get_data( $id);
        return $this->view('frontend.fe_layout.detail_page',$data);
    }

    function download($id){
        if(!Str::isUuid($id)) abort(403);
        $item = ProdukHukum::findOrFail($id);
        if( !file_exists($item->file) ) abort(401);
        return Storage::download(str_replace('storage','public',$item->file));
    }



}
