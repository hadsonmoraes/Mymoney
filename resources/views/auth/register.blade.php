@extends('layouts.app')

@section('title', 'Cadastrar')

@section('content')
    <div class="auth-shell">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-11 col-xxl-10">
                    <div class="card auth-card border-0 shadow-lg overflow-hidden">
                        <div class="row g-0">
                            <div class="col-lg-5 auth-aside d-flex flex-column justify-content-between p-4">
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

                                <div class="auth-note mt-4 text-white-50">
                                    Crie sua conta e acompanhe tudo em um painel organizado.
                                </div>
                            </div>

                            <div class="col-lg-7 bg-body-tertiary">
                                <div class="card-body">
                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="name" class="form-label">{{ __('Name') }}</label>
                                            <input id="name" type="text"
                                                class="form-control @error('name') is-invalid @enderror" name="name"
                                                value="{{ old('name') }}" required autocomplete="name" autofocus>
                                            @error('name')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                            <input id="email" type="email"
                                                class="form-control @error('email') is-invalid @enderror" name="email"
                                                value="{{ old('email') }}" required autocomplete="email">
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">{{ __('Password') }}</label>
                                            <div class="input-group">
                                                <input id="password" type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    name="password" required autocomplete="new-password">
                                                <span class="input-group-text" onclick="togglePassword('password', this)">
                                                    <i class="fa-regular fa-eye"></i>
                                                </span>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label for="password-confirm"
                                                class="form-label">{{ __('Confirm Password') }}</label>
                                            <div class="input-group">
                                                <input id="password-confirm" type="password" class="form-control"
                                                    name="password_confirmation" required autocomplete="new-password">
                                                <span class="input-group-text"
                                                    onclick="togglePassword('password-confirm', this)">
                                                    <i class="fa-regular fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-actions justify-content-end">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                {{ __('Register') }}
                                            </button>
                                        </div>
                                    </form>

                                    <div class="text-center mt-4 auth-note">
                                        Já tem conta?
                                        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Entrar</a>
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
