@extends('frontend.fe_layout.main')

@section('content')
    <style>
        .banner-area {
            background-image: url('{{ asset($_setting['img_header']) }}')
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
                        Kamling
                    </h1>
                    <p class="text-white link-nav"><a href="/">Beranda </a> <span class="lnr lnr-arrow-right"></span> <a
                            href="">{{ Str::title($opt['head']) }}</a></p>
                </div>
            </div>
        </div>
    </section>
    <br>
    <section class="course-details-area">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="menu-content pb-70 col-lg-8">
                    <div class="title text-center">
                        <h3 class="mb-10">Pos Kamling RT {{ $data->rt }}</h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 left-contents">
                    <div class="main-image">
                        <img class="img-fluid" src="{{ image($data->foto, true) }}" alt="foto">
                    </div>
                    <div class="jq-tab-wrapper" id="horizontalTab">
                        <div class="jq-tab-menu">
                            <div class="jq-tab-title active" data-tab="1">Jadwal</div>
                            <div class="jq-tab-title" data-tab="2">Keterangan</div>
                        </div>
                        <div class="jq-tab-content-wrapper">
                            <div class="jq-tab-content active" data-tab="1">
                                <div class="main-image">
                                    <img class="img-fluid" src="{{ image($data->jadwal, true) }}" alt="foto">
                                </div>
                            </div>
                            <div class="jq-tab-content" data-tab="2">
                                {!! $data->keterangan !!}
                            </div>
                            <div class="jq-tab-content" data-tab="3">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 right-contents">
                    @php
                        $maps = 'https://maps.google.com/maps?q=' . $data->map . '&z=15&output=embed';
                        $s = 'http://maps.google.com/?q=' . $data->map;
                    @endphp
                    <iframe src="{{ $maps }}" width="100%" height="270" frameborder="0"
                        style="border:0"></iframe>
                    <a href="{{ $s }}" class="primary-btn text-uppercase">Kunjungi</a>
                </div>
            </div>
        </div>
    </section>
@endsection
