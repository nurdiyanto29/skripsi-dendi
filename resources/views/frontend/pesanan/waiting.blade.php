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
                        Waiting
                    </h1>
                    <p class="text-white link-nav"><a href="/">Beranda </a> <span class="lnr lnr-arrow-right"></span> <a
                            href="">Data Waiting</a></p>
                </div>
            </div>
        </div>
    </section>
    <br>
    <section class="popular-courses-area section-gap courses-page">
        <div class="container">
            <div class="row">
                @foreach ($data as $item)
                    <div class="single-popular-carusel col-lg-3 col-md-6">
                        <div class="thumb-wrap relative">
                            <div class="thumb relative">
                                <div class="overlay overlay-bg"></div>
                                @php
                                    $gbr = App\Models\Gambar::where('id_barang', $item->barang_id)->first();
                                @endphp
                                @if ($gbr)
                                    <img class="img-fluid custom-image" src="{{ URL::to($gbr->file) }}" alt="Foto">
                                @else
                                    <img class="img-fluid custom-image" src="{{ asset('dist/img/default.jpg') }}"
                                        alt="Foto">
                                @endif
                                {{-- <img class="img-fluid" src="{{ URL::to($gbr->file) }}" alt=""> --}}
                            </div>
                         
                        </div>
                        <div class="details">
                            <a href="course-details.html">
                                <h4>
                                    {{ $item->barang->nama ?? '-' }} ({{ $item->barang->kode_barang }})

                                </h4>
                            </a>
                           
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/randomcolor/0.6.1/randomColor.min.js"
        integrity="sha512-vPeZ7JCboHcfpqSx5ZD+/jpEhS4JpXxfz9orSvAPPj0EKUVShU2tgy7XkU+oujBJKnWmu4hU7r9MMQNWPfXsYw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection
@push('js')
    <script>
        $(document).ready(function() {

            @if (session('success'))
                toastr.success('{{ session('success') }}')
            @endif

            @if (session('error'))
                toastr.error('{{ session('error') }}')
            @endif

            @if ($errors->any())
                toastr.error('Form input tidak valid!');
            @endif

            $('#myForm').submit(function(e) {
                $(this).hide();
                $(this).parent().loading();

            });
        });
    </script>
@endpush
