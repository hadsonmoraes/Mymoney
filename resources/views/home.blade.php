@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Lista de contas

                        <a href="{{ route('contas.create') }}" class="btn btn-primary">Cadastrar</a>

                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Id</th>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Valor</th>
                                    <th scope="col">Vencimento</th>
                                    <th scope="col">Situação</th>
                                    <th scope="col" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contas as $conta)
                                    <tr>
                                        <th scope="row">{{ $conta->id }}</th>
                                        <td>{{ $conta->name }}</td>
                                        <td>{{ 'R$' . number_format($conta->value, 2, ',', '.') }}</td>
                                        <td>{{ date('d/m/Y', strtotime($conta->maturity)) }}</td>
                                        <td></td>
                                        <td class="d-md-flex justify-content-center">
                                            <a href="" class="btn btn-primary me-1">Visualizar</a>
                                            <a href="" class="btn btn-warning me-1">Editar</a>
                                            <a href="" class="btn btn-danger">Apagar</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
