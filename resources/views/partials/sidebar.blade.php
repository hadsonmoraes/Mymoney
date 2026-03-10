    @if (Auth::user())
        <div class="d-flex flex-column vh-100">
            <nav id="sidebar"
                class="bg-primary bg-gradient text-white shadow-sm vh-100 position-fixed d-flex flex-column {{ auth()->check() && auth()->user()->sidebar === 0 ? 'collapsed' : '' }}">
                <div class="sidebar-header text-center p-3">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{-- <img src="{{asset(" ..\logo.png")}}" alt=""> {{ config('app.name', 'Mymoney') }} --}}
                        <img src="{{ asset('logo.png') }}" alt="Mymoney" class="w-100" style="height: 100px">
                    </a>
                    <button class="btn btn-outline-light w-100 mt-2" id="toggleSidebar"
                        aria-label="Alternar menu lateral" aria-controls="sidebar"
                        aria-expanded="{{ auth()->check() && auth()->user()->sidebar === 0 ? 'false' : 'true' }}">☰</button>
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
                            <span class="sidebar-label">{{ 'Categoria' }}</span>
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
                            <button class="nav-link sidebar-text" id="toggleTheme">
                                <span class="sidebar-icon"><i class="fa-solid " id="themeIcon"></i></span>
                                <span class="sidebar-label cursor-pointer" id="themeText"></span>
                            </button>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link sidebar-text dropdown-toggle" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
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
