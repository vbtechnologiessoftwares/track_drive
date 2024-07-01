<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">.
    <!-- BEGIN: Head-->
    <head>
       <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="assets/images/favicon.png" type="image/png" />
    <!--plugins-->
      <link rel="stylesheet" type="text/css" href="{{ asset('assets/vendors/css/extensions/swiper.min.css') }}">


    <link href="{{ asset('assets/plugins/simplebar/css/simplebar.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/metismenu/css/metisMenu.min.css')}}" rel="stylesheet" />
    <!-- loader-->
    <link href="{{ asset('assets/css/pace.min.css')}}" rel="stylesheet" />
    <script src="{{ asset('assets/js/pace.min.js')}}"></script>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-extended.css')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&amp;display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css')}}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.css')}}" rel="stylesheet">
    <!-- Theme Style CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/dark-theme.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/css/semi-dark.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/css/header-colors.css')}}" />
    <title>Inbound Call Ad Network</title>
    </head>
    <header>
        @include('layout.header')
        @include('layout.menu')
    </header>



<body>
    <main class="py-4">
            @yield('content')
        </main>
<footer>
     @include('layout.footer')
</footer>
   
</body>
</html>
