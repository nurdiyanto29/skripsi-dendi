<html>

<head>
    <title>Faktur Pembayaran</title>
    <style>
        #tabel {
            font-size: 15px;
            border-collapse: collapse;
        }

        #tabel td {
            padding-left: 5px;
            border: 1px solid black;
        }
    </style>
</head>

<body style='font-family:tahoma; font-size:8pt;'>
    <center>
        <table style='width:100%; font-size:8pt; font-family:calibri; border-collapse: collapse;' border='0'>
            <td width='30%' align='left' style='padding-right:80px; vertical-align:top'>
                <span style='font-size:12pt'><b>Adila Tani</b></span></br>
               Jl Palembang </br>
            </td>
            <td style='vertical-align:top' width='70%' align='right'>
                <b><span style='font-size:12pt'>NOTA PENJUALAN</span></b></br>
                No Trans. : {{$data->kode_transaksi}}</br>
                Tanggal : {{tgl($data->tanggal)}}</br>
                Kasir : {{Auth::user()->name}}</br>
            </td>
        </table>
        <table cellspacing='0' style='width:100%; font-size:8pt; font-family:calibri;  border-collapse: collapse;'
            border='1'>

            <tr align='center'>
                <td width='10%'>Kode Barang</td>
                <td width='20%'>Nama Barang</td>
                <td width='13%'>Harga</td>
                <td width='4%'>Qty</td>
                <td width='13%'>Total Harga</td>
                @php
                    $total = 0;
                @endphp
                @foreach ($bk as $item)
                    
                <tr>
                    <td>{{$item->barang->kode_barang}}</td>
                    <td>{{$item->barang->nama}}</td>
                    <td>{{$item->barang->harga_jual}}</td>
                    <td>{{$item->jumlah}}</td>
                    <td>@currency($item->total_harga)</td>
                <tr>
                    @php
                        $total += $item->total_harga;
                    @endphp
                @endforeach
                <td colspan='4'>
                    <div style='text-align:right'>Total Yang Harus Di Bayar Adalah : </div>
                </td>
                <td style='text-align:left'>@currency($total)</td>
            </tr>
        </table>
    </center>
</body>

</html>
