@extends('layouts.app')

@section('title', 'Editar')

@section('content')
    <div class="container-fluid p-4">
        <div class="row justify-content-center g-3">
            <div class="col-12">
                <div class="page-hero mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <div class="metric-label mb-2">Atualização</div>
                            <h1 class="page-title mb-1">Editar conta</h1>
                            <p class="page-subtitle">Ajuste os dados do lançamento com um fluxo mais leve e organizado.</p>
                        </div>

                        <a href="{{ route('home', session('filtros_contas')) }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card shadow-sm section-card form-surface w-100">
                    <div class="card-body form-card-body">
                        <x-alert />

                        <form action="{{ route('contas.update', ['id' => $contas->id]) }}" method="post"
                            enctype="multipart/form-data" class="form-grid">
                            @csrf
                            @method('PUT')

                            <div class="form-section">
                                <div class="form-section-title">Identificação</div>
                                <div class="form-section-desc">Atualize as informações principais do lançamento.</div>

                                <div class="row g-3">
                                    <div class="col-12 col-lg-7">
                                        <label for="name" class="form-label">Nome</label>
                                        <input type="text" class="form-control" id="name" name="name" required
                                            value="{{ $contas->name }}">
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-2">
                                        <label for="value" class="form-label">Valor</label>
                                        <input type="text" class="form-control" id="value" name="value" required
                                            value="{{ number_format($contas->value, '2', ',', '.') }}">
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-3">
                                        <label for="maturity" class="form-label">Vencimento</label>
                                        <input type="date" class="form-control" id="maturity" name="maturity" required
                                            value="{{ $contas->maturity }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">Classificação</div>
                                <div class="form-section-desc">Mantenha o lançamento organizado com tipo, situação e categoria.</div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-6 col-lg-3">
                                        <label for="type" class="form-label">Tipo</label>
                                        <select class="form-select" id="type" name="type">
                                            <option value="" selected disabled>Selecione</option>
                                            <option value="entrada" @selected($contas->type == 'entrada')>Entrada</option>
                                            <option value="saida" @selected($contas->type == 'saida')>Saída</option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-3">
                                        <label for="situation" class="form-label">Situação</label>
                                        <select class="form-select" id="situation" name="situation" required>
                                            <option value="paid" @selected($contas->situation == 'paid')>Pago</option>
                                            <option value="pending" @selected($contas->situation == 'pending')>Pendente</option>
                                            <option value="canceled" @selected($contas->situation == 'canceled')>Cancelado</option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="category_id" class="form-label">Categoria</label>
                                        <select name="category_id" id="category_id" class="form-select select2">
                                            <option value="" selected disabled>Selecione</option>
                                            @forelse ($categorys as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id', $contas->category_id) == $category->id ? 'selected' : '' }}>
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
                                <div class="form-section-desc">Atualize comprovante, recorrência e notas, se necessário.</div>

                                <div class="row g-3">
                                    <div class="col-12 col-lg-4">
                                        <label for="image" class="form-label">Comprovante</label>
                                        <input class="form-control" type="file" id="image" name="image">
                                        <div class="form-help">Deixe em branco para manter o arquivo atual.</div>
                                    </div>

                                    <div class="col-12 col-lg-4">
                                        <label for="repeat" class="form-label">Repetir lançamento</label>
                                        <input type="number" min="0" class="form-control" id="repeat" name="repeat"
                                            value="{{ $contas->repeat }}">
                                        <div class="form-help">Quantidade de repetições, se aplicável.</div>
                                    </div>

                                    <div class="col-12 col-lg-4 d-flex align-items-end">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" name="fixed"
                                                id="fixed" value="{{ true }}"
                                                {{ old('fixed', $contas->fixed ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="fixed">Despesa/receita fixa</label>
                                            <div class="form-help">Use para contas recorrentes.</div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="note" class="form-label">Nota</label>
                                        <textarea name="note" id="summernote" class="form-control">{{ $contas->note }}</textarea>
                                        <div class="form-help">Adicione contexto para facilitar futuras consultas.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary px-4">Editar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
