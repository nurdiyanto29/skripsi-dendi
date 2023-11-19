{{-- CSS CSS CSS --}}
<link href="https://fonts.googleapis.com/css?family=Poppins:100,200,400,300,500,600,700" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.css" integrity="sha512-YdYyWQf8AS4WSB0WWdc3FbQ3Ypdm0QCWD2k4hgfqbQbRCJBEgX0iAegkl2S1Evma5ImaVXLBeUkIlP6hQ1eYKQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!--
        CSS
        ============================================= -->
    <link rel="stylesheet" href="{{ asset('frontend/css/linearicons.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">
    <style>
    ::-moz-selection{
        background-color:#7fa9df;
    }
    ::selection{
        background-color:#7fa9df;
    }
   .widget-wrap .popular-post-widget .popular-title,
   .post-content-area .single-post .primary-btn,
   .widget-wrap .search-widget form.search-form input[type="text"],
   .widget-wrap .search-widget form.search-form button,
   .genric-btn.primary,
   .single-blog .details-btn:hover
   {
    background :#166eae;
   }
    </style>
    @stack('js')

{{-- JS JS JS --}}
<script src="{{ asset('frontend/js/vendor/jquery-2.2.4.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js"
    integrity="sha512-RCgrAvvoLpP7KVgTkTctrUdv7C6t7Un3p1iaoPr1++3pybCyCsCZZN7QEHMZTcJTmcJ7jzexTO+eFpHk4OCFAg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
</script>
<script src="{{ asset('frontend/js/vendor/bootstrap.min.js') }}"></script>
<script src="{{ asset('frontend/js/easing.min.js') }}"></script>
<script src="{{ asset('frontend/js/hoverIntent.js') }}"></script>
<script src="{{ asset('frontend/js/superfish.min.js') }}"></script>
<script src="{{ asset('frontend/js/jquery.ajaxchimp.min.js') }}"></script>
<script src="{{ asset('frontend/js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('frontend/js/jquery.tabs.min.js') }}"></script>
<script src="{{ asset('frontend/js/jquery.nice-select.min.js') }}"></script>
<script src="{{ asset('frontend/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('plugins/moment/locales.min.js') }}"></script>
<script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>


<script src="{{ asset('frontend/js/main.js') }}"></script>
<script>
    moment.locale('id');
    toastr.options.positionClass = 'toast-bottom-right';
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

window.addEventListener('pageshow', function(event) { 
  if (event.persisted) {
    $('body').loading('stop')
  }
});



// $(function () {
   
//     $('.my-loading').on('click', function () {
//         $('body').loading()
//         // history.pushState("myloading", document.title, window.location.pathname);
//     });
// });   
</script>
@if(session('success'))
    <script>
        // You can use your preferred toaster library here, for example, toastr
        toastr.success('{{ session('success') }}');
    </script>
@endif
@stack('js')
