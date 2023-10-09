@extends('layout.master')

@section('title')
    Ubah Password
@endsection
@push('css')
    <style>
        .btn-primary {
            color: #fff;
            background-color: #00edff;
            border-color: #00edff;
            box-shadow: none;
        }
    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">

                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Ubah Password</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="col-md-12">
                    <!-- Profile Image -->
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                                aria-controls="home" aria-selected="true"> <i class="fas fa-key"></i> Ubah Password</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            <div class="card" style="width: 60%">
                                <div class="card-body">
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible fade show pesan_alert"
                                            role="alert">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif
                                    <form role="form" action="{{ route('ubahpwstore') }}" name="form" id="form"
                                        method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <input type="password" class="form-control" id="password_lama"
                                                name="password_lama" placeholder="Password Lama" value="{{ old('password_lama') }}">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" id="password_baru"
                                                name="password_baru" placeholder="Password Baru"  value="{{ old('password_baru') }}">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" id="konfirmasi_password"
                                                name="konfirmasi_password" placeholder="Konfirmasi Password"  value="{{ old('konfirmasi_password') }}">
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
@endsection
