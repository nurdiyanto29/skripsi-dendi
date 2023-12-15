<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Frontend\Controller;
use App\Models\Agenda;
use App\Models\Barang;
use App\Models\Post;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    function index()
    {
        $data = [
            'barang' => Barang::orderBy('created_at', 'desc')->take(8)->get()
        ];

        if(Auth::check() && !Auth::user()->telegram_id) return view('blank');
        return $this->view('frontend.home',$data);
    }

    
    function sewa()
    {
        $data = [
            'barang' => Barang::orderBy('created_at', 'desc')->get()
        ];
        return $this->view('frontend.sewa',$data);
    }

    


}
