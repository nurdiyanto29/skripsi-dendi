{{-- @extends('frontend.fe_layout.with_sidebar')

@section('item')
    @php
        $judul = $data->nama ?? $data->judul;
        $foto = $data->gambar()->first()->file ?? null;
        // dd($foto);
        $item_list = $item_list ?? [];
        
    @endphp
    <div class="main-img">
        @if ($foto)
            <img class="img-fluid" src="{{ URL::to($foto) }}" alt="Foto" width="100%">
        @else
            <img class="img-fluid" src="{{ asset('dist/img/default.jpg') }}" alt="Foto" width="100%">
        @endif
    </div>

    <div class="details-content ">
        <h3 style="margin:20px 0px">{{ $judul }}</h3>
        <h4>Harga Sewa {{nominal($data->harga_sewa)}}</h4>
        @foreach ($item_list as $k => $lt)
            <p>{{ $k }} : {{ $data->{$lt} }}</p>
        @endforeach
        {!! $data->deskripsi ?? $data->keterangan !!}
    </div>
    <div class="row align-items-center">
        <a href="{{url('dashboard/pesanan/detail/'.$data->id)}}" class="text-uppercase primary-btn mx-auto mt-40">Order Now</a>
    </div>
@endsection --}}
@extends('frontend.fe_layout.main')

@section('content')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
    <style>
        .banner-area {
            background-image: url('{{ asset($_setting['img_header']) }}')
        }

        .img-fluid {
            max-width: 100%;
            min-height: 270px;
            max-height: 280px;
        }
    </style>
    <!-- start banner Area -->
    <!-- start banner Area -->
    <section class="banner-area relative about-banner" id="home">
        <div class="overlay overlay-bg"></div>
        <div class="container">
            <div class="row d-flex align-items-center justify-content-center">
                <div class="about-content col-lg-12">
                    <h1 class="text-white">
                        {{ $opt['head'] }}
                    </h1>
                    <p class="text-white link-nav"><a href="/">Beranda </a> <span class="lnr lnr-arrow-right"></span>
                        <a href="">{{ $opt['head'] }}</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
    <br>
    <section class="course-mission-area pb-120">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="menu-content pb-70 col-lg-8">
                    <div class="title text-center">
                        <h1 class="mb-10"></h1>
                    </div>
                </div>
            </div>
            @php
                $nama = $data->nama;
                $foto = $data->gambar()->first()->file ?? null;
                $item_list = $item_list ?? [];
                
            @endphp
            <div class="row">
                <div class="col-lg-8 left-contents">
                    <div class="main-image" style="text-align: center">
                        @if ($foto)
                            <img class="img-fluid" src="{{ URL::to($foto) }}" alt="Foto">
                        @else
                            <img class="img-fluid" src="{{ asset('dist/img/default.jpg') }}" alt="Foto">
                        @endif
                    </div>
                    <div class="jq-tab-wrapper horizontal-tab" id="horizontalTab">
                        <div class="jq-tab-menu">
                            <div class="jq-tab-title active" data-tab="1">Informasi Barang</div>
                            <div class="jq-tab-title" data-tab="2">Informasi Data Waiting
                                ({{ $data->waiting()->count() }})</div>
                        </div>
                        <div class="jq-tab-content-wrapper">
                            <div class="jq-tab-content active" data-tab="1">
                                <ul class="course-list">
                                    <li class="justify-content-between d-flex">
                                        <p>Nama Barang</p>
                                        <p>{{ $nama }}</p>
                                    </li>
                                    <li class="justify-content-between d-flex">
                                        <p>Kode Barang</p>
                                        <p>{{ $data->kode_barang }}</p>
                                    </li>
                                    <li class="justify-content-between d-flex">
                                        <p>Harga Sewa</p>
                                        <p>{{ rp($data->harga_sewa) }}</p>
                                    </li>
                                    <li class="justify-content-between d-flex">
                                        <p>Stok Tersedia</p>
                                        <p>{{ $data->barangReady() != 0 ?: 'Tidak Tersedia' }}</p>
                                    </li>
                                </ul>
                            </div>
                            <div class="jq-tab-content" data-tab="2">
                                <div class="widget-wrap">
                                    <div class="single-sidebar-widget popular-post-widget">
                                        <div class="popular-post-list">
                                            @foreach ($data->waiting as $item)
                                                <p>{{ $item->user->name }}</p>
                                            @endforeach
                                            @if ($data->waiting()->count() == 0)
                                                <p>Data Waiting Tidak Tersedia</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($data->barangReady() == 0)
                    <div class="col-lg-4 right-contents">
                        <h3>Saat Ini Barang Full Booking, Masih Berminat?</h3>
                        <br>
                        <form action="{{ route('home.pesanan.waiting.store') }}" method="POST">
                            @csrf

                            <input type="hidden" class="form-control" name="barang_id" id=""
                                value="{{ $data->id }}">

                            <button type="submit"
                                class="primary-btn text-uppercase">{{ Auth::check() ? 'Daftar Waiting List' : 'Silahkan Daftar/Login Lebih Dulu' }}</button>
                        </form>
                    </div>
                @else
                    <div class="col-lg-4 right-contents">
                        <h3>Pesan Disini</h3>
                        <br>
                        <form action="{{ route('home.pesanan.store') }}" method="POST">
                            @csrf
                            <div class="form-row">
                                @php
                                    $date = tgl(date('Y-m-d')) . ' ' . date('H:i');
                                @endphp
                                <div class="form-group col-md-12">
                                    <label for="">Tanggal Mulai Sewa</label>
                                    <input type="text" class="form-control" id="" value="{{ $date }}"
                                        readonly @if (!Auth::check()) disabled @endif>

                                </div>

                                <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}">
                                <input type="hidden" name="_id" value="{{ $data->id }}">
                                <div style="display: none" class="form-group col-md-12">
                                    <label for="">Jam Mulai</label>
                                    <input type="hidden" class="form-control" value="{{ date('H:i:s') }}" id="timeInput"
                                        name="jam" @if (!Auth::check()) disabled @endif>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="">Jumlah Hari</label>
                                    <input type="number" class="form-control" name="hari" min="0" id=""
                                        placeholder="Jumlah Hari" @if (!Auth::check()) disabled @endif>
                                </div>
                            </div>
                            <button type="submit"
                                class="primary-btn text-uppercase">{{ Auth::check() ? 'Pesan Sekarang' : 'Silahkan Daftar/Login Lebih Dulu' }}</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endsection
