@extends('frontend.fe_layout.main')

@section('content')
    @include('frontend.fe_layout._banner')
    <section class="popular-courses-area section-gap courses-page">
        <div class="container">
            					
            <div class="row">
                @foreach ($data as $item)
                @php
                    $detail_link = $base_url."/detail/{$item->id}?item=$item->nama";
                @endphp
                <div class="single-popular-carusel col-lg-3 col-md-6">
                    <div class="thumb-wrap relative">
                        <div class="thumb relative">
                            <a href="{{ $detail_link }}">
                            <div class="overlay overlay-bg"></div>	
                            <img class="img-fluid" src="{{ thumbnail($item->foto,'thumb_',true) }}" alt="Logo">
                            </a>
                        </div>								
                    </div>
                    <div class="details">
                        <a href="{{ $detail_link }}">
                            <h4>{{ $item->nama }}</h4>
                        </a>
                        <p>{{ $item->kategori->nama }}</p>
                    </div>
                </div>
                @endforeach

                <div class="col-sm-12"> {{ $data->links() }} </div>
                								
                											
            </div>
        </div>	
    </section>

    
@endsection
