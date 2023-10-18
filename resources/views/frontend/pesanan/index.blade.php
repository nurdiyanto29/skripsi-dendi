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
                        Pesanan
                    </h1>
                    <p class="text-white link-nav"><a href="/">Beranda </a> <span class="lnr lnr-arrow-right"></span> <a
                            href="">Data Pesanan</a></p>
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
            <div class="row">
                <div class="col-lg-12">
                    <div id="pesanan" class="table-responsive" style="background: #fff;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Barang</th>
                                    <th scope="col">Mulai</th>
                                    <th scope="col">Kembali</th>

                                    <th scope="col">Total Bayar</th>
                                    <th scope="col">Tipe Pembayaran</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr>
                                        <td>
                                            <a
                                                href="{{ url('dashboard/pembayaran/create?brg_dtl=' . $item->id) }}">
                                                {{ $item->barangDetail->barang->nama ?? '' }}
                                                {{ $item->barangDetail->barang->kode_barang ?? 'ho' }}
                                            </a>

                                        </td>
                                        <td>{{ tgl_full($item->mulai) }}</td>
                                        <td>{{ tgl_full($item->kembali) }}</td>
                                        <td>{{ rp($item->total) }}</td>
                                        @if ($item->tipe_bayar == 'tf')
                                        <td>
                                            <a href="{{url('/uploads/bukti_bayar/'.$item->bukti_bayar)}}" target="_blank" >{{ $item->tipe_bayar == 'tf' ? 'Transfer' : 'COD'  }}</a>
                                        </td>
                                        @else
                                        <td>COD</td> 
                                        @endif  
                                        <td>{{ Str::title($item->status) }}</td>
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/randomcolor/0.6.1/randomColor.min.js"
        integrity="sha512-vPeZ7JCboHcfpqSx5ZD+/jpEhS4JpXxfz9orSvAPPj0EKUVShU2tgy7XkU+oujBJKnWmu4hU7r9MMQNWPfXsYw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script></script>
@endsection
{{-- @push('js') --}}
{{-- @endpush --}}
