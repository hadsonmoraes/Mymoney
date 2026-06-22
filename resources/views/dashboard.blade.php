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
    </div>
@endsection
