@extends('layouts.fullscreen-layout')

@section('title', 'Verificar email')

@section('content')
    <div class="auth-shell">
        <div class="mx-auto w-full max-w-6xl px-4 py-8">
            <div class="flex justify-center">
                <div class="w-full max-w-2xl">
                    <div class="auth-card overflow-hidden">
                        <div class="p-6 text-center lg:p-12">
                            <div class="auth-badge mb-4"><i class="fa-solid fa-shield-heart"></i> Verificação necessária</div>
                            <h2 class="page-title mb-2">{{ __('Verify Your Email Address') }}</h2>
                            <p class="auth-note mb-4">
                                {{ __('Before proceeding, please check your email for a verification link.') }}
                            </p>

                            @if (session('resent'))
                                <div class="rounded-lg border border-success-200 bg-success-50 p-3 text-left text-success-700" role="alert">
                                    {{ __('A fresh verification link has been sent to your email address.') }}
                                </div>
                            @endif

                            <div class="form-section text-left">
                                <div class="form-section-title">Sem link?</div>
                                <div class="form-section-desc mb-3">
                                    {{ __('If you did not receive the email') }}, {{ __('click here to request another') }}.
                                </div>

                                <form class="flex justify-center" method="POST" action="{{ route('verification.resend') }}">
                                    @csrf
                                    <button type="submit" class="action-button action-button-primary">
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
