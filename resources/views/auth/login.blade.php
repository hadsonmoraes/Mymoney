@extends('layouts.fullscreen-layout')

@section('title', 'Login')

@section('content')
    <div class="auth-shell">
        <div class="mx-auto w-full max-w-6xl px-4 py-8">
            <div class="flex justify-center">
                <div class="w-full">
                    @if (session('status'))
                        <div class="mb-4 rounded-lg border border-success-200 bg-success-50 p-3 text-success-700" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="auth-card overflow-hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-12">
                            <div class="auth-aside flex flex-col justify-between p-6 lg:col-span-5 lg:p-12">
                                <div>
                                    <span class="auth-badge mb-4"><i class="fa-solid fa-chart-line"></i> Controle financeiro
                                        moderno</span>
                                    <h1 class="mb-3">Uma visão mais limpa das suas finanças.</h1>
                                    <p class="mb-4">Acesse seu painel com uma experiência mais rápida, elegante e fácil de
                                        usar.</p>

                                    <div class="summary-list">
                                        <div class="summary-item">
                                            <strong>Dashboard visual</strong>
                                            <span>Indicadores claros</span>
                                        </div>
                                        <div class="summary-item">
                                            <strong>Gestão rápida</strong>
                                            <span>Entradas e saídas</span>
                                        </div>
                                        <div class="summary-item">
                                            <strong>Interface atual</strong>
                                            <span>Modo claro e escuro</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="auth-note mt-4 text-white/70">
                                    Organize sua rotina financeira com menos cliques e mais clareza.
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 lg:col-span-7">
                                <div class="p-6 lg:p-12">
                                    <div class="mb-4">
                                        <div class="metric-label mb-2">Bem-vindo de volta</div>
                                        <h2 class="page-title mb-1">Entrar na sua conta</h2>
                                        <p class="auth-note mb-0">Use seu e-mail e senha para continuar.</p>
                                    </div>

                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="email" class="field-label">{{ __('Email') }}</label>
                                            <input id="email" type="email"
                                                class="field-control @error('email') field-control-invalid @enderror" name="email"
                                                value="{{ old('email') }}" required autofocus autocomplete="username">
                                            @error('email')
                                                <div class="field-error block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="field-label">{{ __('Password') }}</label>
                                            <div class="flex">
                                                <input id="password" type="password"
                                                    class="field-control min-w-0 flex-1 rounded-r-none @error('password') field-control-invalid @enderror"
                                                    name="password" required autocomplete="current-password">
                                                <button type="button"
                                                    class="inline-flex shrink-0 items-center justify-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-500 hover:bg-gray-100 focus-visible:ring-2 focus-visible:ring-brand-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700"
                                                    aria-label="Mostrar senha"
                                                    aria-controls="password" onclick="togglePassword('password', this)">
                                                    <x-svg.eye class="size-5" data-password-icon="visible" />
                                                    <x-svg.eye-close class="hidden size-5" data-password-icon="hidden" />
                                                </button>
                                            </div>
                                            @error('password')
                                                <div class="field-error block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                                            <div class="flex items-start gap-2">
                                                <input id="remember_me" type="checkbox"
                                                    class="mt-1 size-4 shrink-0 accent-brand-500"
                                                    name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <label class="text-sm text-gray-700 dark:text-gray-300"
                                                    for="remember_me">{{ __('Remember me') }}</label>
                                            </div>
                                            @if (Route::has('password.request'))
                                                <a class="action-button action-button-link px-0" href="{{ route('password.request') }}">
                                                    {{ __('Forgot your password?') }}
                                                </a>
                                            @endif
                                        </div>

                                        <div class="grid">
                                            <button type="submit" class="action-button action-button-primary action-button-lg">
                                                {{ __('Log in') }}
                                            </button>
                                        </div>
                                    </form>

                                    <div class="text-center mt-4 auth-note">
                                        Não tem conta?
                                        <a href="{{ route('register') }}" class="font-semibold no-underline">Criar
                                            cadastro</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
