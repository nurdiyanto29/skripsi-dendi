@extends('frontend.fe_layout.main')

@section('content')

        @include('frontend.fe_layout._banner')

    
    <section class="post-content-area events-list-area section-gap2 event-page-lists">
        <div class="container">
            <div class="row">
                
                <div class="col-lg-8 posts-list">
                    @yield('item')
                </div>                

                <div class="col-lg-4 sidebar-widgets">
                    @include('frontend.fe_layout.right_sidebar')
                </div>
            </div>
        </div>
    </section>
    
@endsection
