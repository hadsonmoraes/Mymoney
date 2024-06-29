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
                            <div class="alert alert-success text-center" role="alert">
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
                                            <a href="{{ route('contas.show', ['id' => $conta->id]) }}"
                                                class="btn btn-primary me-1">Visualizar</a>
                                            <a href="{{ route('contas.edit', ['id' => $conta->id]) }}"
                                                class="btn btn-warning me-1">Editar</a>
                                            <form action="{{ route('contas.destroy', ['id' => $conta->id]) }}"
                                                method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Apagar</button>
                                            </form>

                                        </td>
                                    </tr>
                                @endforeach


                                @if (count($contas) == 0)
                                    <p>Nenhuma conta encontrada </p>
                                @endif


                            </tbody>

                        </table>
                        {{ $contas->onEachSide(0)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
