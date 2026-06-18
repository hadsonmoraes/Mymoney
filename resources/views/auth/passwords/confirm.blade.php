@extends('layouts.app')

@section('title', 'Confirmar senha')

@section('content')
    <div class="auth-shell">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8 col-xl-6">
                    <div class="card auth-card border-0 shadow-lg overflow-hidden">
                        <div class="card-body p-4 p-lg-5">
                            <div class="mb-4 text-center">
                                <div class="metric-label mb-2">Confirmação</div>
                                <h2 class="page-title mb-1">{{ __('Confirm Password') }}</h2>
                                <p class="auth-note mb-0">{{ __('Please confirm your password before continuing.') }}</p>
                            </div>

                            <form method="POST" action="{{ route('password.confirm') }}" class="form-grid">
                                @csrf

                                <div class="form-section">
                                    <label for="password" class="form-label">{{ __('Password') }}</label>
                                    <div class="input-group">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                            name="password" required autocomplete="current-password" placeholder="Sua senha atual">
                                        <span class="input-group-text" onclick="togglePassword('password', this)">
                                            <i class="fa-regular fa-eye"></i>
                                        </span>
                                    </div>

                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-actions justify-content-between">
                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link px-0" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif

                                    <button type="submit" class="btn btn-primary">
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
