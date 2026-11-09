@extends('layouts.fullscreen-layout')

@section('title', 'Nova senha')

@section('content')
    <div class="auth-shell">
        <div class="mx-auto w-full max-w-6xl px-4 py-8">
            <div class="flex justify-center">
                <div class="w-full max-w-5xl">
                    <div class="auth-card overflow-hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-12">
                            <div class="auth-aside flex flex-col justify-between p-6 lg:col-span-5 lg:p-12">
                                <div>
                                    <span class="auth-badge mb-4"><i class="fa-solid fa-key"></i> Nova senha</span>
                                    <h1 class="mb-3">Escolha uma senha nova e forte.</h1>
                                    <p class="mb-4">Use o formulário ao lado para redefinir o acesso da sua conta.</p>
                                </div>
                                <div class="auth-note mt-4 text-white/70">
                                    Depois de salvar, você poderá entrar novamente com a senha nova.
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 lg:col-span-7">
                                <div class="p-6 lg:p-12">
                                    <div class="mb-4">
                                        <div class="metric-label mb-2">Redefinição</div>
                                        <h2 class="page-title mb-1">{{ __('Reset Password') }}</h2>
                                        <p class="auth-note mb-0">Confirme seu e-mail e crie a nova senha.</p>
                                    </div>

                                    <form method="POST" action="{{ route('password.update') }}" class="form-grid">
                                        @csrf

                                        <input type="hidden" name="token" value="{{ $token }}">

                                        <div class="form-section">
                                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                                <div class="md:col-span-2">
                                                    <label for="email"
                                                        class="field-label">{{ __('Email Address') }}</label>
                                                    <input id="email" type="email"
                                                        class="field-control @error('email') field-control-invalid @enderror"
                                                        name="email" value="{{ $email ?? old('email') }}" required
                                                        autocomplete="email" autofocus placeholder="nome@exemplo.com">
                                                    @error('email')
                                                        <div class="field-error block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label for="password" class="field-label">{{ __('Password') }}</label>
                                                    <div class="flex">
                                                        <input id="password" type="password"
                                                            class="field-control min-w-0 flex-1 rounded-r-none @error('password') field-control-invalid @enderror"
                                                            name="password" required autocomplete="new-password"
                                                            placeholder="Nova senha">
                                                        <button type="button"
                                                            class="inline-flex shrink-0 items-center justify-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-500 hover:bg-gray-100 focus-visible:ring-2 focus-visible:ring-brand-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700"
                                                            aria-label="Mostrar senha" aria-controls="password"
                                                            onclick="togglePassword('password', this)">
                                                            <x-svg.eye class="size-5" data-password-icon="visible" />
                                                            <x-svg.eye-close class="hidden size-5"
                                                                data-password-icon="hidden" />
                                                        </button>
                                                    </div>
                                                    @error('password')
                                                        <div class="field-error block">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label for="password-confirm"
                                                        class="field-label">{{ __('Confirm Password') }}</label>
                                                    <div class="flex">
                                                        <input id="password-confirm" type="password"
                                                            class="field-control min-w-0 flex-1 rounded-r-none"
                                                            name="password_confirmation" required
                                                            autocomplete="new-password" placeholder="Repita a senha">
                                                        <button type="button"
                                                            class="inline-flex shrink-0 items-center justify-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-500 hover:bg-gray-100 focus-visible:ring-2 focus-visible:ring-brand-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700"
                                                            aria-label="Mostrar senha" aria-controls="password-confirm"
                                                            onclick="togglePassword('password-confirm', this)">
                                                            <x-svg.eye class="size-5" data-password-icon="visible" />
                                                            <x-svg.eye-close class="hidden size-5"
                                                                data-password-icon="hidden" />
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-actions justify-between">
                                            <a href="{{ route('login') }}" class="action-button action-button-outline-secondary">
                                                {{ __('Back') }}
                                            </a>

                                            <button type="submit" class="action-button action-button-primary">
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
