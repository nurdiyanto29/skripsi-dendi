@extends('frontend.fe_layout.with_sidebar')

@section('item')
@php
    $no_search = $no_search ?? false;
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
    $detail_link = $base_url."/detail/{$item->id}?item=$judul";

    @endphp

    <div class="single-post row">
    <div class="col-lg-12 col-md-12">
        @if ($foto)
        <div class="feature-img">
            <a href="{{ $detail_link }}">
                <div class="el_box_image img_post" style="background-image:url({{ image($foto,true) }});"></div>
            </a>
        </div>
        @endif
        
        <a class="posts-title" href="{{ $detail_link }}"><h3 class="with-date">{{ $judul }}</h3></a>
        
        @if ($show_date)
        <p>{{ date('d F Y', strtotime($item->created_at)) }}</p>
        @endif
        @foreach ($item_list as $k => $lt)
        <p>{{ $k }} : {{ $item->{$lt} }}</p>
        @endforeach
        <p class="excert" style="height:5em;">
            {{-- {!! get_substr($item->deskripsi ?? $item->keterangan, 295, '-') !!} --}}
        </p>
        <a href="{{ $detail_link }}" class="primary-btn">Selengkapnya</a>
    </div>
    </div>
    @endforeach


    <div> {{ $data->links() }} </div>
@endsection
