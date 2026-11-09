@extends('layouts.app')

@section('title', 'Editar')

@section('content')
    <div class="w-full">
        <div class="flex flex-wrap justify-center gap-3">
            <div class="w-full">
                <div class="page-hero mb-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="metric-label mb-2">Atualização</div>
                            <h1 class="page-title mb-1">Editar conta</h1>
                            <p class="page-subtitle">Ajuste os dados do lançamento com um fluxo mais leve e organizado.</p>
                        </div>

                        <a href="{{ route('home', session('filtros_contas')) }}" class="action-button action-button-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="section-card form-surface w-full rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="form-card-body">
                        <x-alert />

                        <form action="{{ route('contas.update', ['id' => $contas->id]) }}" method="post"
                            enctype="multipart/form-data" class="form-grid">
                            @csrf
                            @method('PUT')

                            <div class="form-section">
                                <div class="form-section-title">Identificação</div>
                                <div class="form-section-desc">Atualize as informações principais do lançamento.</div>

                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-12">
                                    <div class="lg:col-span-7">
                                        <label for="name" class="field-label">Nome</label>
                                        <input type="text" class="field-control" id="name" name="name" required
                                            value="{{ $contas->name }}">
                                    </div>

                                    <div class="md:col-span-1 lg:col-span-2">
                                        <label for="value" class="field-label">Valor</label>
                                        <input type="text" class="field-control" id="value" name="value" required
                                            value="{{ number_format($contas->value, '2', ',', '.') }}">
                                    </div>

                                    <div class="md:col-span-1 lg:col-span-3">
                                        <label for="maturity" class="field-label">Vencimento</label>
                                        <input type="date" class="field-control" id="maturity" name="maturity" required
                                            value="{{ $contas->maturity?->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">Classificação</div>
                                <div class="form-section-desc">Mantenha o lançamento organizado com tipo, situação e
                                    categoria.</div>

                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-12">
                                    <div class="md:col-span-1 lg:col-span-3">
                                        <label for="type" class="field-label">Tipo</label>
                                        <select class="field-select" id="type" name="type">
                                            <option value="" selected disabled>Selecione</option>
                                            <option value="entrada" @selected($contas->type == 'entrada')>Entrada</option>
                                            <option value="saida" @selected($contas->type == 'saida')>Saída</option>
                                        </select>
                                    </div>

                                    <div class="md:col-span-1 lg:col-span-3">
                                        <label for="situation" class="field-label">Situação</label>
                                        <select class="field-select" id="situation" name="situation" required>
                                            <option value="paid" @selected($contas->situation == 'paid')>Pago</option>
                                            <option value="pending" @selected($contas->situation == 'pending')>Pendente</option>
                                            <option value="canceled" @selected($contas->situation == 'canceled')>Cancelado</option>
                                        </select>
                                    </div>

                                    <div class="md:col-span-2 lg:col-span-6">
                                        <label for="category_id" class="field-label">Categoria</label>
                                        <select name="category_id" id="category_id" class="field-select select2">
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
                                @if ($contas->is_repeated)
                                    <div class="mb-4 rounded-lg border border-blue-light-200 bg-blue-light-50 p-4 text-blue-light-900 shadow-sm dark:border-blue-light-800 dark:bg-blue-light-950 dark:text-blue-light-100">
                                        <div class="flex items-start gap-3">
                                            <i class="fa-solid fa-layer-group text-brand-600 dark:text-brand-400 text-2xl mt-1"></i>
                                            <div class="flex-1">
                                                <div
                                                    class="mb-1 flex flex-wrap items-center justify-between gap-2">
                                                    <h6 class="font-bold mb-0">Lançamento em Sequência
                                                        ({{ $contas->repeat_label ?? 'Repetido' }})
                                                    </h6>
                                                </div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Este lançamento pertence a um grupo de
                                                    repetição. Como
                                                    deseja aplicar as alterações?</p>
                                                <div class="mt-2 flex flex-col gap-2">
                                                    <div class="flex items-start gap-2">
                                                        <input class="mt-1 size-4 shrink-0 accent-brand-500" type="radio"
                                                            name="update_scope" id="scope_only_this" value="only_this"
                                                            checked>
                                                        <label class="text-sm text-gray-700 dark:text-gray-300" for="scope_only_this">
                                                            <strong>Somente este lançamento</strong> <span
                                                                class="text-gray-500 dark:text-gray-400">(padrão: altera apenas este
                                                                registro)</span>
                                                        </label>
                                                    </div>
                                                    <div class="flex items-start gap-2">
                                                        <input class="mt-1 size-4 shrink-0 accent-brand-500" type="radio"
                                                            name="update_scope" id="scope_this_and_next"
                                                            value="this_and_next">
                                                        <label class="text-sm text-gray-700 dark:text-gray-300" for="scope_this_and_next">
                                                            <strong>Este e os próximos lançamentos</strong> <span
                                                                class="text-gray-500 dark:text-gray-400">(altera os dados deste e dos
                                                                lançamentos futuros da sequência)</span>
                                                        </label>
                                                    </div>
                                                    <div class="flex items-start gap-2">
                                                        <input class="mt-1 size-4 shrink-0 accent-brand-500" type="radio"
                                                            name="update_scope" id="scope_all" value="all_sequence">
                                                        <label class="text-sm text-gray-700 dark:text-gray-300" for="scope_all">
                                                            <strong>Toda a sequência</strong> <span
                                                                class="text-gray-500 dark:text-gray-400">(altera todos os lançamentos vinculados
                                                                a esta sequência)</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="form-section">
                                    <div class="form-section-title">Recorrência</div>
                                    <div class="form-section-desc">Regra contínua para geração automática ao longo do
                                        tempo.</div>

                                    @php
                                        $currentFreq =
                                            $contas->recurrence?->frequency ?? ($contas->fixed ? 'monthly' : 'none');
                                        $currentInterval = $contas->recurrence?->interval ?? 1;
                                        $currentEndDate = $contas->recurrence?->end_date?->toDateString();
                                        $currentMax = $contas->recurrence?->max_occurrences;
                                    @endphp

                                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-12">
                                        <div class="md:col-span-1 lg:col-span-4">
                                            <label for="recurrence_type" class="field-label">Tipo de recorrência</label>
                                            <select class="field-select" id="recurrence_type" name="recurrence_type">
                                                <option value="none" @selected($currentFreq == 'none')>Não se repete</option>
                                                <option value="monthly" @selected($currentFreq == 'monthly')>Mensal</option>
                                                <option value="weekly" @selected($currentFreq == 'weekly')>Semanal</option>
                                                <option value="biweekly" @selected($currentFreq == 'biweekly')>Quinzenal (a cada 15
                                                    dias)</option>
                                                <option value="yearly" @selected($currentFreq == 'yearly')>Anual</option>
                                                <option value="custom" @selected($currentFreq == 'custom')>A cada X dias</option>
                                            </select>
                                        </div>

                                        <div class="md:col-span-1 lg:col-span-4 {{ $currentFreq == 'custom' ? '' : 'hidden' }}"
                                            id="customIntervalDiv">
                                            <label for="recurrence_interval" class="field-label">Intervalo em dias</label>
                                            <div class="flex w-full items-stretch">
                                                <span
                                                    class="inline-flex shrink-0 items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 px-3 text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">A
                                                    cada</span>
                                                <input type="number" min="1" max="365"
                                                    class="field-control min-w-0 flex-1 rounded-none"
                                                    id="recurrence_interval" name="recurrence_interval"
                                                    value="{{ old('recurrence_interval', $currentInterval) }}">
                                                <span
                                                    class="inline-flex shrink-0 items-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">dias</span>
                                            </div>
                                        </div>

                                        <div class="md:col-span-1 lg:col-span-4 {{ $currentFreq !== 'none' ? '' : 'hidden' }}"
                                            id="recurrenceEndDateDiv">
                                            <label for="recurrence_end_date" class="field-label">Data limite (Até)</label>
                                            <input type="date" class="field-control" id="recurrence_end_date"
                                                name="recurrence_end_date"
                                                value="{{ old('recurrence_end_date', $currentEndDate) }}">
                                        </div>

                                        <div class="md:col-span-1 lg:col-span-4 {{ $currentFreq !== 'none' ? '' : 'hidden' }}"
                                            id="recurrenceMaxDiv">
                                            <label for="recurrence_max_occurrences" class="field-label">Limite de
                                                ocorrências</label>
                                            <input type="number" min="1" max="365" class="field-control"
                                                id="recurrence_max_occurrences" name="recurrence_max_occurrences"
                                                value="{{ old('recurrence_max_occurrences', $currentMax) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="form-section-title">Detalhes extras</div>
                                    <div class="form-section-desc">Atualize comprovante e notas, se necessário.</div>

                                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                        <div>
                                            <label for="image" class="field-label">Comprovante</label>
                                            <input class="field-control" type="file" id="image" name="image">
                                            <div class="form-help">Deixe em branco para manter o arquivo atual.</div>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label for="note" class="field-label">Nota</label>
                                            <textarea name="note" id="summernote" class="field-control">{{ $contas->note }}</textarea>
                                            <div class="form-help">Adicione contexto para facilitar futuras consultas.
                                            </div>
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
