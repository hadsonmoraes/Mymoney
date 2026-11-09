@extends('layouts.app')

@section('title', 'Visualizar')

@section('content')

    <div class="w-full">
        <div class="flex flex-wrap justify-center gap-3">
            <div class="w-full">
                <div class="page-hero mb-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="metric-label mb-2">Detalhes</div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h1 class="page-title mb-0">Visualizar conta</h1>
                                @if ($contas->is_repeated)
                                    <span class="status-badge bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border">
                                        <i class="fa-solid fa-clone me-1"></i>{{ $contas->repeat_label ?: 'Repetido' }}
                                    </span>
                                @endif
                                @if ($contas->is_recurring)
                                    <span class="status-badge bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 border">
                                        <i class="fa-solid fa-arrows-rotate me-1"></i>Recorrente
                                        ({{ $contas->recurrence?->frequency_label ?? 'Mensal' }})
                                    </span>
                                @endif
                            </div>
                            <p class="page-subtitle mt-1">Confira todas as informações registradas neste lançamento.</p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button type="button" class="action-button action-button-outline-info" data-ui-modal-toggle
                                data-ui-modal-target="#modalRepetirLancamento">
                                <i class="fa-solid fa-clone me-1"></i> Repetir lançamento
                            </button>
                            <a href="{{ route('contas.edit', ['id' => $contas->id]) }}" class="action-button action-button-outline-primary">
                                <i class="fa-solid fa-pen me-1"></i> Editar
                            </a>
                            <a href="{{ route('home', session('filtros_contas')) }}" class="action-button action-button-outline-secondary">
                                <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                            </a>
                        </div>
                    </div>
                </div>

                <div class="section-card form-surface w-full rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="form-card-body">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="mb-3 lg:col-span-2">
                                <label for="name" class="field-label">Nome</label>
                                <input type="text" class="field-control" id="name" value="{{ $contas->name }}"
                                    disabled>
                            </div>

                            <div class="mb-3">
                                <label for="value" class="field-label">Valor</label>
                                <input type="text" class="field-control" id="value"
                                    value="{{ 'R$ ' . number_format($contas->value, 2, ',', '.') }}" disabled>
                            </div>

                            <div class="mb-3">
                                <label for="maturity" class="field-label">Vencimento</label>
                                <input type="text" class="field-control" id="maturity"
                                    value="{{ \Illuminate\Support\Carbon::parse($contas->maturity)->format('d/m/Y') }}"
                                    disabled>
                            </div>

                            <div class="mb-3">
                                <label for="type" class="field-label">Tipo</label>
                                <input type="text" class="field-control" id="type"
                                    value="{{ ucfirst($contas->type) }}" disabled>
                            </div>

                            <div class="mb-3">
                                <label for="situation" class="field-label">Situação</label>
                                <input type="text" class="field-control" id="situation"
                                    value="{{ $contas->situation_name }}" disabled>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="field-label">Categoria</label>
                                <input type="text" class="field-control" id="category"
                                    value="{{ $contas->category->name }}" disabled>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="field-label">Comprovante:</label>
                                @if (!empty($contas->image))
                                    <a class="field-control no-underline"
                                        href="{{ url('img/comprovantes/' . Auth::id() . '/' . $contas->image) }}"
                                        target="_blank">Visualizar <i class="fas fa-download"></i>
                                    </a>
                                @else
                                    <a class="field-control no-underline pointer-events-none opacity-50">Sem comprovante</a>
                                @endif
                            </div>

                            @if ($contas->is_repeated)
                                <div class="mb-3 lg:col-span-2">
                                    <label class="field-label">Repetição</label>
                                    <input type="text" class="field-control"
                                        value="{{ $contas->repeat_label ?: 'Lançamento repetido' }}" disabled>
                                </div>
                            @endif

                            @if ($contas->is_recurring)
                                <div class="mb-3 lg:col-span-2">
                                    <label class="field-label">Regra de Recorrência</label>
                                    <input type="text" class="field-control"
                                        value="{{ $contas->recurrence?->frequency_label ?? 'Mensal' }} (Status: {{ $contas->recurrence?->status_label ?? 'Ativa' }})"
                                        disabled>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="cadastro" class="field-label">Cadastrado</label>
                                <input type="text" class="field-control" id="cadastro"
                                    value="{{ \Illuminate\Support\Carbon::parse($contas->created_at)->format('d/m/Y') }}"
                                    disabled>
                            </div>

                            <div class="mb-3">
                                <label for="editado" class="field-label">Editado</label>
                                <input type="text" class="field-control" id="editado"
                                    value="{{ \Illuminate\Support\Carbon::parse($contas->updated_at)->format('d/m/Y') }}"
                                    disabled>
                            </div>

                            <div class="mb-3 lg:col-span-4">
                                <label for="note" class="field-label">Nota:</label>
                                <div class="field-control bg-white dark:bg-gray-800" style="min-height: 120px;">
                                    {!! $contas->note !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Repetir Lançamento -->
    <div class="ui-modal hidden" data-ui-modal id="modalRepetirLancamento" tabindex="-1" aria-labelledby="modalRepetirLancamentoLabel"
        aria-hidden="true">
        <div class="ui-modal-dialog">
            <div class="ui-modal-content shadow">
                <form method="POST" action="{{ route('contas.repeat', ['id' => $contas->id]) }}">
                    @csrf
                    <div class="ui-modal-header">
                        <h5 class="ui-modal-title" id="modalRepetirLancamentoLabel">
                            <i class="fa-solid fa-clone text-blue-light-700 dark:text-blue-light-300 me-2"></i>Repetir lançamento
                        </h5>
                        <button type="button" class="ui-modal-close" data-ui-modal-dismiss aria-label="Fechar"></button>
                    </div>
                    <div class="ui-modal-body">
                        <div class="mb-3 rounded-lg border border-gray-200 bg-gray-50 p-3 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Lançamento base:</div>
                            <div class="font-bold">{{ $contas->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Valor: <strong>R$ {{ number_format($contas->value, 2, ',', '.') }}</strong> |
                                Vencimento:
                                <strong>{{ \Carbon\Carbon::parse($contas->maturity)->format('d/m/Y') }}</strong>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div>
                                <label for="repeat_count" class="field-label">Quantidade de repetições <span
                                        class="text-error-700 dark:text-error-400">*</span></label>
                                <input type="number" min="1" max="120" class="field-control"
                                    id="repeat_count" name="count" value="5" required>
                                <div class="field-help">Ex: 5 gerará 5 novas ocorrências.</div>
                            </div>

                            <div>
                                <label for="repeat_frequency_show" class="field-label">Frequência <span
                                        class="text-error-700 dark:text-error-400">*</span></label>
                                <select class="field-select" id="repeat_frequency_show" name="frequency" required>
                                    <option value="monthly" selected>Mensal</option>
                                    <option value="weekly">Semanal</option>
                                    <option value="biweekly">Quinzenal (a cada 15 dias)</option>
                                    <option value="yearly">Anual</option>
                                    <option value="custom">A cada X dias</option>
                                </select>
                            </div>

                            <div class="hidden md:col-span-2" id="repeat_custom_interval_show">
                                <label for="repeat_interval" class="field-label">Intervalo personalizado em dias <span
                                        class="text-error-700 dark:text-error-400">*</span></label>
                                <div class="flex w-full items-stretch">
                                    <span
                                        class="inline-flex shrink-0 items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 px-3 text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">A
                                        cada</span>
                                    <input type="number" min="1" max="365"
                                        class="field-control min-w-0 flex-1 rounded-none"
                                        name="interval" value="3">
                                    <span
                                        class="inline-flex shrink-0 items-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">dias</span>
                                </div>
                            </div>

                            <div>
                                <label for="repeat_start_date" class="field-label">Data da 1ª repetição</label>
                                <input type="date" class="field-control" id="repeat_start_date" name="start_date">
                                <div class="field-help">Deixe em branco para calcular a partir do vencimento.</div>
                            </div>

                            <div>
                                <label for="repeat_custom_value" class="field-label">Valor das cópias (opcional)</label>
                                <input type="text" class="field-control" id="repeat_custom_value" name="custom_value"
                                    placeholder="Manter valor original">
                            </div>
                        </div>
                    </div>
                    <div class="ui-modal-footer">
                        <button type="button" class="action-button action-button-outline-secondary"
                            data-ui-modal-dismiss>Cancelar</button>
                        <button type="submit" class="action-button action-button-primary">
                            <i class="fa-solid fa-check me-1"></i>Repetir lançamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
