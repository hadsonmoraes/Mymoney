@extends('layouts.fullscreen-layout')

@section('title', 'Cadastrar')

@section('content')
    <div class="auth-shell">
        <div class="mx-auto w-full max-w-6xl px-4 py-8">
            <div class="flex justify-center">
                <div class="w-full">
                    <div class="auth-card overflow-hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-12">
                            <div class="auth-aside flex flex-col justify-between p-6 lg:col-span-5 lg:p-12">
                                <div>
                                    <span class="auth-badge mb-4"><i class="fa-solid fa-user-plus"></i> Criação rápida de
                                        conta</span>
                                    <h1 class="mb-3">Comece com uma interface mais sofisticada.</h1>
                                    <p class="mb-4">Em poucos passos você cria sua conta e já entra no painel para
                                        controlar suas finanças.</p>

                                    <div class="summary-list">
                                        <div class="summary-item">
                                            <strong>Cadastro simples</strong>
                                            <span>Menos atrito</span>
                                        </div>
                                        <div class="summary-item">
                                            <strong>Experiência fluida</strong>
                                            <span>Layout responsivo</span>
                                        </div>
                                        <div class="summary-item">
                                            <strong>Visual moderno</strong>
                                            <span>Mais profissional</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="auth-note mt-4 text-white/70">
                                    Crie sua conta e acompanhe tudo em um painel organizado.
                                </div>
                            </div>

                            <div class="bg-white dark:bg-gray-800 lg:col-span-7">
                                <div class="p-6 lg:p-12">
                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="name" class="field-label">{{ __('Name') }}</label>
                                            <input id="name" type="text"
                                                class="field-control @error('name') field-control-invalid @enderror" name="name"
                                                value="{{ old('name') }}" required autocomplete="name" autofocus>
                                            @error('name')
                                                <div class="field-error block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="field-label">{{ __('Email Address') }}</label>
                                            <input id="email" type="email"
                                                class="field-control @error('email') field-control-invalid @enderror" name="email"
                                                value="{{ old('email') }}" required autocomplete="email">
                                            @error('email')
                                                <div class="field-error block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="field-label">{{ __('Password') }}</label>
                                            <div class="flex">
                                                <input id="password" type="password"
                                                    class="field-control min-w-0 flex-1 rounded-r-none @error('password') field-control-invalid @enderror"
                                                    name="password" required autocomplete="new-password">
                                                <button type="button"
                                                    class="inline-flex shrink-0 items-center justify-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-500 hover:bg-gray-100 focus-visible:ring-2 focus-visible:ring-brand-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700"
                                                    aria-label="Mostrar senha"
                                                    aria-controls="password" onclick="togglePassword('password', this)">
                                                    <x-svg.eye class="size-5" data-password-icon="visible" />
                                                    <x-svg.eye-close class="hidden size-5" data-password-icon="hidden" />
                                                </button>
                                            </div>
                                            @error('password')
                                                <div class="field-error block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label for="password-confirm"
                                                class="field-label">{{ __('Confirm Password') }}</label>
                                            <div class="flex">
                                                <input id="password-confirm" type="password"
                                                    class="field-control min-w-0 flex-1 rounded-r-none"
                                                    name="password_confirmation" required autocomplete="new-password">
                                                <button type="button"
                                                    class="inline-flex shrink-0 items-center justify-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-500 hover:bg-gray-100 focus-visible:ring-2 focus-visible:ring-brand-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700"
                                                    aria-label="Mostrar senha"
                                                    aria-controls="password-confirm"
                                                    onclick="togglePassword('password-confirm', this)">
                                                    <x-svg.eye class="size-5" data-password-icon="visible" />
                                                    <x-svg.eye-close class="hidden size-5" data-password-icon="hidden" />
                                                </button>
                                            </div>
                                        </div>

                                        <div class="form-actions justify-end">
                                            <button type="submit" class="action-button action-button-primary action-button-lg">
                                                {{ __('Register') }}
                                            </button>
                                        </div>
                                    </form>

                                    <div class="text-center mt-4 auth-note">
                                        Já tem conta?
                                        <a href="{{ route('login') }}" class="font-semibold no-underline">Entrar</a>
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
