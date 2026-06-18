@extends('layouts.app')

@section('title', 'Cadastrar categoria')

@section('content')
    <div class="container-fluid p-4">
        <div class="row justify-content-center g-3">
            <div class="col-12">
                <div class="page-hero mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <div class="metric-label mb-2">Organização</div>
                            <h1 class="page-title mb-1">Criar categoria</h1>
                            <p class="page-subtitle">Crie categorias para organizar melhor seus lançamentos.</p>
                        </div>
                        <a href="{{ route('category.index') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card section-card shadow-sm form-surface w-100">
                    <div class="card-body form-card-body">
                        <x-alert />

                        <form action="{{ route('category.store') }}" method="post" class="form-grid">
                            @csrf

                            <div class="form-section">
                                <div class="form-section-title">Dados básicos</div>
                                <div class="form-section-desc">Defina um nome objetivo e fácil de reconhecer.</div>

                                <div class="row g-3 justify-content-center">
                                    <div class="col-12 col-md-10 col-lg-8">
                                        <label for="name" class="form-label">Nome <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                                            placeholder="Ex: Casa, transporte, alimentação">
                                        <div class="form-help">Use um nome curto para localizar mais rápido.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary px-4">Cadastrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
