<header id="header" id="home">
    <div class="container main-menu">
        <div class="row align-items-center justify-content-between d-flex">
            <div id="logo">
                <a href="/" class="my-loading"><img src="{{ $_setting['logo'] }}" alt="" title=""  /></a>
            </div>
            <nav id="nav-menu-container">
                <ul class="nav-menu">
                    @foreach ($menu_container as $key => $val)
                        @if (is_array($val))
                            <li class="menu-has-children"><a href="javascript:void(0)">{{ $key }}</a>
                                <ul>
                                    @foreach ($val as $i => $item)
                                        <li><a href="{{ $item }}" class="my-loading">{{ $i }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li><a href="{{ $val }}" class="my-loading">{{ Str::upper($key) }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </nav><!-- #nav-menu-container -->
        </div>
    </div>
</header><!-- #header -->
