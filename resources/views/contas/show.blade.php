@extends('layouts.app')

@section('title', 'Create')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Criar conta

                        <a href="{{ route('home') }}" class="btn btn-secondary">Voltar</a>

                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ route('contas.store') }}" method="post">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="value" class="form-label">Valor</label>
                                <input type="text" class="form-control" id="value" name="value" required>
                            </div>

                            <div class="mb-3">
                                <label for="maturity" class="form-label">Valor</label>
                                <input type="date" class="form-control" id="maturity" name="maturity" required>
                            </div>

                            <button type="submit" class="btn btn-primary">Cadastrar</button>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
