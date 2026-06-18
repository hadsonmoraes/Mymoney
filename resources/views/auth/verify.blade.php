@extends('layouts.app')

@section('title', 'Verificar email')

@section('content')
    <div class="auth-shell">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-8 col-xl-6">
                    <div class="card auth-card border-0 shadow-lg overflow-hidden">
                        <div class="card-body p-4 p-lg-5 text-center">
                            <div class="auth-badge mb-4"><i class="fa-solid fa-shield-heart"></i> Verificação necessária</div>
                            <h2 class="page-title mb-2">{{ __('Verify Your Email Address') }}</h2>
                            <p class="auth-note mb-4">
                                {{ __('Before proceeding, please check your email for a verification link.') }}
                            </p>

                            @if (session('resent'))
                                <div class="alert alert-success text-start" role="alert">
                                    {{ __('A fresh verification link has been sent to your email address.') }}
                                </div>
                            @endif

                            <div class="form-section text-start">
                                <div class="form-section-title">Sem link?</div>
                                <div class="form-section-desc mb-3">
                                    {{ __('If you did not receive the email') }}, {{ __('click here to request another') }}.
                                </div>

                                <form class="d-flex justify-content-center" method="POST" action="{{ route('verification.resend') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Resend verification email') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
