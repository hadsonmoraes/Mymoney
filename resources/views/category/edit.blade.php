@extends('layouts.app')

@section('title', 'Editar categoria')

@section('content')
    <div class="w-full">
        <div class="flex flex-wrap justify-center gap-3">
            <div class="w-full">
                <div class="page-hero mb-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="metric-label mb-2">Organização</div>
                            <h1 class="page-title mb-1">Editar categoria</h1>
                            <p class="page-subtitle">Ajuste o nome da categoria sem perder contexto visual.</p>
                        </div>
                        <a href="{{ route('category.index') }}" class="action-button action-button-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="section-card form-surface w-full rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="form-card-body">
                        <x-alert />

                        <form action="{{ route('category.update', ['id' => $categorys->id]) }}" method="post"
                            class="form-grid">
                            @csrf
                            @method('PUT')

                            <div class="form-section">
                                <div class="form-section-title">Dados básicos</div>
                                <div class="form-section-desc">Renomeie a categoria para manter sua organização clara.</div>

                                <div class="grid grid-cols-1 justify-center gap-3 md:grid-cols-12">
                                    <div class="md:col-span-10 md:col-start-2 lg:col-span-8 lg:col-start-3">
                                        <label for="name" class="field-label">Nome</label>
                                        <input type="text" class="field-control" id="name" name="name"
                                            value="{{ $categorys->name }}" placeholder="Ex: Alimentação">
                                        <div class="form-help">Um nome simples ajuda a localizar com mais facilidade.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="action-button action-button-primary px-4">Editar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
