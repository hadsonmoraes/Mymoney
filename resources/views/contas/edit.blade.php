@extends('layouts.app')

@section('title', 'Editar')

@section('content')
    <div class="container-fluid p-3">
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
                                            value="{{ $contas->maturity?->format('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">Classificação</div>
                                <div class="form-section-desc">Mantenha o lançamento organizado com tipo, situação e
                                    categoria.</div>

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
                                @if ($contas->is_repeated)
                                    <div class="alert alert-info border-info-subtle shadow-sm mb-4">
                                        <div class="d-flex gap-3 align-items-start">
                                            <i class="fa-solid fa-layer-group text-primary fs-3 mt-1"></i>
                                            <div class="flex-grow-1">
                                                <div
                                                    class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                                    <h6 class="fw-bold mb-0">Lançamento em Sequência
                                                        ({{ $contas->repeat_label ?? 'Repetido' }})
                                                    </h6>
                                                </div>
                                                <p class="small text-muted mb-2">Este lançamento pertence a um grupo de
                                                    repetição. Como
                                                    deseja aplicar as alterações?</p>
                                                <div class="d-flex flex-column gap-2 mt-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="update_scope" id="scope_only_this" value="only_this"
                                                            checked>
                                                        <label class="form-check-label" for="scope_only_this">
                                                            <strong>Somente este lançamento</strong> <span
                                                                class="text-muted">(padrão: altera apenas este
                                                                registro)</span>
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="update_scope" id="scope_this_and_next"
                                                            value="this_and_next">
                                                        <label class="form-check-label" for="scope_this_and_next">
                                                            <strong>Este e os próximos lançamentos</strong> <span
                                                                class="text-muted">(altera os dados deste e dos
                                                                lançamentos futuros da sequência)</span>
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="update_scope" id="scope_all" value="all_sequence">
                                                        <label class="form-check-label" for="scope_all">
                                                            <strong>Toda a sequência</strong> <span
                                                                class="text-muted">(altera todos os lançamentos vinculados
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

                                    <div class="row g-3">
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <label for="recurrence_type" class="form-label">Tipo de recorrência</label>
                                            <select class="form-select" id="recurrence_type" name="recurrence_type">
                                                <option value="none" @selected($currentFreq == 'none')>Não se repete</option>
                                                <option value="monthly" @selected($currentFreq == 'monthly')>Mensal</option>
                                                <option value="weekly" @selected($currentFreq == 'weekly')>Semanal</option>
                                                <option value="biweekly" @selected($currentFreq == 'biweekly')>Quinzenal (a cada 15
                                                    dias)</option>
                                                <option value="yearly" @selected($currentFreq == 'yearly')>Anual</option>
                                                <option value="custom" @selected($currentFreq == 'custom')>A cada X dias</option>
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-4 {{ $currentFreq == 'custom' ? '' : 'd-none' }}"
                                            id="customIntervalDiv">
                                            <label for="recurrence_interval" class="form-label">Intervalo em dias</label>
                                            <div class="input-group">
                                                <span class="input-group-text">A cada</span>
                                                <input type="number" min="1" max="365" class="form-control"
                                                    id="recurrence_interval" name="recurrence_interval"
                                                    value="{{ old('recurrence_interval', $currentInterval) }}">
                                                <span class="input-group-text">dias</span>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-4 {{ $currentFreq !== 'none' ? '' : 'd-none' }}"
                                            id="recurrenceEndDateDiv">
                                            <label for="recurrence_end_date" class="form-label">Data limite (Até)</label>
                                            <input type="date" class="form-control" id="recurrence_end_date"
                                                name="recurrence_end_date"
                                                value="{{ old('recurrence_end_date', $currentEndDate) }}">
                                        </div>

                                        <div class="col-12 col-md-6 col-lg-4 {{ $currentFreq !== 'none' ? '' : 'd-none' }}"
                                            id="recurrenceMaxDiv">
                                            <label for="recurrence_max_occurrences" class="form-label">Limite de
                                                ocorrências</label>
                                            <input type="number" min="1" max="365" class="form-control"
                                                id="recurrence_max_occurrences" name="recurrence_max_occurrences"
                                                value="{{ old('recurrence_max_occurrences', $currentMax) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="form-section-title">Detalhes extras</div>
                                    <div class="form-section-desc">Atualize comprovante e notas, se necessário.</div>

                                    <div class="row g-3">
                                        <div class="col-12 col-lg-6">
                                            <label for="image" class="form-label">Comprovante</label>
                                            <input class="form-control" type="file" id="image" name="image">
                                            <div class="form-help">Deixe em branco para manter o arquivo atual.</div>
                                        </div>

                                        <div class="col-12">
                                            <label for="note" class="form-label">Nota</label>
                                            <textarea name="note" id="summernote" class="form-control">{{ $contas->note }}</textarea>
                                            <div class="form-help">Adicione contexto para facilitar futuras consultas.
                                            </div>
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
