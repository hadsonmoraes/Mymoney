@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">


                <div class="card mt-3 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="me-2">Lista de categorias</span>
                            {{-- <form id="filterForm" action="{{ route('home') }}">
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
                            </form> --}}
                        </div>
                        <a href="{{ route('category.create') }}" class="btn btn-primary">Cadastrar</a>

                    </div>

                    <div class="card-body">

                        <x-alert />
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Id</th>
                                        <th scope="col">Nome</th>
                                        {{-- <th scope="col">Valor</th>
                                        <th scope="col">Vencimento</th>
                                        <th scope="col">Situação</th>
                                        <th scope="col">Categorias</th> --}}
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                        <tr>
                                            <th scope="row">{{ $category->id }}</th>
                                            <td>{{ $category->name }}</td>
                                            {{-- <td>{{ 'R$' . number_format($category->value, 2, ',', '.') }}</td>
                                            <td>{{ date('d/m/Y', strtotime($category->maturity)) }}</td>
                                            <td>{!! '<span class="badge text-bg-' . $status . ' "   >' . $situation_name . ' </span>' !!} </td>
                                            <td>{{ $category->category }}</td> --}}
                                            <td class="d-none d-md-flex justify-content-center collapse">
                                                {{-- <a href="{{ route('contas.show', ['id' => $category->id]) }}"
                                                    class="btn btn-primary me-1">Visualizar</a> --}}
                                                <a href="{{ route('category.edit', ['id' => $category->id]) }}"
                                                    class="btn btn-warning me-1">Editar</a>
                                                <form id="formExcluir{{ $category->id }}"
                                                    action="{{ route('category.destroy', ['id' => $category->id]) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="confirmarExclusao(event, {{ $category->id }})">Apagar</button>
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
                                                                href="{{ route('contas.show', ['id' => $category->id]) }}">Visualizar</a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('contas.edit', ['id' => $category->id]) }}">Editar</a>
                                                        </li>
                                                        <li>
                                                            <form id="formExcluir{{ $category->id }}"
                                                                action="{{ route('contas.destroy', ['id' => $category->id]) }}"
                                                                method="post">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a class="dropdown-item" type="submit"
                                                                    onclick="confirmarExclusao(event, {{ $category->id }})">Apagar</a>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </td>
                                        </tr>
                                    @endforeach


                                    @if (count($categories) == 0)
                                        <p class="text-center text-danger fw-bold">Nenhuma categoria encontrada! </p>
                                    @endif


                                </tbody>

                            </table>
                        </div>
                        {{ $categories->onEachSide(0)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
