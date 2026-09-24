@extends('layouts.app')

@section('title', 'Visualizar')

@section('content')

    <div class="container-fluid p-4">
        <div class="row justify-content-center g-3">
            <div class="col-md-12">
                <div class="page-hero mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <div class="metric-label mb-2">Detalhes</div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h1 class="page-title mb-0">Visualizar conta</h1>
                                @if ($contas->is_installment)
                                    <span class="badge bg-info text-white border">
                                        <i class="fa-solid fa-layer-group me-1"></i>Parcelamento:
                                        {{ $contas->installment_label }}
                                    </span>
                                @endif
                                @if ($contas->is_repeated)
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        <i class="fa-solid fa-clone me-1"></i>{{ $contas->repeat_label ?: 'Repetido' }}
                                    </span>
                                @endif
                                @if ($contas->is_recurring)
                                    <span class="badge bg-primary-subtle text-primary border">
                                        <i class="fa-solid fa-arrows-rotate me-1"></i>Recorrente
                                        ({{ $contas->recurrence?->frequency_label ?? 'Mensal' }})
                                    </span>
                                @endif
                            </div>
                            <p class="page-subtitle mt-1">Confira todas as informações registradas neste lançamento.</p>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                                data-bs-target="#modalConfigurarParcelamento">
                                <i class="fa-solid fa-layer-group me-1"></i>
                                {{ $contas->is_installment ? 'Editar Parcelamento' : 'Configurar Parcelamento' }}
                            </button>
                            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal"
                                data-bs-target="#modalRepetirLancamento">
                                <i class="fa-solid fa-clone me-1"></i> Repetir lançamento
                            </button>
                            <a href="{{ route('contas.edit', ['id' => $contas->id]) }}" class="btn btn-outline-primary">
                                <i class="fa-solid fa-pen me-1"></i> Editar
                            </a>
                            <a href="{{ route('home', session('filtros_contas')) }}" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                            </a>
                        </div>
                    </div>
                </div>

                @if (!$contas->is_installment && $installmentPattern)
                    <div class="alert alert-warning border shadow-sm mb-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="alert-heading mb-1 fw-bold"><i
                                        class="fa-solid fa-wand-magic-sparkles me-2"></i>Possível parcelamento encontrado
                                </h6>
                                <p class="mb-0 small text-body-secondary">
                                    Este lançamento parece pertencer a um parcelamento
                                    (<strong>{{ $installmentPattern['current'] }}/{{ $installmentPattern['total'] }}</strong>).
                                    @if ($potentialInstallments->count() > 0)
                                        Foram encontrados mais <strong>{{ $potentialInstallments->count() }}</strong>
                                        lançamentos semelhantes no histórico.
                                    @endif
                                    Deseja transformar em parcelamento estruturado?
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalConfigurarParcelamento">
                                    <i class="fa-solid fa-gear me-1"></i> Configurar
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($contas->is_installment && $installmentSummary)
                    <div class="card shadow-sm border-0 mb-3 bg-light-subtle">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0 text-primary"><i class="fa-solid fa-layer-group me-2"></i>Resumo do
                                    Parcelamento ({{ $contas->installment_label }})</h6>
                                <span class="badge bg-primary">{{ $installmentSummary['progress_percent'] }}%
                                    Concluído</span>
                            </div>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $installmentSummary['progress_percent'] }}%;"
                                    aria-valuenow="{{ $installmentSummary['progress_percent'] }}" aria-valuemin="0"
                                    aria-valuemax="100"></div>
                            </div>
                            <div class="row g-2 text-center small">
                                <div class="col-6 col-md-3">
                                    <div class="p-2 border rounded bg-white">
                                        <div class="text-muted">Total de Parcelas</div>
                                        <div class="fw-bold">{{ $installmentSummary['total_installments'] }}
                                            ({{ $installmentSummary['paid_count'] }} pagas)</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-2 border rounded bg-white">
                                        <div class="text-muted">Valor Total</div>
                                        <div class="fw-bold">R$
                                            {{ number_format($installmentSummary['total_value'], 2, ',', '.') }}</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-2 border rounded bg-white text-success">
                                        <div>Total Pago</div>
                                        <div class="fw-bold">R$
                                            {{ number_format($installmentSummary['paid_value'], 2, ',', '.') }}</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-2 border rounded bg-white text-danger">
                                        <div>Restante a Pagar</div>
                                        <div class="fw-bold">R$
                                            {{ number_format($installmentSummary['remaining_value'], 2, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card shadow-sm section-card form-surface w-100">
                    <div class="card-body form-card-body">
                        <div class="row g-3">
                            <div class="col-md-6 col-sm-12 mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="name" value="{{ $contas->name }}"
                                    disabled>
                            </div>

                            <div class="col-md-3 col-sm-12 mb-3">
                                <label for="value" class="form-label">Valor</label>
                                <input type="text" class="form-control" id="value"
                                    value="{{ 'R$ ' . number_format($contas->value, 2, ',', '.') }}" disabled>
                            </div>

                            <div class="col-md-3 col-sm-12 mb-3">
                                <label for="maturity" class="form-label">Vencimento</label>
                                <input type="text" class="form-control" id="maturity"
                                    value="{{ \Illuminate\Support\Carbon::parse($contas->maturity)->format('d/m/Y') }}"
                                    disabled>
                            </div>

                            <div class="col-md-3 col-sm-12 mb-3">
                                <label for="type" class="form-label">Tipo</label>
                                <input type="text" class="form-control" id="type"
                                    value="{{ ucfirst($contas->type) }}" disabled>
                            </div>

                            <div class="col-md-3 col-sm-12 mb-3">
                                <label for="situation" class="form-label">Situação</label>
                                <input type="text" class="form-control" id="situation"
                                    value="{{ $contas->situation_name }}" disabled>
                            </div>

                            <div class="col-md-3 col-sm-12 mb-3">
                                <label for="category" class="form-label">Categoria</label>
                                <input type="text" class="form-control" id="category"
                                    value="{{ $contas->category->name }}" disabled>
                            </div>

                            <div class="col-md-3 col-sm-12 mb-3">
                                <label for="image" class="form-label">Comprovante:</label>
                                @if (!empty($contas->image))
                                    <a class="form-control text-decoration-none"
                                        style="background-color:var(--bs-secondary-bg);"
                                        href="{{ url('img/comprovantes/' . Auth::id() . '/' . $contas->image) }}"
                                        target="_blank">Visualizar <i class="fas fa-download"></i>
                                    </a>
                                @else
                                    <a class="form-control text-decoration-none disabled"
                                        style="background-color:var(--bs-secondary-bg);">Sem comprovante</a>
                                @endif
                            </div>

                            @if ($contas->is_repeated)
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <label class="form-label">Repetição</label>
                                    <input type="text" class="form-control"
                                        value="{{ $contas->repeat_label ?: 'Lançamento repetido' }}" disabled>
                                </div>
                            @endif

                            @if ($contas->is_recurring)
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <label class="form-label">Regra de Recorrência</label>
                                    <input type="text" class="form-control"
                                        value="{{ $contas->recurrence?->frequency_label ?? 'Mensal' }} (Status: {{ $contas->recurrence?->status_label ?? 'Ativa' }})"
                                        disabled>
                                </div>
                            @endif

                            <div class="col-md-3 col-sm-12 mb-3">
                                <label for="cadastro" class="form-label">Cadastrado</label>
                                <input type="text" class="form-control" id="cadastro"
                                    value="{{ \Illuminate\Support\Carbon::parse($contas->created_at)->format('d/m/Y') }}"
                                    disabled>
                            </div>

                            <div class="col-md-3 col-sm-12 mb-3">
                                <label for="editado" class="form-label">Editado</label>
                                <input type="text" class="form-control" id="editado"
                                    value="{{ \Illuminate\Support\Carbon::parse($contas->updated_at)->format('d/m/Y') }}"
                                    disabled>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="note" class="form-label">Nota:</label>
                                <div class="form-control bg-body-tertiary" style="min-height: 120px;">
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
    <div class="modal fade" id="modalRepetirLancamento" tabindex="-1" aria-labelledby="modalRepetirLancamentoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <form method="POST" action="{{ route('contas.repeat', ['id' => $contas->id]) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalRepetirLancamentoLabel">
                            <i class="fa-solid fa-clone text-info me-2"></i>Repetir lançamento
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-light border mb-3">
                            <div class="small text-muted">Lançamento base:</div>
                            <div class="fw-bold">{{ $contas->name }}</div>
                            <div class="small text-muted mt-1">
                                Valor: <strong>R$ {{ number_format($contas->value, 2, ',', '.') }}</strong> |
                                Vencimento:
                                <strong>{{ \Carbon\Carbon::parse($contas->maturity)->format('d/m/Y') }}</strong>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="repeat_count" class="form-label">Quantidade de repetições <span
                                        class="text-danger">*</span></label>
                                <input type="number" min="1" max="120" class="form-control"
                                    id="repeat_count" name="count" value="5" required>
                                <div class="form-text">Ex: 5 gerará 5 novas ocorrências.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="repeat_frequency_show" class="form-label">Frequência <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="repeat_frequency_show" name="frequency" required>
                                    <option value="monthly" selected>Mensal</option>
                                    <option value="weekly">Semanal</option>
                                    <option value="biweekly">Quinzenal (a cada 15 dias)</option>
                                    <option value="yearly">Anual</option>
                                    <option value="custom">A cada X dias</option>
                                </select>
                            </div>

                            <div class="col-12 d-none" id="repeat_custom_interval_show">
                                <label for="repeat_interval" class="form-label">Intervalo personalizado em dias <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">A cada</span>
                                    <input type="number" min="1" max="365" class="form-control"
                                        name="interval" value="3">
                                    <span class="input-group-text">dias</span>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="repeat_start_date" class="form-label">Data da 1ª repetição</label>
                                <input type="date" class="form-control" id="repeat_start_date" name="start_date">
                                <div class="form-text">Deixe em branco para calcular a partir do vencimento.</div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="repeat_custom_value" class="form-label">Valor das cópias (opcional)</label>
                                <input type="text" class="form-control" id="repeat_custom_value" name="custom_value"
                                    placeholder="Manter valor original">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-check me-1"></i>Repetir lançamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Configurar Parcelamento Estruturado -->
    <div class="modal fade" id="modalConfigurarParcelamento" tabindex="-1"
        aria-labelledby="modalConfigurarParcelamentoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow">
                <form method="POST" action="{{ route('contas.configurar-parcelamento', ['id' => $contas->id]) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalConfigurarParcelamentoLabel">
                            <i class="fa-solid fa-layer-group text-primary me-2"></i>Configurar Parcelamento
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-light border mb-3">
                            <div class="small text-muted">Lançamento:</div>
                            <div class="fw-bold">{{ $contas->name }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Faz parte de um parcelamento?</label>
                            <select class="form-select" id="show_is_installment" name="is_installment" required>
                                <option value="1"
                                    {{ $contas->is_installment || $installmentPattern ? 'selected' : '' }}>Sim, é um
                                    lançamento parcelado</option>
                                <option value="0"
                                    {{ !$contas->is_installment && !$installmentPattern ? 'selected' : '' }}>Não (remover
                                    parcelamento estruturado)</option>
                            </select>
                        </div>

                        <div id="show_installment_fields">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label for="installment_number" class="form-label fw-bold small">Parcela atual <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="installment_number"
                                        name="installment_number" min="1" max="999"
                                        value="{{ old('installment_number', $contas->installment_number ?? ($installmentPattern['current'] ?? 1)) }}">
                                </div>
                                <div class="col-6">
                                    <label for="installments_total" class="form-label fw-bold small">Total de parcelas
                                        <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="installments_total"
                                        name="installments_total" min="1" max="999"
                                        value="{{ old('installments_total', $contas->installments_total ?? ($installmentPattern['total'] ?? 12)) }}">
                                </div>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="link_related" name="link_related"
                                    value="1" checked>
                                <label class="form-check-label small" for="link_related">
                                    Vincular automaticamente outros lançamentos antigos que parecem pertencer a este mesmo
                                    parcelamento.
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-1"></i>Salvar Parcelamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
