<?php

namespace App\Http\Controllers\Frontend;



use App\Models\Pbb;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Frontend\Controller;


class PbbController extends Controller
{
    function index()
    {
        
        $item = Pbb::orderBy('created_at', 'desc');
        $base_url = '/pbb';
        $data = [
            'base_url' => $base_url,
            'header' => 'PBB',
            'breadcrumbs' => [
                'Beranda' => '/',
                'PBB' => $base_url
            ],

            'data' => $item->paginate(10)
        ];
        return $this->view('frontend.pbb_list',$data);

    }

    function download($type,$id){
        if(!Str::isUuid($id) || !in_array($type,['sppt','bagan'])) abort(403);
        $item = Pbb::findOrFail($id);
        $file = $item->{'file_'.$type};
        if( !file_exists($file ) ) abort(400);
        return Storage::download(str_replace('storage','public',$file));
    }
   


}
