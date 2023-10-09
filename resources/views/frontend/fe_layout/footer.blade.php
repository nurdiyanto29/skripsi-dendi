<footer class="footer-area section-gap">
    <div class="container">
        <div class="row">
            @foreach ($menu_footer as $key => $item)
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="single-footer-widget">
                        <h4>{{ $key }}</h4>
                        <ul>
                            @foreach ($item as $i => $val)
                                <li><a href="{{ $val }}" class="my-loading">{{ $i }}</a></li>
                            @endforeach
                            
                        </ul>
                    </div>
                </div>
            @endforeach

            <div class="col-lg-3  col-md-6 col-sm-6">
                <div class="single-footer-widget">
                    <h4><img src="{{ $_setting['logo'] }}"></h4>
                    <p>{{ $_setting['alamat'] }}</p>
                    <p>{{ $_setting['email'] }}</p>
                    <p>{{ $_setting['telepon'] }}</p>
                </div>
            </div>

        </div>
        {{-- End Row --}}


        <div class="footer-bottom row align-items-center justify-content-between">
            <p class="footer-text m-0 col-lg-6 col-md-12">
                Copyright &copy; 2023 All rights reserved by <a href="https://www.google.com"
                    target="_blank">Gading Adventure</a>
            </p>
            <div class="col-lg-6 col-sm-12 footer-social">
                @foreach ($sosmed as $item)
                    <a href="{{ $item->link }}" target="_blank"><i class="fa fa-2x fa-{{ strtolower($item->tipe) }}"></i></a>
                @endforeach

            </div>
        </div>

    </div>
</footer>
