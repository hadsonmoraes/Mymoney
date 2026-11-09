@extends('layouts.fullscreen-layout')

@section('title', 'Recuperar senha')

@section('content')
    <div class="auth-shell">
        <div class="mx-auto w-full max-w-6xl px-4 py-8">
            <div class="flex justify-center">
                <div class="w-full max-w-5xl">
                    <div class="auth-card overflow-hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-12">
                            <div class="auth-aside flex flex-col justify-between p-6 lg:col-span-5 lg:p-12">
                                <div>
                                    <span class="auth-badge mb-4"><i class="fa-solid fa-envelope"></i> Recuperação de
                                        acesso</span>
                                    <h1 class="mb-3">Redefina sua senha com segurança.</h1>
                                    <p class="mb-4">Informe seu e-mail e enviaremos um link para criar uma nova senha.</p>
                                </div>
                                <div class="auth-note mt-4 text-white/70">
                                    Se o e-mail estiver cadastrado, o link chegará em alguns instantes.
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 lg:col-span-7">
                                <div class="p-6 lg:p-12">
                                    <div class="mb-4">
                                        <div class="metric-label mb-2">Recuperação</div>
                                        <h2 class="page-title mb-1">{{ __('Reset Password') }}</h2>
                                        <p class="auth-note mb-0">Digite o endereço de e-mail da conta.</p>
                                    </div>

                                    @if (session('status'))
                                        <div class="rounded-lg border border-success-200 bg-success-50 p-3 text-success-700" role="alert">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('password.email') }}" class="form-grid">
                                        @csrf

                                        <div class="form-section">
                                            <label for="email" class="field-label">{{ __('Email Address') }}</label>
                                            <input id="email" type="email"
                                                class="field-control @error('email') field-control-invalid @enderror" name="email"
                                                value="{{ old('email') }}" required autocomplete="email" autofocus
                                                placeholder="nome@exemplo.com">
                                            @error('email')
                                                <div class="field-error block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-actions justify-between">
                                            <a href="{{ route('login') }}" class="action-button action-button-outline-secondary">
                                                {{ __('Back') }}
                                            </a>

                                            <button type="submit" class="action-button action-button-primary action-button-lg">
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
