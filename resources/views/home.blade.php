@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Filtro</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('home') }}">
                            <div class="row">
                                <div class="col-md-3 col-sm-12">
                                    <label for="name" class="form-label fw-bold">Nome</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $name }}">
                                </div>

                                <div class="col-md-2 col-sm-12">
                                    <label for="data_inicio" class="form-label fw-bold">Data Início</label>
                                    <input type="date" class="form-control" id="data_inicio" name="data_inicio"
                                        value="{{ $data_inicio }}">
                                </div>

                                <div class="col-md-2 col-sm-12">
                                    <label for="data_fim" class="form-label fw-bold">Data Fim</label>
                                    <input type="date" class="form-control" id="data_fim" name="data_fim"
                                        value="{{ $data_fim }}">
                                </div>

                                <div class="col-md-2 col-sm-12">
                                    <label for="situation" class="form-label fw-bold">Situação</label>
                                    <select class="form-select" id="situation" name="situation">
                                        <option value="">Todos</option>
                                        <option value="paid" @selected($situation == 'paid')>Pago</option>
                                        <option value="pending" @selected($situation == 'pending')>Pendente</option>
                                        <option value="canceled" @selected($situation == 'canceled')>Cancelado</option>
                                    </select>
                                </div>

                                @if ($perPage)
                                    <input type="hidden" name="perPage" id="perPage" value="{{ $perPage }}">
                                @endif

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
                        <div class="d-flex align-items-center">
                            <span class="me-2">Lista de contas</span>
                            <form id="filterForm" action="{{ route('home') }}">
                                <select class="form-select" id="perPage" name="perPage"
                                    onchange="document.getElementById('filterForm').submit()">
                                    <option value="5" @selected($perPage == '5')>5</option>
                                    <option value="10" @selected($perPage == '10')>10</option>
                                    <option value="25" @selected($perPage == '25')>25</option>
                                    <option value="50" @selected($perPage == '50')>50</option>
                                    <option value="100" @selected($perPage == '100')>100</option>
                                    <option value="150" @selected($perPage == '150')>150</option>
                                    <option value="200" @selected($perPage == '200')>200</option>
                                </select>
                                @if ($name)
                                    <input type="hidden" name="name" value="{{ $name }}">
                                @endif
                                @if ($data_inicio)
                                    <input type="hidden" name="data_inicio" value="{{ $data_inicio }}">
                                @endif
                                @if ($data_fim)
                                    <input type="hidden" name="data_fim" value="{{ $data_fim }}">
                                @endif
                                @if ($situation)
                                    <input type="hidden" name="situation" value="{{ $situation }}">
                                @endif
                            </form>
                        </div>
                        <a href="{{ route('contas.create') }}" class="btn btn-primary">Cadastrar</a>

                    </div>

                    <div class="card-body">

                        <x-alert />
                        <div class="table-responsive">
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
                                            <td class="d-none d-md-flex justify-content-center collapse">
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
                                            <td class="d-flex d-md-none justify-content-center">
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        Ações
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('contas.show', ['id' => $conta->id]) }}">Visualizar</a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('contas.edit', ['id' => $conta->id]) }}">Editar</a>
                                                        </li>
                                                        <li>
                                                            <form id="formExcluir{{ $conta->id }}"
                                                                action="{{ route('contas.destroy', ['id' => $conta->id]) }}"
                                                                method="post">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a class="dropdown-item" type="submit"
                                                                    onclick="confirmarExclusao(event, {{ $conta->id }})">Apagar</a>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </td>
                                        </tr>
                                    @endforeach


                                    @if (count($contas) == 0)
                                        <p class="text-center text-danger fw-bold">Nenhuma conta encontrada! </p>
                                    @endif


                                </tbody>

                            </table>
                        </div>
                        {{ $contas->onEachSide(0)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
