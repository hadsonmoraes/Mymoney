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

                        <form action="{{ route('contas.store') }}" method="post" enctype="multipart/form-data"
                            class="form-grid">
                            @csrf

                            <div class="form-section">
                                <div class="form-section-title">Identificação</div>
                                <div class="form-section-desc">Comece pelo nome, valor e vencimento do lançamento.</div>

                                <div class="row g-3">
                                    <div class="col-12 col-lg-7">
                                        <label for="name" class="form-label">Nome <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ old('name') }}" placeholder="Ex: Aluguel, salário, energia">
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-2">
                                        <label for="value" class="form-label">Valor <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="value" name="value"
                                            value="{{ old('value') }}" placeholder="0,00">
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-3">
                                        <label for="maturity" class="form-label">Vencimento <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="maturity" name="maturity"
                                            value="{{ old('maturity') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">Classificação</div>
                                <div class="form-section-desc">Defina o tipo, a situação e a categoria para localizar o
                                    lançamento depois.</div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-6 col-lg-3">
                                        <label for="type" class="form-label">Tipo <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="type" name="type">
                                            <option value="" selected disabled>Selecione</option>
                                            <option value="entrada" {{ old('type') == 'entrada' ? 'selected' : '' }}>Entrada
                                            </option>
                                            <option value="saida" {{ old('type') == 'saida' ? 'selected' : '' }}>Saída
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-3">
                                        <label for="situation" class="form-label">Situação <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="situation" name="situation">
                                            <option value="" selected disabled>Selecione</option>
                                            <option value="paid" {{ old('situation') == 'paid' ? 'selected' : '' }}>Pago
                                            </option>
                                            <option value="pending" {{ old('situation') == 'pending' ? 'selected' : '' }}>
                                                Pendente</option>
                                            <option value="canceled" {{ old('situation') == 'canceled' ? 'selected' : '' }}>
                                                Cancelado</option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="category_id" class="form-label">Categoria <span
                                                class="text-danger">*</span></label>
                                        <select name="category_id" id="category_id" class="form-select select2">
                                            <option value="" selected disabled>Selecione</option>
                                            @forelse ($categorys as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                <div class="form-section-title">Recorrência</div>
                                <div class="form-section-desc">Configure regras para gerar novos lançamentos automaticamente
                                    ao longo do tempo.</div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <label for="recurrence_type" class="form-label">Tipo de recorrência</label>
                                        <select class="form-select" id="recurrence_type" name="recurrence_type">
                                            <option value="none"
                                                {{ old('recurrence_type', 'none') == 'none' ? 'selected' : '' }}>Não se
                                                repete</option>
                                            <option value="monthly"
                                                {{ old('recurrence_type') == 'monthly' ? 'selected' : '' }}>Mensal</option>
                                            <option value="weekly"
                                                {{ old('recurrence_type') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                                            <option value="biweekly"
                                                {{ old('recurrence_type') == 'biweekly' ? 'selected' : '' }}>Quinzenal (a
                                                cada 15 dias)</option>
                                            <option value="yearly"
                                                {{ old('recurrence_type') == 'yearly' ? 'selected' : '' }}>Anual</option>
                                            <option value="custom"
                                                {{ old('recurrence_type') == 'custom' ? 'selected' : '' }}>A cada X dias
                                            </option>
                                        </select>
                                        <div class="form-help">Regra automática gerada pelo sistema.</div>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-4 {{ old('recurrence_type') == 'custom' ? '' : 'd-none' }}"
                                        id="customIntervalDiv">
                                        <label for="recurrence_interval" class="form-label">Intervalo em dias <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">A cada</span>
                                            <input type="number" min="1" max="365" class="form-control"
                                                id="recurrence_interval" name="recurrence_interval"
                                                value="{{ old('recurrence_interval', 3) }}">
                                            <span class="input-group-text">dias</span>
                                        </div>
                                        <div class="form-help">Ex: 3 dias, 10 dias, 45 dias...</div>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-4 {{ old('recurrence_type', 'none') !== 'none' ? '' : 'd-none' }}"
                                        id="recurrenceEndDateDiv">
                                        <label for="recurrence_end_date" class="form-label">Até (Data limite)</label>
                                        <input type="date" class="form-control" id="recurrence_end_date"
                                            name="recurrence_end_date" value="{{ old('recurrence_end_date') }}">
                                        <div class="form-help">Opcional. Deixe em branco para tempo indeterminado.</div>
                                    </div>

                                    <div class="col-12 col-md-6 col-lg-4 {{ old('recurrence_type', 'none') !== 'none' ? '' : 'd-none' }}"
                                        id="recurrenceMaxDiv">
                                        <label for="recurrence_max_occurrences" class="form-label">Limite de
                                            ocorrências</label>
                                        <input type="number" min="1" max="365" class="form-control"
                                            id="recurrence_max_occurrences" name="recurrence_max_occurrences"
                                            value="{{ old('recurrence_max_occurrences') }}" placeholder="Ex: 12">
                                        <div class="form-help">Opcional. Exemplo: 12 ocorrências.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">Detalhes extras</div>
                                <div class="form-section-desc">Anexe comprovante e adicione observações.</div>

                                <div class="row g-3">
                                    <div class="col-12 col-lg-6">
                                        <label for="image" class="form-label">Comprovante</label>
                                        <input class="form-control" type="file" id="image" name="image">
                                        <div class="form-help">Opcional. Um arquivo por lançamento.</div>
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
