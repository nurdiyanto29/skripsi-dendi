@extends('frontend.fe_layout.with_sidebar')

@section('item')
    @php
        $judul = $data->nama ?? $data->judul;
        $foto = $data->foto ?? null;
        $item_list = $item_list ?? [];
        
    @endphp
    <div class="event-details-area">
        <div class="main-img">
            <img class="img-fluid" src="{{ image($data->foto, true) }}" alt="foto" width="100%">
        </div>
        <div class="details-content">
            <h4>{{ $judul }}</h4>

            <ul class="timeline">


                <li>
                    <i class="fa fa-calendar bg-blue" style="background-color: #FF4573;color: white;"></i>
                    <div class="timeline-item">
                        <h3 class="timeline-header">Date & Time</h3>
                        <div class="timeline-body">
                            {{ tgl($data->mulai) }} - {{ tgl($data->selesai) }}
                        </div>
                    </div>
                </li>

                <li>
                    <i class="fa fa-map-marker bg-blue" style="background-color: #F7B72B;color: white;"></i>
                    <div class="timeline-item">
                        <h3 class="timeline-header">Event Location</h3>
                        <div class="timeline-body">
                            @php
                                // $tempat = $data->tempat ? explode(',',$data->tempat ) : [];
                                $tempat = str_replace(',', '<br>', $data->tempat);
                            @endphp
                            {!! $tempat !!}
                        </div>
                    </div>
                </li>

                <li>
                    <i class="fa fa-plus bg-blue" style="background-color: #ED7C52;color: white;"></i>
                    <div class="timeline-item">
                        <h3 class="timeline-header">Detail</h3>
                        <div class="timeline-body">
                            {!! $data->deskripsi !!}
                        </div>
                    </div>
                </li>

            </ul>
        </div>
    </div>
@endsection
