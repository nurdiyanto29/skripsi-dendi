<?php

use App\Http\Controllers\BarangController;

use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\TelegramController;

use App\Http\Controllers\UserCOntroller;
use Barryvdh\DomPDF\PDF;

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.auth');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.auth');

Route::group([], __DIR__ . '/home.php');

Route::get('/', [HomeController::class, 'index'])->name('admin.index');

Route::get('/nota', function () {
    $pdf = PDF::loadview('nota');
    return $pdf->stream('laporan-pegawai.pdf');
});
Route::get('/set', function () {
    $env = config('app.telegram.bot_token');
    $ngrok = config('base.url');
    $uri = '/telegram/webhook/';
    $url = $ngrok.$uri;

    $x = "https://api.telegram.org/bot".$env."/setWebhook?url=".$url.$env;

    return '<script>window.open("' . $x . '", "_blank");</script>';
   
});
Route::get('/delete', function () {
    $env = config('app.telegram.bot_token');
    $ngrok = config('base.url');
    $uri = '/telegram/webhook/';
    $url = $ngrok.$uri;

    $x = "https://api.telegram.org/bot".$env.'/deleteWebhook';

    return '<script>window.open("' . $x . '", "_blank");</script>';
   
});



Route::group(['prefix' => 'telegram'], function(){
    // Route::get('messages', [TelegramController::class, 'messages']);
    Route::get('messages', [TelegramController::class,'sendMessage']);
    
    Route::get('reset', [TelegramController::class,'resetAllChats']);
    



    Route::get('set-webhook', [TelegramController::class,'setWebhook']);
    // Route::match(['get','post'],'webhook/{token}', [TelegramController::class,]'webhook');
    Route::match(['get','post'],'webhook/'. env('TELEGRAM_BOT_TOKEN'), [TelegramController::class,'webhook']);
});



Route::group(['middleware' => ['cekrole:Pemilik']], function(){
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    
    
    Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::get('/barang/variasi/{id}', [BarangController::class, 'variasi'])->name('variasi.create');
    Route::get('/barang/edit/variasi/{id}', [BarangController::class, 'variasi'])->name('variasi.edit');
    Route::get('/barang/edit/{id}', [BarangController::class, 'edit'])->name('barang.edit');
    Route::get('/barang/detail/{barang}', [BarangController::class, 'show'])->name('barang.detail');
    Route::POST('/barang/store', [BarangController::class, 'store'])->name('barang.store');
    Route::put('/barang/update/{id}', [BarangController::class, 'update'])->name('barang.update');
    Route::post('/barang/delete', [BarangController::class, 'destroy'])->name('barang.delete');


    Route::get('/barang_detail/delete', [BarangController::class, 'destroy_barang_detail'])->name('barang_detail.delete');
    Route::post('/barang_detail/store', [BarangController::class, 'barang_detail_post'])->name('barang_detail.store');
    
    
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/konfirmasi', [PesananController::class, 'konfirmasi'])->name('pesanan.konfirmasi');
    Route::get('/pesananan/create', [PesananController::class, 'create'])->name('pesananan.create');
    Route::get('/pesananan/edit/{id}', [PesananController::class, 'edit'])->name('pesananan.edit');
    Route::get('/pesananan/detail/{pesananan}', [PesananController::class, 'show'])->name('pesananan.detail');
    Route::POST('/pesananan/store', [PesananController::class, 'store'])->name('pesananan.store');
    Route::put('/pesananan/update/{id}', [PesananController::class, 'update'])->name('pesananan.update');
    Route::post('/pesananan/delete', [PesananController::class, 'destroy'])->name('pesananan.delete');

    
    // Route::get('/penjualan/nota/{key}', [PenjualanController::class, 'nota'])->name('penjualan.nota');

    // Route::get('/penjualan/cetak', [PenjualanController::class, 'cetak_pdf'])->name('penjualan.cetak');
    // Route::get('/penjualan/cek/{id}', [PenjualanController::class, 'cek'])->name('penjualan.cek');
    // Route::get('/penjualan/cetak-pdf', [PenjualanController::class, 'pdf'])->name('penjualan.c');
    
   
    Route::get('/user', [UserCOntroller::class, 'index'])->name('user.index');
    Route::POST('/user/store', [UserCOntroller::class, 'store'])->name('user.store');
    Route::PUT('/user/update/{id}', [UserCOntroller::class, 'update'])->name('user.update');
    Route::post('/user/delete', [UserCOntroller::class, 'destroy'])->name('user.delete');


    Route::get('/profile', [AuthController::class, 'profile'])->name('user.profile');
    Route::post('/store/password', [AuthController::class, 'ubahpwstore'])->name('ubahpwstore');
});
