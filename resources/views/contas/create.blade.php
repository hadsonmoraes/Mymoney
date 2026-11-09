@extends('layouts.app')

@section('title', 'Cadastrar')

@section('content')
    <div class="w-full p-3">
        <div class="flex flex-wrap justify-center gap-3">
            <div class="w-full">
                <div class="page-hero mb-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="metric-label mb-2">Novo lançamento</div>
                            <h1 class="page-title mb-1">Criar conta</h1>
                            <p class="page-subtitle">Cadastre entradas e saídas com um fluxo mais claro e organizado.</p>
                        </div>

                        <a href="{{ route('home', session('filtros_contas')) }}" class="action-button action-button-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="section-card form-surface w-full rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="form-card-body">
                        <x-alert />

                        <form action="{{ route('contas.store') }}" method="post" enctype="multipart/form-data"
                            class="form-grid">
                            @csrf

                            <div class="form-section">
                                <div class="form-section-title">Identificação</div>
                                <div class="form-section-desc">Comece pelo nome, valor e vencimento do lançamento.</div>

                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-12">
                                    <div class="lg:col-span-7">
                                        <label for="name" class="field-label">Nome <span
                                                class="text-error-700 dark:text-error-400">*</span></label>
                                        <input type="text" class="field-control" id="name" name="name"
                                            value="{{ old('name') }}" placeholder="Ex: Aluguel, salário, energia">
                                    </div>

                                    <div class="md:col-span-1 lg:col-span-2">
                                        <label for="value" class="field-label">Valor <span
                                                class="text-error-700 dark:text-error-400">*</span></label>
                                        <input type="text" class="field-control" id="value" name="value"
                                            value="{{ old('value') }}" placeholder="0,00">
                                    </div>

                                    <div class="md:col-span-1 lg:col-span-3">
                                        <label for="maturity" class="field-label">Vencimento <span
                                                class="text-error-700 dark:text-error-400">*</span></label>
                                        <input type="date" class="field-control" id="maturity" name="maturity"
                                            value="{{ old('maturity') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">Classificação</div>
                                <div class="form-section-desc">Defina o tipo, a situação e a categoria para localizar o
                                    lançamento depois.</div>

                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-12">
                                    <div class="md:col-span-1 lg:col-span-3">
                                        <label for="type" class="field-label">Tipo <span
                                                class="text-error-700 dark:text-error-400">*</span></label>
                                        <select class="field-select" id="type" name="type">
                                            <option value="" selected disabled>Selecione</option>
                                            <option value="entrada" {{ old('type') == 'entrada' ? 'selected' : '' }}>Entrada
                                            </option>
                                            <option value="saida" {{ old('type') == 'saida' ? 'selected' : '' }}>Saída
                                            </option>
                                        </select>
                                    </div>

                                    <div class="md:col-span-1 lg:col-span-3">
                                        <label for="situation" class="field-label">Situação <span
                                                class="text-error-700 dark:text-error-400">*</span></label>
                                        <select class="field-select" id="situation" name="situation">
                                            <option value="" selected disabled>Selecione</option>
                                            <option value="paid" {{ old('situation') == 'paid' ? 'selected' : '' }}>Pago
                                            </option>
                                            <option value="pending" {{ old('situation') == 'pending' ? 'selected' : '' }}>
                                                Pendente</option>
                                            <option value="canceled" {{ old('situation') == 'canceled' ? 'selected' : '' }}>
                                                Cancelado</option>
                                        </select>
                                    </div>

                                    <div class="md:col-span-2 lg:col-span-6">
                                        <label for="category_id" class="field-label">Categoria <span
                                                class="text-error-700 dark:text-error-400">*</span></label>
                                        <select name="category_id" id="category_id" class="field-select select2">
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

                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-12">
                                    <div class="md:col-span-1 lg:col-span-4">
                                        <label for="recurrence_type" class="field-label">Tipo de recorrência</label>
                                        <select class="field-select" id="recurrence_type" name="recurrence_type">
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

                                    <div class="md:col-span-1 lg:col-span-4 {{ old('recurrence_type') == 'custom' ? '' : 'hidden' }}"
                                        id="customIntervalDiv">
                                        <label for="recurrence_interval" class="field-label">Intervalo em dias <span
                                                class="text-error-700 dark:text-error-400">*</span></label>
                                        <div class="flex w-full items-stretch">
                                            <span
                                                class="inline-flex shrink-0 items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 px-3 text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">A
                                                cada</span>
                                            <input type="number" min="1" max="365" class="field-control min-w-0 flex-1 rounded-none"
                                                id="recurrence_interval" name="recurrence_interval"
                                                value="{{ old('recurrence_interval', 3) }}">
                                            <span
                                                class="inline-flex shrink-0 items-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">dias</span>
                                        </div>
                                        <div class="form-help">Ex: 3 dias, 10 dias, 45 dias...</div>
                                    </div>

                                    <div class="md:col-span-1 lg:col-span-4 {{ old('recurrence_type', 'none') !== 'none' ? '' : 'hidden' }}"
                                        id="recurrenceEndDateDiv">
                                        <label for="recurrence_end_date" class="field-label">Até (Data limite)</label>
                                        <input type="date" class="field-control" id="recurrence_end_date"
                                            name="recurrence_end_date" value="{{ old('recurrence_end_date') }}">
                                        <div class="form-help">Opcional. Deixe em branco para tempo indeterminado.</div>
                                    </div>

                                    <div class="md:col-span-1 lg:col-span-4 {{ old('recurrence_type', 'none') !== 'none' ? '' : 'hidden' }}"
                                        id="recurrenceMaxDiv">
                                        <label for="recurrence_max_occurrences" class="field-label">Limite de
                                            ocorrências</label>
                                        <input type="number" min="1" max="365" class="field-control"
                                            id="recurrence_max_occurrences" name="recurrence_max_occurrences"
                                            value="{{ old('recurrence_max_occurrences') }}" placeholder="Ex: 12">
                                        <div class="form-help">Opcional. Exemplo: 12 ocorrências.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">Detalhes extras</div>
                                <div class="form-section-desc">Anexe comprovante e adicione observações.</div>

                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <div>
                                        <label for="image" class="field-label">Comprovante</label>
                                        <input class="field-control" type="file" id="image" name="image">
                                        <div class="form-help">Opcional. Um arquivo por lançamento.</div>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="note" class="field-label">Nota</label>
                                        <textarea name="note" id="summernote" class="field-control">{{ old('note') }}</textarea>
                                        <div class="form-help">Use para adicionar observações, detalhes ou lembretes.</div>
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
