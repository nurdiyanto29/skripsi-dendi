@extends('layout.master')

@section('title')
    Detail Barang
@endsection
@push('css')
    <style>
        .error {
            color: red;
            font-weight: 400px;
        }
    </style>
    <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
@endpush
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Detail Barang</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Barang</a></li>
                            <li class="breadcrumb-item active">Detail Barang</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">

            <!-- Default box -->
            <div class="card card-solid">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <h3 class="d-inline-block d-sm-none"></h3>
                            @if (App\Models\Gambar::where('id_barang', $data->id)->exists())
                                <div class="col-12">
                                    @php
                                        $gbr = App\Models\Gambar::where('id_barang', $data->id)->first();
                                    @endphp
                                    <img src="{{ URL::to($gbr->file) }}" class="product-image" alt="Product Image">
                                </div>
                                <div class="col-12 product-image-thumbs">
                                    @foreach ($data->gambar as $dt)
                                        <div
                                            @if ($loop->first) class="product-image-thumb active"
                  @else
                  class="product-image-thumb" @endif>
                                            <img src="{{ URL::to($dt->file) }}" alt="Product Image">
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="col-12">
                                    <img src="{{ asset('dist/img/default.jpg') }}" class="product-image"
                                        alt="Product Image">
                                </div>
                            @endif
                        </div>
                        <div class="col-12 col-sm-6">
                            <h3 class="my-3">{{ $data->nama }}</h3>
                            <p>{!! $data->deskripsi !!}</p>
                            <hr>
                            <h4><span style="color: red; font-weight:900"> {{ $data->barangDetail()->count() }}</span> Total
                                stok
                            </h4>
                            <h4><span style="color: red; font-weight:900"> {{ $data->barangReady() }}</span> Stok Ready
                            </h4>
                            <h4><span style="color: red; font-weight:900"> {{ $data->barangDisewa() }}</span> Stok Disewa
                            </h4>

                            <div class="bg-gray py-2 px-3 mt-4">
                                <h2 class="mb-0">
                                    @currency($data->harga_sewa)
                                </h2>
                            </div>
                            <br><br>
                            <div class="mb-2">
                                <button class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#add_barang"
                                    type="button">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </div>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr style="text-align: center">
                                        <th style="width: 20px">No</th>
                                        <th>Nama Barang</th>
                                        <th>Status</th>
                                        <th>Jadwal Mulai - Kembali</th>
                                        <th><i class="fas fa-cogs"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $x = 1;
                                        $y = 1;
                                    @endphp
                                    @foreach ($data->barangDetail()->get() as $dt)
                                        <tr>
                                            <td>{{ $x++ }}</td>
                                            <td>{{ $dt->barang->nama }} {{ $y++ }}</td>

                                            <td>{{ $dt->status_sewa ? 'Disewa' : 'Ready' }}</td>
                                            <td>{{ tgl_full($dt->mulai) }} - {{ tgl_full($dt->kembali) }}</td>
                                            <td>
                                                <button class="btn btn-danger btn-xs"
                                                    onclick="window.location='{{ url('barang_detail/delete?_i=' . $dt->id.'&&_barang_id='.$dt->barang_id) }}'"
                                                    type="button">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div class="modal fade" id="add_barang" tabindex="-1" role="dialog" aria-labelledby="JenisTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="JenisTitle">Tambah jumlah barang untuk {{$data->nama}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ url('barang_detail/store') }}" name="form" id="form" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="errr">
                                <div class="errorTxt"></div>
                            </div>
                                <input type="hidden" class="form-control" id="barang_id" name="barang_id" value="{{$data->id}}" required>
                            <div class="form-group">
                                <label style="color: #6c757d">Masukkan Jumlah</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                            </div>
                            <div class="modal-footer bg-whitesmoke" style="margin-right:-25px">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        </section>
        <!-- /.content -->
    </div>
@endsection
@push('js')
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $("input[data-type='number']").keyup(function(event) {
                // skip for arrow keys
                if (event.which >= 37 && event.which <= 40) {
                    event.preventDefault();
                }
                var $this = $(this);
                var num = $this.val().replace(/,/gi, "");
                var num2 = num.split(/(?=(?:\d{3})+$)/).join(",");
                console.log(num2);
                // the following line has been simplified. Revision history contains original.
                //   $this.val(num2);
            });
        });
    </script>
@endpush
