<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PembayaranController;
use App\Http\Controllers\Frontend\PesananController;
use App\Http\Controllers\Frontend\PostController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'dashboard', 'as' => 'home.'], function () {
        $class = HomeController::class;
        Route::get('/', [$class, 'index'])->name('index');
        Route::get('/sewa', [$class, 'sewa'])->name('sewa');


        Route::group(['prefix' => 'item', 'as' => 'barang.'], function () {
                $class = PostController::class;
                Route::get('/{type}', [$class, 'index'])->name('index');
                Route::get('/{type}/detail/{id}', [$class, 'detail'])->name('detail');
        });

        Route::group(['middleware' => ['cekrole:Penyewa']], function(){
                Route::group(['prefix' => 'pesanan', 'as' => 'pesanan.'], function () {
                        $class = PesananController::class;
                        Route::get('/', [$class, 'index'])->name('index');
                        Route::post('/store', [$class, 'store'])->name('store');
                        Route::post('/waiting/store', [$class, 'waiting_store'])->name('waiting.store');
                });

                Route::group(['prefix' => 'waiting', 'as' => 'waiting.'], function () {
                        $class = PesananController::class;
                        Route::get('/', [$class, 'waiting_index'])->name('waiting_index');
                });

        });
        Route::group(['prefix' => 'pembayaran', 'as' => 'pembayaran.'], function () {
                $class = PembayaranController::class;
                Route::get('/create', [$class, 'create'])->name('create');
                Route::post('/store', [$class, 'store'])->name('store');
               
        });
      
});
Route::group(['prefix' => 'register', 'as' => 'register.'], function () {
        $class = AuthController::class;
        Route::get('/', [$class, 'register'])->name('register');
        Route::post('/store', [$class, 'register_store'])->name('store');
});

