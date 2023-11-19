@extends('frontend.fe_layout.main')

@section('content')
    <style>
        .banner-area {
            background-image: url('{{ asset($_setting['img_header']) }}')
        }

        .cta-one-area {
            background: url('{{ asset('img/metafora.png') }}') center !important;
            background-size: auto;
            background-size: cover;
            text-align: center;
            color: #fff;
        }
    </style>
    <!-- start banner Area -->
    <section class="banner-area relative" id="home">
        <div class="overlay overlay-bg"></div>
        <div class="container">
            <div class="row fullscreen d-flex align-items-center justify-content-between">
                <div class="banner-content col-lg-9 col-md-12">
                    <h1 style="color: azure" class="text-uppercase">
                        Gading Adventure
                        </h3>
                        <h3 style="color: azure">
                            Tempat Sewa Perlengkapan
                            <br>
                            Outdor
                        </h3>

                </div>
            </div>
        </div>
    </section>
    <!-- End banner Area -->


    {{-- Berita --}}
    <section class="popular-course-area section-gap">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="menu-content pb-70 col-lg-8">
                    <div class="title text-center">
                        <h1 class="mb-10">Alat Outdor</h1>
                        <p>Daftar Alat Outdor yang dapat kamu sewa </p>
                    </div>
                </div>
            </div>
            <div class="row">
                @if (count($barang))
                    @if ($barang)
                        <div class="active-popular-carusel">
                            @foreach ($barang as $item)
                                @php
                                    $detail_link = "/dashboard/item/barang/detail/$item->id";
                                @endphp
                                <div class="single-popular-carusel">
                                    <div class="thumb-wrap relative">
                                        <div class="thumb relative">
                                            <a href="{{ $detail_link }}">
                                                <div class="overlay overlay-bg"></div>
                                                @php
                                                    $gbr = App\Models\Gambar::where('id_barang', $item->id)->first();
                                                @endphp
                                                @if ($gbr)
                                                    <img class="img-fluid custom-image" src="{{ URL::to($gbr->file) }}" alt="Foto">
                                                @else
                                                    <img class="img-fluid custom-image" src="{{ asset('dist/img/default.jpg') }}"
                                                        alt="Foto">
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                    <div class="details">
                                        <a href="{{ $detail_link }}">
                                            <h4 class="crop-text" style="height: 2.5em;">
                                                {{ $item->nama  }}  {{$item->barangReady() ? '(Stok '.$item->barangReady().')' : ' (Stok Kosong)'}} 
                                            </h4>
                                            <h5>Harga : {{nominal($item->harga_sewa)}} /day</h5>
                                        </a>
                                        <p style="height: 4em;">
                                            {{ $item->deskripsi }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="col-sm-12">
                            <div class="alert alert-info">Belum Ada Data</div>
                        </div>
                    @endif
                @endif

            </div>
        </div>
    </section>


@endsection
