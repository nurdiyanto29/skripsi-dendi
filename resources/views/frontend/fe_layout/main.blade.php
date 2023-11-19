<!DOCTYPE html>
<html  class="no-js">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{ $_setting['favicon'] }}">
    <meta name="author" content="{{ $_setting['author'] }}">
    <meta name="description" content="{{ $_setting['description'] }}">
    <meta name="keywords" content="{{ $_setting['keywords'] }}">
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $_setting['title'] }}</title>

    @include('frontend.fe_layout.library')

    <style>
        #main-content ol{
            list-style: decimal
        }
        #main-content ul{
            list-style: inherit
        }
        #main-content ul.pagination{
            list-style: none
        }

        .custom-image {
            /* border-radius: 50%; */
            object-fit: cover;
            object-position: center;
            width: 200px;
            height: 200px;
        }
    </style>
</head>

<body>
    @include('frontend.fe_layout.header')
    <div id="main-content">
        @yield('content')
    </div>

    <!-- start footer Area -->
    @include('frontend.fe_layout.footer')
    <!-- End footer Area -->

</body>

</html>
