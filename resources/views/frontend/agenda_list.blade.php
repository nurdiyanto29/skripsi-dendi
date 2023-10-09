@extends('frontend.fe_layout.with_sidebar')

@section('item')
    @php
        $search = $search ?? null;
        $item_list = $item_list ?? [];
       
        
    @endphp

    @if ($search)
        <div style="margin-bottom: 30px;">
            <h4>Hasil {{ count($data) }} record</h4>
        </div>
    @endif


    @foreach ($data as $item)
        @php
            
            $judul = $item->nama ?? $item->judul;
            $foto = $item->foto ?? null;
            $detail_link = $base_url . "/detail/{$item->id}?item=$judul";
            $tanggal = date('d', strtotime($item->mulai));
            $bulan = date('F', strtotime($item->mulai));
        @endphp

        <div class="single-post row">
            <div class="col-lg-2  col-md-2 meta-details">
                <ul class="tags info-date-agenda-web">
                    <div><span style="font-size:40px;">{{ $tanggal }}</span></div>
                    <span style="text-transform: uppercase;">{{ $bulan }}</span>
                </ul>
                <ul class="tags info-date-agenda-mobile" style="margin-botton 15px;">
                    <div style="vertical-align: middle;">
                        <span style="float:left;font-size:38px;font-weight:bold;color:black;">{{ $tanggal }}</span>
                        <span
                            style="float:left;font-size:20px;padding-bottom:12px;text-transform: uppercase;">&nbsp;{{ $bulan }}</span>
                    </div>
                </ul>
                <div class="user-details row">
                </div>
            </div>
            <div class="col-lg-10 col-md-10 ">
                <div class="feature-img">
                    <a href="{{ $detail_link }}">
                        <div class="el_box_image img_post_mw_agenda"
                            style="background-image:url({{ image($foto,true) }});">
                        </div>
                    </a>
                </div>
                <a class="posts-title" href="{{ $detail_link }}">
                    <h3>{{ $judul }}</h3>
                </a>
                <p class="excert crop-text" style="height:5em;">{!! get_substr($item->deskripsi ) !!}</p>
                <a href="{{ $detail_link }}" class="primary-btn">Selengkapnya</a>
            </div>
        </div>
        
    @endforeach
    <div class="col-sm-12"> {{ $data->links() }} </div>
@endsection
