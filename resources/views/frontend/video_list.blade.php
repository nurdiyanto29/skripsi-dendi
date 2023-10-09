@extends('frontend.fe_layout.main')

@section('content')
    @include('frontend.fe_layout._banner')


    <section class="post-content-area events-list-area section-gap2 event-page-lists">
        <div class="container">
            <div class="row">

                

                    @if (count($data))
                        @foreach ($data as $item)
                        <div class="single-popular-carusel col-lg-4 col-md-6" id="page1">
                            <div class="bingkai" style="border:1px solid #e2e2e2;padding:0;border-radius:2px">
                                <div class="thumb-wrap relative">
                                    <div class="embed-responsive embed-responsive-16by9">

                                        <iframe showinfo=0 controls=0 autohide=1 style="width: 100%; height: 220px;"
                                            allowfullscreen="" frameborder="0" gesture="media"
                                            src="https://www.youtube.com/embed/{{ $item->link }}">
                                        </iframe>
                                        <div class="on_top_video">
                                            <a class="link_on_top_video play-btn"
                                                href="https://www.youtube.com/watch?v={{ $item->link }}"></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="details pl-3 pr-3">
                                    <h4 class="crop-text-row" title="{{ $item->judul }}">{{ $item->judul }}</h4>
                                    <p>{{ date('d F Y', strtotime($item->created_at)) }}</p>

                                </div>
                            </div>

                        </div>
                        @endforeach

                        <div class="col-lg-12"> {{ $data->links() }} </div>
                    @else
                    <div class="col-lg-12">
                        <div class="alert alert-info">Belum Ada Data</div>
                    </div>
                    @endif

                

            </div>
        </div>
    </section>
@endsection
