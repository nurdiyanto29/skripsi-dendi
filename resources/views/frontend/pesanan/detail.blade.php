@extends('frontend.fe_layout.main')

@section('content')
    <style>
        .banner-area {
            background-image: url('{{ asset($_setting['img_header']) }}')
        }

        .img-fluid {
            width: 270px !important;
            height: 205px !important;
            object-fit: cover !important;

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
    <section class="events-list-area section-gap event-page-lists">
        <div class="container">
            <div class="row align-items-center">
                @foreach ($data as $val)
                    <div class="col-lg-6 pb-30">
                        <div class="single-carusel row align-items-center">
                            <div class="col-12 col-md-6 thumb">
                                <a href="{{ route('kamling.detail', ['id' => $val->id]) }}" class="my-loading">
                                    <img class="img-fluid" style="width: 270px; height:210"
                                        src="{{ image($val->foto, true) }}" alt="foto">
                                </a>
                            </div>
                            <div class="detials col-12 col-md-6">
                                <p></p>
                                <a href="{{ route('kamling.detail', ['id' => $val->id]) }}">
                                    <h4>Pos Kamling RT {{ $val->rt }}</h4>
                                </a>
                                <p>
                                    {!! Str::limit($val->keterangan, 220, '...') !!}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div> {{ $data->links() }} </div>
        </div>
    </section>
@endsection
