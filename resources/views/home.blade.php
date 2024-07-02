@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Pesquisar</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('home') }}">
                            <div class="row">
                                <div class="col-md-3 col-sm-12">
                                    <label for="name" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $name }}">
                                </div>

                                <div class="col-md-3 col-sm-12">
                                    <label for="data_inicio" class="form-label">Data Início</label>
                                    <input type="date" class="form-control" id="data_inicio" name="data_inicio"
                                        value="{{ $data_inicio }}">
                                </div>

                                <div class="col-md-3 col-sm-12">
                                    <label for="data_fim" class="form-label">Data Fim</label>
                                    <input type="date" class="form-control" id="data_fim" name="data_fim"
                                        value="{{ $data_fim }}">
                                </div>

                                <div class="col-md-3 col-sm-12 mt-3 pt-3">
                                    <button type="submit" class="btn btn-info">Pesquisar</button>
                                    <a href="{{ route('home') }}" class="btn btn-warning">Limpar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <div class="card mt-3 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Lista de contas

                        <a href="{{ route('contas.create') }}" class="btn btn-primary">Cadastrar</a>

                    </div>

                    <div class="card-body">

                        <x-alert />

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
                                    @php

                                        if ($conta->situation == 'paid') {
                                            $status = 'success';
                                            $situation_name = 'Pago';
                                        } elseif ($conta->situation == 'pending') {
                                            $status = 'warning';
                                            $situation_name = 'Pendente';
                                        } else {
                                            $status = 'danger';
                                            $situation_name = 'Cancelado';
                                        }
                                    @endphp
                                    <tr>
                                        <th scope="row">{{ $conta->id }}</th>
                                        <td>{{ $conta->name }}</td>
                                        <td>{{ 'R$' . number_format($conta->value, 2, ',', '.') }}</td>
                                        <td>{{ date('d/m/Y', strtotime($conta->maturity)) }}</td>
                                        <td>{!! '<span class="badge text-bg-' . $status . ' "   >' . $situation_name . ' </span>' !!} </td>
                                        <td class="d-md-flex justify-content-center">
                                            <a href="{{ route('contas.show', ['id' => $conta->id]) }}"
                                                class="btn btn-primary me-1">Visualizar</a>
                                            <a href="{{ route('contas.edit', ['id' => $conta->id]) }}"
                                                class="btn btn-warning me-1">Editar</a>
                                            <form id="formExcluir{{ $conta->id }}"
                                                action="{{ route('contas.destroy', ['id' => $conta->id]) }}"
                                                method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                    onclick="confirmarExclusao(event, {{ $conta->id }})">Apagar</button>
                                            </form>

                                        </td>
                                    </tr>
                                @endforeach


                                @if (count($contas) == 0)
                                    <p class="text-center text-danger fw-bold">Nenhuma conta encontrada! </p>
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
