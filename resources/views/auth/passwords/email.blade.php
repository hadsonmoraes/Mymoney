@extends('layouts.app')

@section('title', 'alterar senha')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-lg border-0 rounded-top-4 rounded-bottom-4">
                    <div class="card-header bg-gradient-primary text-white text-center py-3 rounded-top-4">
                        <h4 class="mb-0">{{ __('Reset Password') }}</h4>
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="col-md-4 col-form-label ">{{ __('Email Address') }}</label>


                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">

                                <button type="submit" class="btn btn-primary btn-lg">
                                    {{ __('Send Password Reset Link') }}
                                </button>


                                <a href="{{ route('login') }}" class="btn btn-secondary btn-lg">
                                    {{ __('Back') }}
                                </a>

                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .bg-gradient-primary {
            background: linear-gradient(90deg, rgba(0, 123, 255, 1) 0%, rgba(2, 117, 216, 1) 100%);
        }

        .invalid-feedback {
            display: block;
        }
    </style>
@endsection
