@extends('layouts.app')

@section('title', 'Categorias')

@section('content')
    <div class="w-full">
        <div class="flex flex-wrap justify-center gap-3">
            <div class="w-full">

                <div class="page-hero mb-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="metric-label mb-2">Organização</div>
                            <h1 class="page-title mb-1">Categorias</h1>
                            <p class="page-subtitle">Agrupe seus lançamentos de forma visual e simples de administrar.</p>
                        </div>
                        <a href="{{ route('category.create') }}" class="action-button action-button-primary">
                            <x-svg.plus class="size-4 me-1" /> Nova categoria
                        </a>
                    </div>
                </div>

                <div class="section-card min-w-0 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="p-5">

                        <x-alert />
                        <div class="w-full overflow-x-auto">
                            <table class="data-table align-middle">
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
                                            <td class="text-center">
                                                <div class="relative inline-flex items-center gap-2" role="group" data-ui-dropdown aria-label="Ações da categoria">
                                                    <a href="{{ route('category.edit', ['id' => $category->id]) }}"
                                                        class="action-button action-button-outline-secondary action-button-sm">Editar</a>
                                                    <form id="formExcluir{{ $category->id }}"
                                                        action="{{ route('category.destroy', ['id' => $category->id]) }}"
                                                        method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="action-button action-button-outline-danger action-button-sm"
                                                            onclick="confirmarExclusao(event, {{ $category->id }})">Apagar</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if (count($categories) == 0)
                                        <tr>

                                            <td colspan="3">
                                                <div class="empty-state">
                                                    <div class="icon"><x-svg.list class="size-6" /></div>
                                                    <div class="font-semibold text-gray-800 dark:text-gray-200">Nenhuma categoria encontrada</div>
                                                    <div>Crie sua primeira categoria para começar a organizar os
                                                        lançamentos.</div>
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
