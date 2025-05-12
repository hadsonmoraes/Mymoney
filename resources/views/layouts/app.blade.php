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
    @vite(['resources/css/app.css','resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="theme-light">
    @if (Auth::user())
    <div class="d-flex">
        <nav id="sidebar" class="bg-primary bg-gradient text-white shadow-sm vh-100 position-fixed d-flex flex-column">
            <div class="sidebar-header text-center p-3">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{-- <img src="{{asset(" ..\logo.png")}}" alt=""> {{ config('app.name', 'Mymoney') }} --}}
                    <img src="{{asset("logo.png")}}" alt="Mymoney" class="w-100" style="height: 100px" >
                </a>
                <button class="btn btn-outline-light w-100 mt-2" id="toggleSidebar">☰</button>
            </div>
            <ul class="nav flex-column p-3 flex-grow-1">
                <li class="nav-item">
                    <a class="nav-link sidebar-text {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <span class="sidebar-icon"><i class="fa-solid fa-chart-line"></i></span>
                        <span class="sidebar-label">{{ __('Dashboard') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-text {{ request()->routeIs('home') ? 'active' : '' }}"
                        href="{{ route('home') }}">
                        <span class="sidebar-icon"><i class="fa-solid fa-house"></i></span>
                        <span class="sidebar-label">{{ __('Home') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link sidebar-text {{ request()->routeIs('category.index') ? 'active' : '' }}"
                        href="{{ route('category.index') }}">
                        <span class="sidebar-icon"><i class="fa-solid fa-list"></i></span>
                        <span class="sidebar-label">{{ __('Categoria') }}</span>
                    </a>
                </li>
            </ul>
            <ul class="nav flex-column p-3 mt-auto">
                @guest
                @if (Route::has('login'))
                <li class="nav-item">
                    <a class="nav-link sidebar-text" href="{{ route('login') }}">
                        <span class="sidebar-icon"><i class="fa-solid fa-key"></i></span>
                        <span class="sidebar-label">{{ __('Login') }}</span>
                    </a>
                </li>
                @endif
                @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link sidebar-text" href="{{ route('register') }}">
                        <span class="sidebar-icon"><i class="fa-solid fa-pen-to-square"></i></span>
                        <span class="sidebar-label">{{ __('Register') }}</span>
                    </a>
                </li>
                @endif
                @else
                <li class="nav-item">
                    <button class="nav-link sidebar-text" onclick="toggleTheme()">
                        <span class="sidebar-icon"><i class="fa-solid " id="themeIcon"></i></span>
                        <span class="sidebar-label cursor-pointer" id="themeText"></span>
                    </button>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link sidebar-text dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <span class="sidebar-icon"><i class="fa-solid fa-user"></i></span>
                        <span class="sidebar-label">{{ strtoupper(Auth::user()->name) }}</span>
                    </a>
                    <div class="dropdown-menu shadow">
                        <a class="dropdown-item" href="{{ route('profile.edit', ['id' => Auth::user()->id]) }}">
                            {{ __('Profile') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
                @endguest
            </ul>
        </nav>

        @endif
        <div id="content">
            <main>
                @yield('content')
            </main>
        </div>
    </div>

    @if (Auth::user())
    <style>
        #sidebar {
            width: 250px;
            transition: width 0.3s;
            overflow: hidden;
            background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
        }

        #sidebar.collapsed {
            width: 80px;
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff !important;
            border-radius: 5px;
        }

        .nav-link.active:hover {
            color: #d3d3d3 !important;
        }


        @media (max-width: 768px) {

            /* Ajusta quando a tela for menor que 768px */
            #sidebar {
                width: 80px;
            }

            #content {
                margin-left: 80px !important;
                width: calc(100% - 80px) !important;
            }

            /* Esconde os textos dentro da sidebar quando responsiva */
            #sidebar .sidebar-label {
                display: none;
            }
        }


        #content {
            margin-left: 250px;
            width: calc(100% - 250px);
            transition: margin-left 0.3s, width 0.3s;
        }

        #sidebar.collapsed+#content {
            margin-left: 80px;
            width: calc(100% - 80px);
        }

        .sidebar-text {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
        }

        .sidebar-text:hover {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #d3d3d3;
        }

        .sidebar-icon {
            font-size: 20px;
        }

        #sidebar.collapsed .sidebar-label {
            display: none;
        }

        .dropdown-menu {
            position: fixed !important;
            /* Permite que o dropdown fique fora da sidebar */
        }


        /* Estilos padrão (claro) */
        .theme-light {
        background-color: #fff;
        color: #222;
        }

        /* Estilos do modo escuro */
        .theme-dark {
        background-color: #222;
        color: #fff;
        }

        .theme-dark #sidebar {
        background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%) !important;
        background-size: cover;
        }

    </style>

    @endif
</body>

</html>
