<?php

namespace App\Http\Controllers\Frontend;

use App\Models\SocialMedia;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Route;

class Controller extends BaseController
{
    use  DispatchesJobs, ValidatesRequests;

    function view($path, $data)
    {
        // Paginator::useBootstrapFour();

        $data += [
            '_setting' => config('gading'),
            'sosmed' => [],
            'menu_container' => [
                'beranda' => '/',
                'sewa' => '/dashboard/sewa',
            ],


            'menu_footer' => [],

        ];
        if (Auth::check()) {

            $data['menu_container'] += [
                
             

                'Pesanan Anda' => [
                    'Riwayat Pesan' => '/dashboard/pesanan',
                    'Waiting' => '/dashboard/waiting',
                ],
                'Logout' =>  '/logout',
            ];
        }
        if (Auth::check() == 0) {
            $data['menu_container'] += [
                'Login' =>  '/login',

            ];
        }
        // dd($data);
        return view($path, $data);
    }
}
