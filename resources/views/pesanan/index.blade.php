@extends('layout.master')

@section('title')
    Pesanan
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
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Data Pesanan</h3>
                                <div class="form-group">
                                    {{-- <button class="btn btn-primary btn-sm float-right"
                                        onclick="window.location='{{ url('barang/create') }}'" type="button">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button> --}}
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr style="text-align: center">
                                            <th style="width: 20px">No</th>
                                            <th>Nama Barang</th>
                                            <th>Mulai - Kembali</th>
                                            <th>Total </th>
                                            <th>Pemesan</th>
                                            <th>Tipe Bayar</th>
                                            <th>Status</th>
                                            <th><i class="fas fa-cogs"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $x = 1;
                                        @endphp
                                         {{-- belum bayar
                                         terbayar belum terkonfirmasi=> pertama ketika sudah melakukan pembayaran baik cod atau tf
                                         terbayar terkonfirmasi =>  --}}
                                        @foreach ($data as $dt)
                                            <tr>
                                                <td>{{ $x++ }}</td>
                                                <td>{{ $dt->barangDetail->barang->nama }}</td>
                                                <td>{{ tgl_full($dt->mulai) }} - {{ tgl_full($dt->kembali) }}</td>
                                                <td>{{ rp($dt->total) }}</td>
                                                <td>{{ $dt->user->name }}</td>
                                                @if ($dt->tipe_bayar == 'tf')
                                                    <td>
                                                        <a href="{{ url('/uploads/bukti_bayar/' . $dt->bukti_bayar) }}"
                                                            target="_blank">{{ $dt->tipe_bayar == 'tf' ? 'Transfer' : 'COD' }}</a>
                                                    </td>
                                                @else
                                                    <td>COD</td>
                                                @endif
                                                <td>{{ $dt->status}}</td>
                                                <td>
                                                    @if ($dt->status == 'terbayar belum terkonfirmasi')
                                                    <a href="/pesanan/konfirmasi?_i={{$dt->id}}&&_status=terbayar terkonfirmasi">Konfirmasi</a>  
                                                    @endif
                                                    @if ($dt->status == 'terbayar terkonfirmasi')
                                                    <a href="/pesanan/konfirmasi?_i={{$dt->id}}&&_status=dikembalikan">Dikembalikan</a>  
                                                    @endif
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
        // $(document).on("click", "#barcode_tampil", function() {
        //     $('#barcodeTampil').modal('show');
        //     let kode = $(this).data('kode');
        //     let id = $(this).data('id');
        //     $("#show-barcode").attr("src", "/storage/" + kode + ".png");
        //     $("#show-id").val(id);
        // });
        $(document).on('click', '#delete-data', function() {
            let id = $(this).attr('data-id');
            let nama = $(this).attr('data-nama');
            $('#id').val(id);
            $('#delete-nama').html(nama);

        });
    </script>
@endpush
