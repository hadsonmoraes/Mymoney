<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Mymoney') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/sass/app.scss', 'resources/js/app.js'])
</head>

@php
    $theme = auth()->check() ? auth()->user()->darkmode : 'theme-light';
    $bsTheme = $theme === 'theme-dark' ? 'dark' : 'light';
@endphp

<body class="{{ $theme }}" data-theme="{{ $theme }}" data-bs-theme="{{ $bsTheme }}">

    @include('partials/sidebar')

    <div @auth id="content" @endauth>
        <main>
            @yield('content')
        </main>
    </div>

    @include('partials/footer')

</body>

</html>
