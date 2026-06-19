@extends('layouts.app')

@section('title', 'Nova senha')

@section('content')
    <div class="auth-shell">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10 col-xl-8 col-xxl-7">
                    <div class="card auth-card border-0 shadow-lg overflow-hidden">
                        <div class="row g-0">
                            <div class="col-lg-5 auth-aside d-flex flex-column justify-content-between p-4">
                                <div>
                                    <span class="auth-badge mb-4"><i class="fa-solid fa-key"></i> Nova senha</span>
                                    <h1 class="mb-3">Escolha uma senha nova e forte.</h1>
                                    <p class="mb-4">Use o formulário ao lado para redefinir o acesso da sua conta.</p>
                                </div>
                                <div class="auth-note mt-4 text-white-50">
                                    Depois de salvar, você poderá entrar novamente com a senha nova.
                                </div>
                            </div>

                            <div class="col-lg-7 bg-body-tertiary">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <div class="metric-label mb-2">Redefinição</div>
                                        <h2 class="page-title mb-1">{{ __('Reset Password') }}</h2>
                                        <p class="auth-note mb-0">Confirme seu e-mail e crie a nova senha.</p>
                                    </div>

                                    <form method="POST" action="{{ route('password.update') }}" class="form-grid">
                                        @csrf

                                        <input type="hidden" name="token" value="{{ $token }}">

                                        <div class="form-section">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label for="email"
                                                        class="form-label">{{ __('Email Address') }}</label>
                                                    <input id="email" type="email"
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        name="email" value="{{ $email ?? old('email') }}" required
                                                        autocomplete="email" autofocus placeholder="nome@exemplo.com">
                                                    @error('email')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-12 col-lg-6">
                                                    <label for="password" class="form-label">{{ __('Password') }}</label>
                                                    <div class="input-group">
                                                        <input id="password" type="password"
                                                            class="form-control @error('password') is-invalid @enderror"
                                                            name="password" required autocomplete="new-password"
                                                            placeholder="Nova senha">
                                                        <span class="input-group-text"
                                                            onclick="togglePassword('password', this)">
                                                            <i class="fa-regular fa-eye"></i>
                                                        </span>
                                                    </div>
                                                    @error('password')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-12 col-lg-6">
                                                    <label for="password-confirm"
                                                        class="form-label">{{ __('Confirm Password') }}</label>
                                                    <div class="input-group">
                                                        <input id="password-confirm" type="password" class="form-control"
                                                            name="password_confirmation" required
                                                            autocomplete="new-password" placeholder="Repita a senha">
                                                        <span class="input-group-text"
                                                            onclick="togglePassword('password-confirm', this)">
                                                            <i class="fa-regular fa-eye"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-actions justify-content-between">
                                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                                                {{ __('Back') }}
                                            </a>

                                            <button type="submit" class="btn btn-primary">
                                                {{ __('Reset Password') }}
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
