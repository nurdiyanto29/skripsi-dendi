<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Post;
use App\Models\Umkm;
use App\Models\Agenda;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Frontend\Controller;

class HomeController extends Controller
{
    function index()
    {

       
        $data = [
            'barang' => Barang::orderBy('created_at', 'desc')->where('status', 1)->take(8)->get()
        ];

        if (Auth::check() && !Auth::user()->telegram_id) return view('blank');
        return $this->view('frontend.home', $data);
    }


    function sewa()
    {
        $data = [
            'barang' => Barang::orderBy('created_at', 'desc')->where('status', 1)->get()
        ];
        return $this->view('frontend.sewa', $data);
    }
}
