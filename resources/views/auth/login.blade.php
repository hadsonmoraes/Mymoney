@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="auth-shell">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-11 col-xxl-10">
                    @if (session('status'))
                        <div class="alert alert-success mb-4" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="card auth-card border-0 shadow-lg overflow-hidden">
                        <div class="row g-0">
                            <div class="col-lg-5 auth-aside d-flex flex-column justify-content-between p-4">
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

                                <div class="auth-note mt-4 text-white-50">
                                    Organize sua rotina financeira com menos cliques e mais clareza.
                                </div>
                            </div>

                            <div class="col-lg-7 bg-body-tertiary">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <div class="metric-label mb-2">Bem-vindo de volta</div>
                                        <h2 class="page-title mb-1">Entrar na sua conta</h2>
                                        <p class="auth-note mb-0">Use seu e-mail e senha para continuar.</p>
                                    </div>

                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="email" class="form-label">{{ __('Email') }}</label>
                                            <input id="email" type="email"
                                                class="form-control @error('email') is-invalid @enderror" name="email"
                                                value="{{ old('email') }}" required autofocus autocomplete="username">
                                            @error('email')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">{{ __('Password') }}</label>
                                            <div class="input-group">
                                                <input id="password" type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    name="password" required autocomplete="current-password">
                                                <span class="input-group-text" onclick="togglePassword('password', this)">
                                                    <i class="fa-regular fa-eye"></i>
                                                </span>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                                            <div class="form-check">
                                                <input id="remember_me" type="checkbox" class="form-check-input"
                                                    name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="remember_me">{{ __('Remember me') }}</label>
                                            </div>
                                            @if (Route::has('password.request'))
                                                <a class="btn btn-link px-0" href="{{ route('password.request') }}">
                                                    {{ __('Forgot your password?') }}
                                                </a>
                                            @endif
                                        </div>

                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                {{ __('Log in') }}
                                            </button>
                                        </div>
                                    </form>

                                    <div class="text-center mt-4 auth-note">
                                        Não tem conta?
                                        <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Criar
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
