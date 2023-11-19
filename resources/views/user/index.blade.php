@extends('layout.master')

@section('title')
    Penyewa
@endsection
@push('css')
    <style>
        .error {
            color: red;
            font-weight: 400px;
        }

        .select2-selection {
            height: 37px !important;

        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
@endpush
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-header">
                                <h3 class="card-title">Data Penyewa</h3>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-sm float-right" data-toggle="modal"
                                        data-target="#user" type="button">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr style="text-align: center">
                                            <th style="width: 20px">No</th>
                                            <th>Nama </th>
                                            <th>Email </th>
                                            <th>Alamat </th>
                                            <th>Phone </th>
                                            <th>Telegram Id </th>
                                            <th>Bergabung Sejak </th>
                                          
                                            <th style="width: 20px"><i class="fas fa-cogs"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $x = 1;
                                        @endphp
                                        @foreach ($data as $dt)
                                            <tr>
                                                <td>{{ $x++ }}</td>
                                                <td>{{ $dt->name }}</td>
                                                <td>{{ $dt->email }}</td>
                                                <td>{{ $dt->alamat }}</td>
                                                <td>{{ $dt->tlp }}</td>
                                                <td>{{ $dt->telegram_id }}</td>
                                                <td>{{ tgl($dt->created_at) }}</td>
                                                <td style="text-align: center"> <a href="#"
                                                        class="nav-link has-dropdown" data-toggle="dropdown"><i
                                                            class="fa fa-ellipsis-h " style="color: #777778"></i></a>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="nav-link" id="edit-data" data-toggle="modal"
                                                                data-target="#editmodal"
                                                                data-id="{{ $dt->id }}"
                                                                data-name="{{ $dt->name }}"
                                                                data-email="{{ $dt->email }}"    
                                                                data-alamat="{{ $dt->alamat }}"    
                                                                data-tlp="{{ $dt->tlp }}"    
                                                                href="#">Edit</a></li>
                                                        <li> <a href="#" id="delete-data" data-id={{ $dt->id }}
                                                                class="nav-link" data-toggle="modal"
                                                                data-target="#deleteModal">Delete</a></li>
                                                        </a></li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <div class="modal fade" id="user" tabindex="-1" role="dialog" aria-labelledby="JenisTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="JenisTitle">Tambah Penyewa</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('user.store') }}" name="form" id="form" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="errr">
                                <div class="errorTxt"></div>
                            </div>
                            <div class="form-group">
                                <label style="color: #6c757d">Nama</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label style="color: #6c757d">Email</label>
                                <input type="text" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label style="color: #6c757d">Alamat</label>
                                <input type="text" class="form-control" id="alamat" name="alamat" required>
                            </div>
                            <div class="form-group">
                                <label style="color: #6c757d">Phone</label>
                                <input type="text" class="form-control" id="tlp" name="tlp" required>
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
        @if ($data->count() > 0)
            <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="Delete"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 style="font-size: 20px" class="modal-title" id="exampleModalCenterTitle"><i
                                    class="fas fa-info-circle"></i><span></span> Konformasi Hapus!</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('user.delete') }}" method="post">
                                @csrf
                                <input type="hidden" name="id" id="id">
                                <p> Apakah Anda yakin ingin menghapus data  ?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" style="width: 50px" class="btn btn-secondary">Ya</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Batal</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="editmodal" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalCenterTitle">Edit Data penyewa</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('user.update', $dt->id) }}" name="formedit" id="formedit"
                                method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUt')
                                <input type="hidden" class="form-control" id="edit-id" name="id">

                                <div class="form-group">
                                    <label style="color: #6c757d">Nama</label>
                                    <input type="text" class="form-control" id="edit_name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label style="color: #6c757d">Email</label>
                                    <input type="text" class="form-control" id="edit_email" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label style="color: #6c757d">Alamat</label>
                                    <input type="text" class="form-control" id="edit_alamat" name="alamat" required>
                                </div>
                                <div class="form-group">
                                    <label style="color: #6c757d">Phone</label>
                                    <input type="text" class="form-control" id="edit_tlp" name="tlp" required>
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
        @endif
    </div>
@endsection
@push('js')
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#id_barang').select2({
                minimumInputLength: 3,
            });
        });
        $(document).ready(function() {
            $('#id_barang2').select2({
                minimumInputLength: 3,
            });
        });


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
                $this.val(num2);
            });
        });
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "autoWidth": false,
            });
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
        $(document).on("click", "#barcode_tampil", function() {
            $('#barcodeTampil').modal('show');
            let kode_barang = $(this).data('kode_barang');
            let id = $(this).data('id');
            $("#show-barcode").attr("src", "/storage/" + kode_barang + ".png");
            $("#show-id").val(id);
        });
        $(document).on('click', '#edit-data', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let email = $(this).data('email');
            let tlp = $(this).data('tlp');
            let alamat = $(this).data('alamat');
           
            $('#edit_name').val(name);
            $('#edit_email').val(email);
            $('#edit_tlp').val(tlp);
            $('#edit_alamat').val(alamat);
            
            $('#edit-id').val(id);
        });
        $(document).on('click', '#delete-data', function() {
            let id = $(this).attr('data-id');
            // let name_barang = $(this).attr('data-name_barang');
            $('#id').val(id);
            //  $('#delete-name').html(name_barang);

        });
        jQuery(function($) {
                var validator = $('#form').validate({
                    rules: {
                        name: {
                            required: true
                        },
                        email: {
                            required: true
                        },
                        tlp: {
                            required: true
                        },
                      
                    },
                    messages: {
                        name: {
                            required: "Nama Wajib Diisi*",
                        },
                        email: {
                            required: "Email Wajib Diisi*",
                        },
                        tlp: {
                            required: "Phone Wajib Diisi*",
                        },

                    },
                    errorElement: 'div',
                    errorLabelContainer: '.errorTxt',
                    errorClass: 'error2',

                    highlight: function(element, errorClass) {
                        $(element).parents("div.errorTxt").addClass(errorClass)
                    },
                });
            });
    </script>
@endpush
