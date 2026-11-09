@extends('layouts.app')

@section('title', 'Cadastrar categoria')

@section('content')
    <div class="w-full p-3">
        <div class="flex flex-wrap justify-center gap-3">
            <div class="w-full">
                <div class="page-hero mb-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="metric-label mb-2">Organização</div>
                            <h1 class="page-title mb-1">Criar categoria</h1>
                            <p class="page-subtitle">Crie categorias para organizar melhor seus lançamentos.</p>
                        </div>
                        <a href="{{ route('category.index') }}" class="action-button action-button-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="section-card form-surface w-full rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="form-card-body">
                        <x-alert />

                        <form action="{{ route('category.store') }}" method="post" class="form-grid">
                            @csrf

                            <div class="form-section">
                                <div class="form-section-title">Dados básicos</div>
                                <div class="form-section-desc">Defina um nome objetivo e fácil de reconhecer.</div>

                                <div class="grid grid-cols-1 justify-center gap-3 md:grid-cols-12">
                                    <div class="md:col-span-10 md:col-start-2 lg:col-span-8 lg:col-start-3">
                                        <label for="name" class="field-label">Nome <span
                                                class="text-error-700 dark:text-error-400">*</span></label>
                                        <input type="text" class="field-control" id="name" name="name"
                                            value="{{ old('name') }}" placeholder="Ex: Casa, transporte, alimentação">
                                        <div class="form-help">Use um nome curto para localizar mais rápido.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="action-button action-button-primary px-4">Cadastrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
