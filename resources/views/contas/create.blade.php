@extends('layouts.app')

@section('title', 'Cadastrar')

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

                            <div class="row">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="value" class="form-label">Valor</label>
                                    <input type="text" class="form-control" id="value" name="value" required>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="maturity" class="form-label">Vencimento</label>
                                    <input type="date" class="form-control" id="maturity" name="maturity" required>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="situation" class="form-label">Situação</label>
                                    <select class="form-select" id="situation" name="situation" required>
                                        <option value="" selected disabled>selecione</option>
                                        <option value="paid">Pago</option>
                                        <option value="pending">Pendente</option>
                                        <option value="canceled">Cancelado</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Cadastrar</button>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
