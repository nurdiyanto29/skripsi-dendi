
    <!-- start banner Area -->
    <section class="banner-area relative about-banner" id="home"
        style="background-image: url({{ $_setting['img_header'] }})">
        <div class="overlay overlay-bg"></div>
        <div class="container">
            <div class="row d-flex align-items-center justify-content-center">
                <div class="about-content col-lg-12">
                    <h1 class="text-white">
                        {{ ucwords($header) }}
                    </h1>
                    @php
                        $brd = [];
                        foreach ($breadcrumbs as $key => $val) {
                            $brd[] = '<a href="' . $val . '" class="my-loading" >' . ucwords($key) . '</a>';
                        }
                        $brd = implode('<span class="lnr lnr-arrow-right"></span>', $brd);
                    @endphp
                    <p class="text-white link-nav">{!! $brd !!}</p>

                </div>
            </div>
        </div>
    </section>
    <!-- End banner Area -->