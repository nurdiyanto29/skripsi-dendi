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

    {{-- @dd(1) --}}
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
            {{-- @dd($data) --}}
            @php

                $foto = $data->barangDetail->barang->gambar()->first()->file ?? null;
                $item_list = $item_list ?? [];

                // Tanggal dan jam masuk
                $mulai = strtotime($data->barangDetail->mulai);

                // Tanggal dan jam keluar
                $kembali = strtotime($data->barangDetail->kembali);

                $diffInDays = ($kembali - $mulai) / (60 * 60 * 24);

                $total_bayar = $diffInDays * $data->barangDetail->barang->harga_sewa;

                //
                $waktu_setelah_penambahan = strtotime('+12 hours', $mulai);

                $akhir = date('Y-m-d H:i:s', $waktu_setelah_penambahan);

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
                </div>
                <div class="col-lg-4 right-contents">
                    <h3>Pembayaran </h3>
                    <br>
                    <div class="alert alert-primary" role="alert">
                        @if ($data->status == 'terbayar belum terkonfirmasi' && $data->tipe_bayar == 'cod')
                            Terimakasih telah melakukan pemesanan. silahkan datang ke toko dan akan di konfirmasi oleh cs
                        @endif

                        @if ($data->status == 'terbayar terkonfirmasi')
                            Terimakasih telah melakukan pembayaran. Pesanan sudah terkonfirmasi
                        @endif

                        @if ($data->status == 'belum bayar' && !$data->tipe_bayar)
                            Batas akhir pembayaran adalah 12 jam setelah pesanan ini dibuat( {{ tgl_full($akhir) }})
                        @endif
                    </div>
                    <form action="{{ route('home.pembayaran.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row mb-3">
                            <input type="hidden" name="_id" value="{{ $data->id }}">
                            <input type="hidden" class="form-control" value="{{ tgl($data->mulai) }}">
                            <input type="hidden" class="form-control" value="{{ tgl($data->kembali) }}">

                            {{-- @dd($data) --}}
                            <div class="form-group col-md-12">
                                <label for="">Tanggal Sewa</label>
                                <input type="text" class="form-control" name="tanggal" readonly
                                    value="{{ tgl_full($data->mulai) }}">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="">Tanggal Kembali</label>
                                <input type="text" class="form-control" name="tanggal_kembali" readonly
                                    value="{{ tgl_full($data->kembali) }}">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="">Jumlah Hari</label>
                                <input type="text" class="form-control" disabled value="{{ $diffInDays }}">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="">Total Yang Harus Dibayarkan</label>
                                <input type="text" class="form-control" disabled value="{{ $total_bayar }}">
                            </div>

                            @if ($data->status == 'belum bayar')
                                <div class="form-group col-md-12">
                                    <label for="">Pilih metode pembayaran</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipe_bayar" value="cod"
                                            id="cod" checked>
                                        <label class="form-check-label" for="cod">
                                            Bayar Di Toko
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipe_bayar" value="tf"
                                            id="tf">
                                        <label class="form-check-label" for="tf">
                                            Transfer
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group col-md-12" id="uploadSection" style="display: none;">
                                    <label for="">Upload Bukti Bayar</label>
                                    <input type="file" name="file" class="form-control">
                                </div>
                            @endif
                        </div>
                        @if (Auth::check() && $data->tipe_bayar == null)
                            <button type="submit" class="primary-btn text-uppercase">Bayar
                            </button>
                        @endif
                        @if (Auth::check() == false)
                            <a class="primary-btn text-uppercase" href="{{ route('login') }}">
                                Login
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </section>
    <script>
        const codRadio = document.getElementById('cod');
        const tfRadio = document.getElementById('tf');
        const uploadSection = document.getElementById('uploadSection');

        codRadio.addEventListener('change', function() {
            uploadSection.style.display = 'none';
        });

        tfRadio.addEventListener('change', function() {
            uploadSection.style.display = 'block';
        });
    </script>
@endsection
