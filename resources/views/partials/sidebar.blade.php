    @if (Auth::user())
        <div class="flex flex-col h-screen">
            <nav id="sidebar"
                class="h-screen fixed flex flex-col {{ auth()->check() && auth()->user()->sidebar === 0 ? 'collapsed' : '' }}">
                <div class="sidebar-header text-center p-3">
                    <a class="inline-flex items-center" href="{{ url('/') }}">
                        <img src="{{ asset('logo.png') }}" alt="Mymoney" class="sidebar-brand w-full"
                            style="max-width: 120px; height: auto;">
                    </a>
                    <span class="sidebar-badge mt-3">Financeiro inteligente</span>
                    <button class="action-button action-button-outline-light w-full mt-3" id="toggleSidebar"
                        aria-label="Alternar menu lateral" aria-controls="sidebar"
                        aria-expanded="{{ auth()->check() && auth()->user()->sidebar === 0 ? 'false' : 'true' }}">☰</button>
                </div>
                <ul class="flex flex-col p-3 flex-1">
                    <li class="w-full">
                        <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 no-underline hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 sidebar-text {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <span class="sidebar-icon"><i class="fa-solid fa-chart-line"></i></span>
                            <span class="sidebar-label">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                    <li class="w-full">
                        <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 no-underline hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 sidebar-text {{ request()->routeIs('home') ? 'active' : '' }}"
                            href="{{ route('home') }}">
                            <span class="sidebar-icon"><i class="fa-solid fa-house"></i></span>
                            <span class="sidebar-label">{{ __('Home') }}</span>
                        </a>
                    </li>
                    <li class="w-full">
                        <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 no-underline hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 sidebar-text {{ request()->routeIs('category.index') ? 'active' : '' }}"
                            href="{{ route('category.index') }}">
                            <span class="sidebar-icon"><i class="fa-solid fa-list"></i></span>
                            <span class="sidebar-label">{{ 'Categoria' }}</span>
                        </a>
                    </li>
                    <li class="w-full">
                        <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 no-underline hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 sidebar-text {{ request()->routeIs('lembretes.*') ? 'active' : '' }}"
                            href="{{ route('lembretes.index') }}">
                            <span class="sidebar-icon relative">
                                <i class="fa-solid fa-bell"></i>
                                @if (auth()->check() && auth()->user()->unreadNotificationsCount() > 0)
                                    <span
                                        class="absolute top-0 start-full -translate-x-1/2 -translate-y-1/2 p-1 bg-error-600 text-white border border-white rounded-full">
                                        <span class="sr-only">Novos lembretes</span>
                                    </span>
                                @endif
                            </span>
                            <span class="sidebar-label inline-flex items-center justify-between">
                                <span class="sidebar-label">{{ __('Lembretes') }}</span>
                                @if (auth()->check() && auth()->user()->unreadNotificationsCount() > 0)
                                    <span
                                        class="status-badge bg-error-600 text-white rounded-full ms-2">{{ auth()->user()->unreadNotificationsCount() }}</span>
                                @endif
                            </span>
                        </a>
                    </li>
                </ul>
                <ul class="flex flex-col p-3 mt-auto">
                    @guest
                        @if (Route::has('login'))
                            <li class="w-full">
                                <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 no-underline hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 sidebar-text" href="{{ route('login') }}">
                                    <span class="sidebar-icon"><i class="fa-solid fa-key"></i></span>
                                    <span class="sidebar-label">{{ __('Login') }}</span>
                                </a>
                            </li>
                        @endif
                        @if (Route::has('register'))
                            <li class="w-full">
                                <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 no-underline hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 sidebar-text" href="{{ route('register') }}">
                                    <span class="sidebar-icon"><i class="fa-solid fa-pen-to-square"></i></span>
                                    <span class="sidebar-label">{{ __('Register') }}</span>
                                </a>
                            </li>
                        @endif
                    @else
                        <li class="w-full">
                            <button class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 no-underline hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 sidebar-text" id="toggleTheme">
                                <span class="sidebar-icon"><i class="fa-solid" id="themeIcon"></i></span>
                                <span class="sidebar-label cursor-pointer" id="themeText"></span>
                            </button>
                        </li>
                        <li class="w-full relative sidebar-account-menu" data-ui-dropdown>
                            <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 no-underline hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 sidebar-text cursor-pointer" href="#" role="button"
                                data-ui-dropdown-toggle aria-expanded="false">
                                <span class="sidebar-icon"><i class="fa-solid fa-user"></i></span>
                                <span class="sidebar-label">{{ strtoupper(Auth::user()->name) }}</span>
                            </a>
                            <div class="hidden floating-menu shadow sidebar-user-dropdown">
                                <a class="floating-menu-item" href="{{ route('profile.edit', ['id' => Auth::user()->id]) }}">
                                    {{ __('Profile') }}
                                </a>
                                <a class="floating-menu-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
                <div class="sidebar-footer p-3">
                    <small class="text-white/70 block sidebar-label">Controle financeiro com uma interface mais limpa
                        e rápida.</small>
                </div>
            </nav>
    @endif
