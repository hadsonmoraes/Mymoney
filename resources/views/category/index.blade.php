@extends('layouts.app')

@section('title', 'Categorias')

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center g-3">
        <div class="col-md-12">

            <div class="page-hero mb-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <div class="metric-label mb-2">Organização</div>
                        <h1 class="page-title mb-1">Categorias</h1>
                        <p class="page-subtitle">Agrupe seus lançamentos de forma visual e simples de administrar.</p>
                    </div>
                    <a href="{{ route('category.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-circle-plus me-1"></i> Nova categoria
                    </a>
                </div>
            </div>

            <div class="card shadow-sm section-card">
                <div class="card-body">

                    <x-alert />
                    <div class="table-responsive">
                        <table class="table align-middle">
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
                                    <th class="align-middle" scope="row">{{ $category->id }}</th>
                                    <td class="align-middle">{{ $category->name }}</td>
                                    <td class="d-none d-md-flex justify-content-center collapse">
                                        <a href="{{ route('category.edit', ['id' => $category->id]) }}"
                                            class="btn btn-outline-secondary btn-sm me-1">Editar</a>
                                        <form id="formExcluir{{ $category->id }}"
                                            action="{{ route('category.destroy', ['id' => $category->id]) }}"
                                            method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
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
                                                        href="{{ route('category.edit', ['id' => $category->id]) }}">Editar</a>
                                                </li>
                                                <li>
                                                    <form id="formExcluir{{ $category->id }}"
                                                        action="{{ route('category.destroy', ['id' => $category->id]) }}"
                                                        method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <a class="dropdown-item" type="button"
                                                            onclick="confirmarExclusao(event, {{ $category->id }})">Apagar</a>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>

                                    </td>
                                </tr>
                                @endforeach

                                @if (count($categories) == 0)
                                <tr>

                                <td colspan="3">
                                    <div class="empty-state">
                                        <div class="icon"><i class="fa-solid fa-list"></i></div>
                                        <div class="fw-semibold text-body">Nenhuma categoria encontrada</div>
                                        <div>Crie sua primeira categoria para começar a organizar os lançamentos.</div>
                                    </div>
                                </td>

                                </tr>
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
