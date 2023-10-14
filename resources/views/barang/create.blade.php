@extends('layout.master')

@section('title')
    Tambah Barang
@endsection
@push('css')
    <style>
        /* .error {
                                color: red;
                                font-weight: 400px;
                            } */

        .ck-editor__editable {
            min-height: 200px;
        }

        .select2-selection {
            height: 37px !important;

        }

        .error {
            color: #fff;
            font-size: 12px;
            width: unset;
        }

        .errorTxt {
            /* min-height: 20px; */
            background-color: #f39c12 !important;
            margin-bottom: 10px;
        }

        .error2 {
            padding-left: 20px;
            padding-top: 6px;
            padding-bottom: 6px;
            color: #fff
        }


        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff;
            border-color: #006fe6;
            color: #000;
            padding: 0 10px;
            margin-top: 0.31rem;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="{{ asset('image-uploader-master/dist/image-uploader.min.css') }}">
    <script src="https://cdn.ckeditor.com/ckeditor5/30.0.0/classic/ckeditor.js"></script>
@endpush
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Tambah Barang</h3>
                            </div>
                            <form role="form" action="{{ route('barang.store') }}" name="form" id="form"
                                method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="errr">
                                        <div class="errorTxt"></div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-6">
                                            <label style="color: #6c757d">Nama Barang</label>
                                            <input type="text" class="form-control" id="nama" name="nama"
                                                required>
                                        </div>
                                        <div class="form-group col-6">
                                            <label style="color: #6c757d">Harga sewa</label>
                                            <input type="text" class="form-control" id="harga_sewa" name="harga_sewa"
                                                required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-6">
                                            <label style="color: #6c757d">Stok Awal</label>
                                            <input type="number" class="form-control" id="stok" name="stok"
                                                required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-6">
                                            <label style="color: #6c757d">Deskripsi</label>
                                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Enter ..."></textarea>
                                        </div>

                                        <div class="form-group col-6">
                                            <label style="color: #6c757d">Gambar</label>
                                            <div class="input-images">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
    @endsection
    @push('js')
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
        <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="{{ asset('image-uploader-master/dist/image-uploader.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.input-images').imageUploader({
                    extensions: [".jpg", ".jpeg", ".png", ".gif", ".svg"],
                    imagesInputName: "file"
                });
            });
        </script>
    @endpush
