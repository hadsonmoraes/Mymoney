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
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $category)
                                        <tr>
                                            <th scope="row">{{ $category->id }}</th>
                                            <td>{{ $category->name }}</td>
                                            <td class="d-none d-md-flex justify-content-center collapse">
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
