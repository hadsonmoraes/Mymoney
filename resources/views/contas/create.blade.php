@extends('layouts.app')

@section('title', 'Cadastrar')

@section('content')
    <div class="container-fluid p-4">
        <div class="row justify-content-center g-3">
            <div class="col-12">
                <div class="page-hero mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <div class="metric-label mb-2">Novo lançamento</div>
                            <h1 class="page-title mb-1">Criar conta</h1>
                            <p class="page-subtitle">Cadastre entradas e saídas com um fluxo mais claro e organizado.</p>
                        </div>

                        <a href="{{ route('home', session('filtros_contas')) }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card shadow-sm section-card form-surface w-100">
                    <div class="card-body form-card-body">
                        <x-alert />

                        <form action="{{ route('contas.store') }}" method="post" enctype="multipart/form-data" class="form-grid">
                            @csrf

                            <div class="form-section">
                                <div class="form-section-title">Identificação</div>
                                <div class="form-section-desc">Comece pelo nome, valor e vencimento do lançamento.</div>

                                <div class="row g-3">
                                    <div class="col-12 col-lg-7">
                                        <label for="name" class="form-label">Nome <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ old('name') }}" placeholder="Ex: Aluguel, salário, energia">
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-2">
                                        <label for="value" class="form-label">Valor <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="value" name="value"
                                            value="{{ old('value') }}" placeholder="0,00">
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-3">
                                        <label for="maturity" class="form-label">Vencimento <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="maturity" name="maturity"
                                            value="{{ old('maturity') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">Classificação</div>
                                <div class="form-section-desc">Defina o tipo, a situação e a categoria para localizar o lançamento depois.</div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-6 col-lg-3">
                                        <label for="type" class="form-label">Tipo <span class="text-danger">*</span></label>
                                        <select class="form-select" id="type" name="type">
                                            <option value="" selected disabled>Selecione</option>
                                            <option value="entrada" {{ old('type') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                                            <option value="saida" {{ old('type') == 'saida' ? 'selected' : '' }}>Saída</option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-3">
                                        <label for="situation" class="form-label">Situação <span class="text-danger">*</span></label>
                                        <select class="form-select" id="situation" name="situation">
                                            <option value="" selected disabled>Selecione</option>
                                            <option value="paid" {{ old('situation') == 'paid' ? 'selected' : '' }}>Pago</option>
                                            <option value="pending" {{ old('situation') == 'pending' ? 'selected' : '' }}>Pendente</option>
                                            <option value="canceled" {{ old('situation') == 'canceled' ? 'selected' : '' }}>Cancelado</option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="category_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                                        <select name="category_id" id="category_id" class="form-select select2">
                                            <option value="" selected disabled>Selecione</option>
                                            @forelse ($categorys as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @empty
                                                <option value="">Nenhuma categoria encontrada</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">Detalhes extras</div>
                                <div class="form-section-desc">Anexe comprovante, marque recorrência e adicione observações.</div>

                                <div class="row g-3">
                                    <div class="col-12 col-lg-4">
                                        <label for="image" class="form-label">Comprovante</label>
                                        <input class="form-control" type="file" id="image" name="image">
                                        <div class="form-help">Opcional. Um arquivo por lançamento.</div>
                                    </div>

                                    <div class="col-12 col-lg-4">
                                        <label for="repeat" class="form-label">Repetir lançamento</label>
                                        <input type="number" min="0" class="form-control" id="repeat" name="repeat"
                                            value="{{ old('repeat') }}" placeholder="0">
                                        <div class="form-help">Informe quantas vezes ele deve se repetir.</div>
                                    </div>

                                    <div class="col-12 col-lg-4 d-flex align-items-end">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" name="fixed"
                                                id="fixed" value="{{ true }}" {{ old('fixed') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="fixed">Despesa/receita fixa</label>
                                            <div class="form-help">Ideal para contas recorrentes.</div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="note" class="form-label">Nota</label>
                                        <textarea name="note" id="summernote" class="form-control">{{ old('note') }}</textarea>
                                        <div class="form-help">Use para adicionar observações, detalhes ou lembretes.</div>
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
