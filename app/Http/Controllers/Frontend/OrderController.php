<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Frontend\Controller;
use App\Models\Barang;
use App\Models\Kamling;
use Illuminate\Http\Request;
use App\Models\Dusun;

class OrderController extends Controller
{
    function index(){

        $data = [];
        $opt = [
            'head' => 'Data Kamling'
        ];
        return $this->view('frontend.kamling.index', compact('data', 'opt'));
    }

    function show($id){
        $dusun = Dusun::findOrfail($id);
        $data = Kamling::whereDusun_id($id)->paginate(6);
        $opt = [
            'head' => 'Data Kamling Padukuhan '. $dusun->nama
        ];
        if($data){
            return $this->view('frontend.kamling.detail', compact('data', 'opt'));
        }
        abort(404);
    }
    function detail($id){
;        $data = Barang::findOrFail($id);
        $opt = [
            'head' => ''
        ];
        if($data){
            return $this->view('frontend.kamling.detail_kamling', compact('data', 'opt'));
        }
        abort(404);
    }


}
