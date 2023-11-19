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
                        Data Barang
                    </h1>
                    <p class="text-white link-nav"><a href="/">Beranda </a> <span class="lnr lnr-arrow-right"></span> <a
                            href="">Data Barang</a></p>
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
          {{-- row --}}
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
          {{-- row --}}
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/randomcolor/0.6.1/randomColor.min.js"
        integrity="sha512-vPeZ7JCboHcfpqSx5ZD+/jpEhS4JpXxfz9orSvAPPj0EKUVShU2tgy7XkU+oujBJKnWmu4hU7r9MMQNWPfXsYw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
    </script>
@endsection