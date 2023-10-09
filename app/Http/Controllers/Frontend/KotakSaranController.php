<?php

namespace App\Http\Controllers\Frontend;

use App\Models\KotakSaran;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Frontend\Controller;

class KotakSaranController extends Controller
{
    function index()
    {
        $data = [];
        $opt = [
            'page' => 'Kotak Saran',
            'alamat' => 'Jl. Parasamya No. 44 Beran Lor Tridadi Sleman ',
            'email' => 'pemerintahkalurahantridadi@gmail.com',
            'tlp' => '(0274) 868 342',
        ];
        return $this->view('frontend.kotak_saran',compact('data','opt'));
    }

    function store(Request $req) : RedirectResponse
    {
        $data = $req->validate([
            'nama' => 'required|email',
            'email' => 'required|email',
            'subjek' => 'required',
            'pesan' => 'required',
        ]);

        KotakSaran::create($data);
        
        return redirect()->to('/kotak_saran')->with('success', 'Berhasil dikirim');

        
    }


}
