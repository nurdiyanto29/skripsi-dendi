@extends('layout.master')

@section('title')
    Dashboard
@endsection

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Dashboard</h1>
                    </div><!-- /.col -->

                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Info boxes -->
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1">
                                <a href="{{ route('barang.index') }}">
                                    <i class="fas fa-box-open"></i>
                                </a>
                            </span>

                            <div class="info-box-content">
                                <span class="info-box-text">Total Barang</span>
                                <span class="info-box-number">
                                    {{ $barang ?? '' }}
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-danger elevation-1" style="background: aqua">
                                <a href=""><i class="fas fa-shopping-cart"></i></a> </span>

                            <div class="info-box-content">
                                <span class="info-box-text">User</span>
                                <span class="info-box-number">{{ $user ?? '' }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->

                    <!-- fix for small devices only -->
                    <div class="clearfix hidden-md-up"></div>

                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-success elevation-1"><a href=""> <i
                                        class="fas fa-list"></i></a></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Waiting</span>
                                <span class="info-box-number">{{ $waiting ?? '' }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-warning elevation-1"><a href=""><i
                                        class="fas fa-table"></i></a> </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Barang Ready</span>
                                <span class="info-box-number">{{ $br ?? '' }}</span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                    </div>
                    <!-- /.col -->
                </div>


                <!-- Main row -->
                <div class="row">
                    <!-- Left col -->
                    <div class="col-md-8">
                        <!-- MAP & BOX PANE -->

                        <!-- /.card -->

                        <!-- TABLE: LATEST ORDERS -->
                        <div class="card">
                            <div class="card-header border-transparent">
                                <h3 class="card-title">Transaksi Terbaru</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>

                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table m-0">
                                        <thead>
                                            <tr>
                                                <th>Kode Transaksi</th>
                                                <th>Jumlah Barang</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- @foreach ($penjualan->take(5) as $dt)
                                                <tr>
                                                    <td><a
                                                            href="{{ route('penjualan.detail', $dt->id) }}">{{ $dt->kode_transaksi }}</a>
                                                    </td>
                                                    @php
                                                    $jml = App\Models\BarangKeluar::where('penjualan_id', $dt->id)->sum('jumlah'); @endphp
                                                    <td>{{ $jml }}</td>
                                                    <td>
                                                        <div class="sparkbar" data-color="#00a65a" data-height="20">
                                                            {{ tgl($dt->tanggal) }}</div>
                                                    </td>
                                                </tr>
                                            @endforeach --}}

                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer clearfix">
                                {{-- <a href="javascript:void(0)" class="btn btn-sm btn-info float-left">Place New Order</a> --}}
                                <a href=""
                                    class="btn btn-sm btn-secondary float-right">Selengkapnya >></a>
                            </div>
                            <!-- /.card-footer -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->

                    <div class="col-md-4">
                        <!-- Info Boxes Style 2 -->

                        <!-- PRODUCT LIST -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Produk Terbaru</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>

                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <ul class="products-list product-list-in-card pl-2 pr-2">
                                    @foreach ($brg->take(4) as $b)
                                        <li class="item">
                                            <div class="product-info">
                                                <a href="{{ route('barang.detail', $b->id) }}"
                                                    class="product-title">{{ $b->nama_barang }}
                                                </a>
                                                <span class="product-description">
                                                    <b>{{ $b->nama }}</b> Kode: {{ $b->kode_barang }}
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                    <!-- /.item -->

                                    <!-- /.item -->
                                </ul>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer text-center">
                                <a href="{{ route('barang.index') }}" class="uppercase">View All Products</a>
                            </div>
                            <!-- /.card-footer -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!--/. container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection
