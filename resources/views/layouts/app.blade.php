<!doctype html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2563eb">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Mymoney') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/sass/app.scss', 'resources/js/app.js'])
</head>

@php
    $theme = auth()->check() ? auth()->user()->darkmode : 'theme-light';
    $bsTheme = $theme === 'theme-dark' ? 'dark' : 'light';
    $sidebarCollapsed = auth()->check() && auth()->user()->sidebar === 0;
@endphp

<body class="{{ $theme }} app-shell {{ $sidebarCollapsed ? 'sidebar-collapsed' : '' }}" data-theme="{{ $theme }}"
    data-bs-theme="{{ $bsTheme }}">
    <div class="shell-backdrop"></div>

    @include('partials/sidebar')

    <div @auth id="content" @endauth>
        <main>
            @yield('content')
        </main>
    </div>

    @include('partials/footer')

</body>

</html>
