@extends('layouts.app')

@section('title', 'Recuperar senha')

@section('content')
    <div class="auth-shell">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10 col-xl-8 col-xxl-7">
                    <div class="card auth-card border-0 shadow-lg overflow-hidden">
                        <div class="row g-0">
                            <div class="col-lg-5 auth-aside d-flex flex-column justify-content-between">
                                <div>
                                    <span class="auth-badge mb-4"><i class="fa-solid fa-envelope"></i> Recuperação de acesso</span>
                                    <h1 class="mb-3">Redefina sua senha com segurança.</h1>
                                    <p class="mb-4">Informe seu e-mail e enviaremos um link para criar uma nova senha.</p>
                                </div>
                                <div class="auth-note mt-4 text-white-50">
                                    Se o e-mail estiver cadastrado, o link chegará em alguns instantes.
                                </div>
                            </div>

                            <div class="col-lg-7 bg-body-tertiary">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <div class="metric-label mb-2">Recuperação</div>
                                        <h2 class="page-title mb-1">{{ __('Reset Password') }}</h2>
                                        <p class="auth-note mb-0">Digite o endereço de e-mail da conta.</p>
                                    </div>

                                    @if (session('status'))
                                        <div class="alert alert-success" role="alert">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('password.email') }}" class="form-grid">
                                        @csrf

                                        <div class="form-section">
                                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                                placeholder="nome@exemplo.com">
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-actions justify-content-between">
                                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                                                {{ __('Back') }}
                                            </a>

                                            <button type="submit" class="btn btn-primary btn-lg">
                                                {{ __('Send Password Reset Link') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
