<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Frontend\Controller;
use App\Models\Barang;

class PostController extends Controller
{
    function index($type, Request $req)
    {
        $data = $this->get_data($type);
        return $this->view('frontend.fe_layout.paginate',$data);
    }
   
    private function get_data($type,$id=null){
       
        $item = Barang::orderBy('created_at', 'desc');
        $sidebar_data = $item->take(4)->get();
        
        $search = request('search');
        
        if($id){
            $item = $item->where('id',$id)->first();
        }else{
            if($search) $item->where('judul','LIKE', "%{$search}%");
            $item =  $item->paginate(10);
        }
        
        $header = $type=='lkk' ? strtoupper($type) : ucwords($type);
        $base = "/post/$type";
        return [
            'search' => $search,
            'show_date' => true,
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


    

    function detail($type,$id){
        $data = Barang::find($id);
        $opt = [
            'head' => 'Detail Barang'
        ];
        return $this->view('frontend.fe_layout.detail_page',compact('data', 'opt'));
    }



}
