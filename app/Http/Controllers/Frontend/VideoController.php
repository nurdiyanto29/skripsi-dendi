<?php

namespace App\Http\Controllers\Frontend;




use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Frontend\Controller;
use App\Models\Video;

class VideoController extends Controller
{
    function index()
    {
        
        $item = Video::orderBy('created_at', 'desc');
        $base_url = '/video';
        $data = [
            'base_url' => $base_url,
            'header' => 'Video',

            'breadcrumbs' => [
                'Beranda' => '/',
                'Video' => $base_url
            ],

            'data' => $item->paginate(10)
        ];
        return $this->view('frontend.video_list',$data);

    }



}
