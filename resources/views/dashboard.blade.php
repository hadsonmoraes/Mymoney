@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $money = fn($value) => 'R$ ' . number_format($value, 2, ',', '.');
        $saldoClass = $MyTotal >= 0 ? 'text-success' : 'text-danger';
    @endphp

    <div class="container-fluid p-4">
        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <div class="page-hero dash-hero">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <div class="metric-label mb-2">Painel financeiro</div>
                            <h4 class="page-title mb-1">Dashboard Financeiro</h4>
                            <p class="page-subtitle">Resumo por período com status, totais e movimentações das contas.</p>
                        </div>
                        <div class="text-end">
                            <small class="metric-label d-block">Período selecionado</small>
                            <span class="fw-semibold">
                                {{ \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') }}
                                até
                                {{ \Carbon\Carbon::parse($data_fim)->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-2">
            <div class="col-md-12">
                <div class="card dash-filter-card section-card">
                    <div class="card-body p-4">
                        <form action="{{ route('dashboard') }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4 col-sm-12">
                                    <label for="data_inicio" class="form-label fw-semibold">Data Início</label>
                                    <input type="date" class="form-control" id="data_inicio" name="data_inicio"
                                        value="{{ $data_inicio }}">
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <label for="data_fim" class="form-label fw-semibold">Data Fim</label>
                                    <input type="date" class="form-control" id="data_fim" name="data_fim"
                                        value="{{ $data_fim }}">
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-magnifying-glass me-1"></i> Pesquisar
                                    </button>
                                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Limpar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-4 col-md-6">
                <div class="card dash-stat-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="metric-label">Saldo Entrada/Saída</span>
                            <span class="icon bg-soft-secondary"><i class="fa-solid fa-scale-balanced"></i></span>
                        </div>
                        <h3 class="metric-value mb-1 {{ $saldoClass }}">{{ $money($MyTotal) }}</h3>
                        <small class="text-muted">Entradas - Saídas no período</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="card dash-stat-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="metric-label">Total de Contas</span>
                            <span class="icon bg-soft-primary"><i class="fa-solid fa-layer-group"></i></span>
                        </div>
                        <h4 class="metric-value mb-1">{{ $money($total) }}</h4>
                        <small class="text-muted">Quantidade: {{ $totalquantidade }}</small>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-12">
                <div class="card dash-stat-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="metric-label">Movimentação</span>
                            <span class="icon bg-soft-info"><i class="fa-solid fa-arrow-right-arrow-left"></i></span>
                        </div>
                        <p class="mb-1"><span class="fw-semibold">Entrada:</span> {{ $money($contasEntradaValor) }}
                            ({{ $contasEntradaQuantidade }})</p>
                        <p class="mb-0"><span class="fw-semibold">Saída:</span> {{ $money($contasSaidaValor) }}
                            ({{ $contasSaidaQuantidade }})</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-lg-3 col-md-6">
                <div class="card dash-stat-card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">Pago</span>
                            <span class="icon bg-soft-success"><i class="fa-solid fa-circle-check"></i></span>
                        </div>
                        <div class="fw-bold">{{ $money($contasPagasValor) }}</div>
                        <small class="text-muted">Qtd: {{ $contasPagasQuantidade }}</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card dash-stat-card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">Pendente</span>
                            <span class="icon bg-soft-warning"><i class="fa-solid fa-hourglass-half"></i></span>
                        </div>
                        <div class="fw-bold">{{ $money($contasPendentesValor) }}</div>
                        <small class="text-muted">Qtd: {{ $contasPendentesQuantidade }}</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card dash-stat-card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">Cancelado</span>
                            <span class="icon bg-soft-danger"><i class="fa-solid fa-ban"></i></span>
                        </div>
                        <div class="fw-bold">{{ $money($contasCanceladasValor) }}</div>
                        <small class="text-muted">Qtd: {{ $contasCanceladasQuantidade }}</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card dash-stat-card h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-semibold">Entrada / Saída</span>
                            <span class="icon bg-soft-dark"><i class="fa-solid fa-chart-column"></i></span>
                        </div>
                        <div class="fw-bold">{{ $money($contasEntradaValor) }}</div>
                        <small class="text-muted">Saída: {{ $money($contasSaidaValor) }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projeção Futura e Próximos 30 Dias -->
        <div class="row g-3 mt-3">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0 fw-bold"><i
                                        class="fa-solid fa-calendar-days text-primary me-2"></i>Compromissos Futuros
                                    (Próximos 30 Dias)</h5>
                                <small class="text-muted">Projeção com base em lançamentos pendentes com vencimento nos
                                    próximos 30 dias.</small>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border px-3 py-2">Próximos 30 dias</span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="border rounded p-3 bg-light">
                                    <span class="text-muted small fw-semibold">Receitas Previstas</span>
                                    <h4 class="text-success fw-bold mb-0 mt-1">{{ $money($futureEntradasValor) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 bg-light">
                                    <span class="text-muted small fw-semibold">Despesas Previstas</span>
                                    <h4 class="text-danger fw-bold mb-0 mt-1">{{ $money($futureSaidasValor) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 bg-light">
                                    <span class="text-muted small fw-semibold">Saldo Projetado do Período</span>
                                    <h4
                                        class="{{ $futureSaldoProjetado >= 0 ? 'text-primary' : 'text-danger' }} fw-bold mb-0 mt-1">
                                        {{ $money($futureSaldoProjetado) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas de Vencimento e Contas Vencidas -->
        <div class="row g-3 mt-2">
            <!-- Vencidos -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                        <h6 class="mb-0 fw-bold text-danger"><i
                                class="fa-solid fa-circle-exclamation me-2"></i>Lançamentos Vencidos Pendentes</h6>
                        <span class="badge bg-danger rounded-pill">{{ $overdueCount }}</span>
                    </div>
                    <div class="card-body pt-0">
                        @if ($overdueCount > 0)
                            <div class="alert alert-danger py-2 small mb-3">
                                <strong>Atenção:</strong> Existem {{ $overdueCount }} contas vencidas totalizando
                                <strong>{{ $money($overdueValor) }}</strong>.
                            </div>
                            <div class="list-group list-group-flush border rounded"
                                style="max-height: 220px; overflow-y: auto;">
                                @foreach ($overdueContas->take(5) as $c)
                                    <div
                                        class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                        <div>
                                            <div class="fw-semibold small">{{ $c->name }}</div>
                                            <small class="text-danger">Venceu em
                                                {{ \Carbon\Carbon::parse($c->maturity)->format('d/m/Y') }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="fw-bold text-dark small">{{ $money($c->value) }}</span>
                                            <a href="{{ route('contas.show', $c->id) }}"
                                                class="btn btn-sm btn-link p-0 d-block text-secondary small">Ver</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted small">
                                <i class="fa-regular fa-circle-check fa-2x text-success mb-2"></i>
                                <div>Nenhum lançamento vencido pendente. Excelente!</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Vencendo Hoje e Próximos 7 Dias -->
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                        <h6 class="mb-0 fw-bold text-warning-emphasis"><i class="fa-solid fa-clock me-2"></i>Vencendo Hoje
                            & Próximos 7 Dias</h6>
                        <span
                            class="badge bg-warning text-dark rounded-pill">{{ $dueTodayContas->count() + $upcoming7DaysContas->count() }}</span>
                    </div>
                    <div class="card-body pt-0">
                        @if ($dueTodayContas->count() > 0 || $upcoming7DaysContas->count() > 0)
                            <div class="list-group list-group-flush border rounded"
                                style="max-height: 250px; overflow-y: auto;">
                                @foreach ($dueTodayContas as $c)
                                    <div
                                        class="list-group-item list-group-item-danger d-flex justify-content-between align-items-center py-2 px-3">
                                        <div>
                                            <span class="badge bg-danger me-1">Hoje</span>
                                            <span class="fw-semibold small">{{ $c->name }}</span>
                                        </div>
                                        <span class="fw-bold small">{{ $money($c->value) }}</span>
                                    </div>
                                @endforeach
                                @foreach ($upcoming7DaysContas as $c)
                                    <div
                                        class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                        <div>
                                            <span
                                                class="badge bg-warning text-dark me-1">{{ \Carbon\Carbon::parse($c->maturity)->format('d/m') }}</span>
                                            <span class="fw-semibold small">{{ $c->name }}</span>
                                        </div>
                                        <span class="fw-bold small text-muted">{{ $money($c->value) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted small">
                                <i class="fa-regular fa-calendar-check fa-2x text-primary mb-2"></i>
                                <div>Nenhum vencimento nos próximos 7 dias.</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Progresso dos Parcelamentos Ativos -->
        <div class="row g-3 mt-2 mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                        <div>
                            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-layer-group text-info me-2"></i>Progresso dos
                                Parcelamentos Ativos</h5>
                            <small class="text-muted">Acompanhamento consolidado com dados estruturados reais de cada
                                parcela.</small>
                        </div>
                        <span class="badge bg-info text-white rounded-pill px-3">{{ $activeInstallments->count() }}
                            ativos</span>
                    </div>
                    <div class="card-body pt-0">
                        @if ($activeInstallments->isEmpty())
                            <div class="text-center py-4 text-muted small">
                                <i class="fa-solid fa-receipt fa-2x text-secondary mb-2"></i>
                                <div>Nenhum parcelamento ativo no momento.</div>
                            </div>
                        @else
                            <div class="row g-3">
                                @foreach ($activeInstallments as $inst)
                                    <div class="col-md-6 col-xl-4">
                                        <div class="border rounded p-3 bg-light-subtle h-100">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="fw-bold mb-0 text-dark">{{ $inst->name }}</h6>
                                                    <small class="text-muted">{{ $inst->category }}</small>
                                                </div>
                                                <span
                                                    class="badge bg-primary">{{ $inst->paid_count }}/{{ $inst->total_installments }}</span>
                                            </div>

                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between small text-muted mb-1">
                                                    <span>Progresso</span>
                                                    <span
                                                        class="fw-bold text-primary">{{ $inst->progress_percent }}%</span>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: {{ $inst->progress_percent }}%;"
                                                        aria-valuenow="{{ $inst->progress_percent }}" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between small pt-1 border-top mt-2">
                                                <span class="text-muted">Restante:
                                                    <strong>{{ $money($inst->remaining_value) }}</strong></span>
                                                <span class="text-muted">Total:
                                                    <strong>{{ $money($inst->total_value) }}</strong></span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
