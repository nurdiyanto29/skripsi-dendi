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

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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


                                <div class="form-group col-md-12">
                                    <input type="text" id="daterange" class="form-control" name="tanggal" />

                                </div>

                                {{-- <input type="hidden" name="tanggal" value="{{ date('Y-m-d') }}"> --}}
                                <input type="hidden" name="_id" value="{{ $data->id }}">
                                <div style="display: none" class="form-group col-md-12">
                                    <label for="">Jam Mulai</label>
                                    <input type="hidden" class="form-control" value="{{ date('00:00:01') }}"
                                        id="timeInput" name="jam" @if (!Auth::check()) disabled @endif>
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
    @push('js')
        <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

        <script>
            $(function() {
                var today = new Date();
                var barang_id = {!! json_encode($data->id) !!};
                var maxDate = new Date();

                $.ajax({
                    url: '/dashboard/pesanan/get_data?_i=' + barang_id,
                    method: 'GET',
                    success: function(rentalDates) {
                        rentalDates = Object.values(rentalDates); // Mengonversi objek ke array

                        $('#daterange').daterangepicker({
                            startDate: today,
                            minDate: today,
                            isInvalidDate: function(date) {
                                var formattedDate = date.format('YYYY-MM-DD');
                                return rentalDates.includes(formattedDate);
                            },
                            locale: {
                                format: 'YYYY-MM-DD', // Format tanggal
                                cancelLabel: 'Batal',
                                applyLabel: 'Pilih',
                                daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                                monthNames: [
                                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                    'Juli', 'Agustus', 'September', 'Oktober', 'November',
                                    'Desember'
                                ]
                            }
                        }).on('apply.daterangepicker', function(ev, picker) {
                            var chosenStartDate = picker.startDate;
                            var chosenEndDate = picker.endDate;

                            for (var i = new Date(chosenStartDate); i <= chosenEndDate; i.setDate(i
                                    .getDate() + 1)) {
                                var formattedDate = moment(i).format('YYYY-MM-DD');
                                if (rentalDates.includes(formattedDate)) {
                                    alert('Tanggal yang dipilih tidak tersedia untuk pemesanan!');
                                    $('#daterange').data('daterangepicker').setStartDate(today);
                                    $('#daterange').data('daterangepicker').setEndDate(today);
                                    return;
                                }
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching rental dates:', error);
                    }
                });
            });
        </script>
    @endpush
