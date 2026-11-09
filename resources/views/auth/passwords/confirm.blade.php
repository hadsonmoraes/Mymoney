@extends('layouts.fullscreen-layout')

@section('title', 'Confirmar senha')

@section('content')
    <div class="auth-shell">
        <div class="mx-auto w-full max-w-6xl px-4 py-8">
            <div class="flex justify-center">
                <div class="w-full max-w-2xl">
                    <div class="auth-card overflow-hidden">
                        <div class="p-6 lg:p-12">
                            <div class="mb-4 text-center">
                                <div class="metric-label mb-2">Confirmação</div>
                                <h2 class="page-title mb-1">{{ __('Confirm Password') }}</h2>
                                <p class="auth-note mb-0">{{ __('Please confirm your password before continuing.') }}</p>
                            </div>

                            <form method="POST" action="{{ route('password.confirm') }}" class="form-grid">
                                @csrf

                                <div class="form-section">
                                    <label for="password" class="field-label">{{ __('Password') }}</label>
                                    <div class="flex">
                                        <input id="password" type="password"
                                            class="field-control min-w-0 flex-1 rounded-r-none @error('password') field-control-invalid @enderror"
                                            name="password" required autocomplete="current-password" placeholder="Sua senha atual">
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

                                <div class="form-actions justify-between">
                                    @if (Route::has('password.request'))
                                        <a class="action-button action-button-link px-0" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif

                                    <button type="submit" class="action-button action-button-primary">
                                        {{ __('Confirm Password') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
