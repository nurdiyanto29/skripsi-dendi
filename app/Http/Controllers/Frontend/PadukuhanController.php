<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Frontend\Controller;
use App\Models\Dusun;
use Illuminate\Http\Request;

class PadukuhanController extends Controller
{
    function index(){

        $data = Dusun::all();
        $opt = [
            'head' => 'Padukuhan'
        ];
        return $this->view('frontend.padukuhan.index', compact('data', 'opt'));
    }
    function show(){
        $data = Dusun::whereNama(request('dusun'))->first();
        if($data){
            $opt = [
                'head' => 'Padukuhan'
            ];
            return $this->view('frontend.padukuhan.detail', compact('data', 'opt'));
        }
        abort(404);
    }
}
