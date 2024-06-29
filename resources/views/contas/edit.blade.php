@extends('layouts.app')

@section('title', 'Editar')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Editar conta

                        <a href="{{ route('home') }}" class="btn btn-secondary">Voltar</a>

                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form action="{{ route('contas.update', ['id' => $contas->id]) }}" method="post">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="name" name="name" required
                                    value="{{ $contas->name }}">
                            </div>

                            <div class="mb-3">
                                <label for="value" class="form-label">Valor</label>
                                <input type="text" class="form-control" id="value" name="value" required
                                    value="{{ $contas->value }}">
                            </div>

                            <div class="mb-3">
                                <label for="maturity" class="form-label">Vencimento</label>
                                <input type="date" class="form-control" id="maturity" name="maturity" required
                                    value="{{ $contas->maturity }}">
                            </div>

                            <button type="submit" class="btn btn-primary">Editar</button>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
