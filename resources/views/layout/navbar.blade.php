<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <style>
        .bg-custom {
            background-color: #1f2d3d !important;
            color: white
        }
    </style>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="{{asset('dist/img/user1-128x128.jpg')}}" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                Brad Diesel
                                <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">Call me whenever you can...</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="{{asset('dist/img/user8-128x128.jpg')}}" alt="User Avatar" class="img-size-50 img-circle mr-3">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                John Pierce
                                <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">I got your message bro</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="{{asset('dist/img/user3-128x128')}}.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                Nora Silvester
                                <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">The subject goes here</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
            </div>
        </li>
        <!-- Notifications Dropdown Menu -->

        <li class="nav-item dropdown user user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <img src="{{ asset('dist/img/avatar.jpg') }}" class="user-image img-circle elevation-2"
                    alt="User Image">
                <span class="d-none d-flex float-right justify-content-around align-items-center">
                    <span class="d-block mr-4">{{ Str::ucfirst(Auth::user()->name) }}</span>
                    <svg width="8" height="7" viewBox="0 0 8 7" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M4.86603 6.5C4.48113 7.16667 3.51887 7.16667 3.13397 6.5L0.535899 2C0.150999 1.33333 0.632124 0.5 1.40192 0.5L6.59808 0.500001C7.36788 0.500001 7.849 1.33333 7.4641 2L4.86603 6.5Z"
                            fill="#333333" />
                    </svg>
                </span>


            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <!-- User image -->
                <li class="user-header bg-custom">
                    <img src="{{ asset('dist/img/avatar.jpg') }}" class="img-circle elevation-2" alt="User Image">
                    <p>
                        {{ Str::ucfirst(Auth::user()->name) }}
                        <small style="color: gold"> &#8226; Online</small>
                    </p>
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                    <div style="padding-bottom: 4px">
                        <a href="{{url('/profile')}}" class="btn btn-default btn-flat btn-block 1single">Ubah Password</a>
                    </div>
                    <div>
                        <button type="button" onclick="window.location='{{ url('/logout') }}'"
                            class="btn btn-default btn-flat btn-block 1single">Sign out</button>
                    </div>
                </li>
            </ul>
        </li>

    </ul>
</nav>
