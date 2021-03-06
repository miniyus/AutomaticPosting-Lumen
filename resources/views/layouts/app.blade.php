<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

        <title>{{ config('app.name', 'Lumen') }}</title>
        <!-- Scripts  -->
        {{-- <script src="{{ url('/js/app.js') }}" defer></script> --}}

        <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
        <script src="https://www.gstatic.com/charts/loader.js"></script>

        <!-- Fonts -->
        <link rel="icon" type="image/png" href="/assets/favicon.png?{{ \Carbon\Carbon::now()->timestamp }}" sizes="16x16" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.15.3/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel="stylesheet" type="text/css" />

        <!-- Styles -->
        {{-- <link href="{{ url('/css/app.css') }}" rel="stylesheet" /> --}}
        <link href="{{ url('/css/posts/styles.css') }}" rel="stylesheet" />
        <link rel="manifest" href="/assets/manifest.json" />
        <link rel="apple-touch-icon" href="/assets/favicon.png" />
        <link rel="shortcut icon" href="/assets/favicon.png" />
        @include('layouts.open-graph')
    </head>
    <body>
        @if(request()->is('api/posts/*'))
            @yield('stand-alone')
        @else
            @include('layouts.nav')
            @yield('header')
            @yield('content')
            @yield('footer')
        @endif
        <!-- Bootstrap core JS-->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="{{ url('js/posts/scripts.js') }}"></script>
    </body>
</html>
