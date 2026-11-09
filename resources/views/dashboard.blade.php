@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $money = fn($value) => 'R$ ' . number_format($value, 2, ',', '.');
        $saldoClass = $MyTotal >= 0
            ? 'text-success-700 dark:text-success-400'
            : 'text-error-700 dark:text-error-400';
    @endphp

    <div class="w-full p-3">
        <div class="mb-3">
            <div class="w-full">
                <div class="page-hero dash-hero">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="metric-label mb-2">Painel financeiro</div>
                            <h4 class="page-title mb-1">Dashboard Financeiro</h4>
                            <p class="page-subtitle">Resumo por período com status, totais e movimentações das contas.</p>
                        </div>
                        <div class="text-right">
                            <small class="metric-label block">Período selecionado</small>
                            <span class="font-semibold">
                                {{ \Carbon\Carbon::parse($data_inicio)->format('d/m/Y') }}
                                até
                                {{ \Carbon\Carbon::parse($data_fim)->format('d/m/Y') }}
                            </span>
                        </div>
                        <button type="button" class="action-button action-button-outline-secondary" data-ui-modal-toggle
                            data-ui-modal-target="#modalFiltrosDashboard">
                            <x-svg.horizontal-slide class="size-4 me-1" />
                            Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-modal hidden" data-ui-modal id="modalFiltrosDashboard" tabindex="-1" aria-labelledby="modalFiltrosDashboardLabel"
            aria-hidden="true">
            <div class="ui-modal-dialog">
                <div class="ui-modal-content filter-modal">

                    <div class="ui-modal-header filter-modal-header">

                        <div class="flex items-center gap-3">

                            <div class="filter-modal-icon">
                                <x-svg.horizontal-slide class="size-5" />
                            </div>

                            <div>
                                <h5 class="ui-modal-title mb-1" id="modalFiltrosDashboardLabel">
                                    Filtros do dashboard
                                </h5>

                                <p class="filter-modal-description mb-0">
                                    Selecione o período que deseja visualizar.
                                </p>
                            </div>

                        </div>

                        <button type="button" class="ui-modal-close" data-ui-modal-dismiss aria-label="Fechar"></button>

                    </div>

                    <div class="ui-modal-body filter-modal-body">

                        <form action="{{ route('dashboard') }}" id="formFiltrosDashboard">

                            <div class="mb-3 grid grid-cols-1 gap-3 md:grid-cols-2">

                                <div>
                                    <div class="filter-field">

                                        <label for="dashboard_data_inicio" class="field-label">
                                            <x-svg.calendar class="size-4" />
                                            Data início
                                        </label>

                                        <input type="date" class="field-control" id="dashboard_data_inicio"
                                            name="data_inicio" value="{{ $data_inicio }}">

                                    </div>
                                </div>

                                <div>
                                    <div class="filter-field">

                                        <label for="dashboard_data_fim" class="field-label">
                                            <x-svg.calendar class="size-4" />
                                            Data fim
                                        </label>

                                        <input type="date" class="field-control" id="dashboard_data_fim" name="data_fim"
                                            value="{{ $data_fim }}">

                                    </div>
                                </div>

                            </div>

                        </form>

                    </div>

                    <div class="ui-modal-footer filter-modal-footer">

                        <a href="{{ route('dashboard') }}" class="action-button action-button-outline-secondary">
                            <x-svg.arrow-left class="size-4 me-1" />
                            Limpar
                        </a>

                        <button type="submit" form="formFiltrosDashboard" class="action-button action-button-primary">
                            <x-svg.search class="size-4 me-1" />
                            Aplicar período
                        </button>

                    </div>

                </div>
            </div>
        </div>

        <div class="mb-3 grid grid-cols-1 gap-3 md:grid-cols-3">
            <div class="min-w-0">
                <div class="dash-stat-card">
                    <div class="p-4">
                        <div class="mb-2 flex items-start justify-between">
                            <span class="metric-label">Saldo Entrada/Saída</span>
                            <span class="icon bg-gray-100 dark:bg-gray-700"><x-svg.bar-chart class="size-5" /></span>
                        </div>
                        <h3 class="metric-value mb-1 {{ $saldoClass }}">{{ $money($MyTotal) }}</h3>
                        <small class="text-gray-500 dark:text-gray-400">Entradas - Saídas no período</small>
                    </div>
                </div>
            </div>

            <div class="min-w-0">
                <div class="dash-stat-card">
                    <div class="p-4">
                        <div class="mb-2 flex items-start justify-between">
                            <span class="metric-label">Total de Contas</span>
                            <span class="icon bg-brand-50 dark:bg-brand-500/10"><x-svg.box class="size-5" /></span>
                        </div>
                        <h4 class="metric-value mb-1">{{ $money($total) }}</h4>
                        <small class="text-gray-500 dark:text-gray-400">Quantidade: {{ $totalquantidade }}</small>
                    </div>
                </div>
            </div>

            <div class="min-w-0">
                <div class="dash-stat-card">
                    <div class="p-4">
                        <div class="mb-2 flex items-start justify-between">
                            <span class="metric-label">Movimentação</span>
                            <span class="icon bg-blue-light-50 dark:bg-blue-light-500/10"><x-svg.arrow-right class="size-5" /></span>
                        </div>
                        <p class="mb-1"><span class="font-semibold">Entrada:</span> {{ $money($contasEntradaValor) }}
                            ({{ $contasEntradaQuantidade }})</p>
                        <p class="mb-0"><span class="font-semibold">Saída:</span> {{ $money($contasSaidaValor) }}
                            ({{ $contasSaidaQuantidade }})</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="min-w-0">
                <div class="dash-stat-card">
                    <div class="p-3">
                        <div class="mb-2 flex justify-between">
                            <span class="font-semibold">Pago</span>
                            <span class="icon bg-success-50 dark:bg-success-500/10"><x-svg.check-circle class="size-5" /></span>
                        </div>
                        <div class="font-bold">{{ $money($contasPagasValor) }}</div>
                        <small class="text-gray-500 dark:text-gray-400">Qtd: {{ $contasPagasQuantidade }}</small>
                    </div>
                </div>
            </div>

            <div class="min-w-0">
                <div class="dash-stat-card">
                    <div class="p-3">
                        <div class="mb-2 flex justify-between">
                            <span class="font-semibold">Pendente</span>
                            <span class="icon bg-warning-50 dark:bg-warning-500/10"><x-svg.time class="size-5" /></span>
                        </div>
                        <div class="font-bold">{{ $money($contasPendentesValor) }}</div>
                        <small class="text-gray-500 dark:text-gray-400">Qtd: {{ $contasPendentesQuantidade }}</small>
                    </div>
                </div>
            </div>

            <div class="min-w-0">
                <div class="dash-stat-card">
                    <div class="p-3">
                        <div class="mb-2 flex justify-between">
                            <span class="font-semibold">Cancelado</span>
                            <span class="icon bg-error-50 dark:bg-error-500/10"><x-svg.x class="size-5" /></span>
                        </div>
                        <div class="font-bold">{{ $money($contasCanceladasValor) }}</div>
                        <small class="text-gray-500 dark:text-gray-400">Qtd: {{ $contasCanceladasQuantidade }}</small>
                    </div>
                </div>
            </div>

            <div class="min-w-0">
                <div class="dash-stat-card">
                    <div class="p-3">
                        <div class="mb-2 flex justify-between">
                            <span class="font-semibold">Entrada / Saída</span>
                            <span class="icon bg-gray-100 dark:bg-gray-700"><x-svg.bar-chart class="size-5" /></span>
                        </div>
                        <div class="font-bold">{{ $money($contasEntradaValor) }}</div>
                        <small class="text-gray-500 dark:text-gray-400">Saída: {{ $money($contasSaidaValor) }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projeção Futura e Próximos 30 Dias -->
        <div class="mb-3">
            <div class="w-full">
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="border-b border-gray-200 p-4 dark:border-gray-700">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h5 class="mb-0 font-bold"><x-svg.calendar class="size-5 text-brand-600 dark:text-brand-400 me-2" />Compromissos Futuros
                                    (Próximos 30 Dias)</h5>
                                <small class="text-gray-500 dark:text-gray-400">Projeção com base em lançamentos pendentes com vencimento nos
                                    próximos 30 dias.</small>
                            </div>
                            <span class="status-badge bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 border px-3 py-2">Próximos 30 dias</span>
                        </div>
                    </div>
                    <div class="p-4 pt-0">
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                            <div>
                                <div class="border rounded p-3 bg-gray-50 dark:bg-gray-800">
                                    <span class="text-gray-500 dark:text-gray-400 text-sm font-semibold">Receitas Previstas</span>
                                    <h4 class="text-success-700 dark:text-success-400 font-bold mb-0 mt-1">{{ $money($futureEntradasValor) }}</h4>
                                </div>
                            </div>
                            <div>
                                <div class="border rounded p-3 bg-gray-50 dark:bg-gray-800">
                                    <span class="text-gray-500 dark:text-gray-400 text-sm font-semibold">Despesas Previstas</span>
                                    <h4 class="text-error-700 dark:text-error-400 font-bold mb-0 mt-1">{{ $money($futureSaidasValor) }}</h4>
                                </div>
                            </div>
                            <div>
                                <div class="border rounded p-3 bg-gray-50 dark:bg-gray-800">
                                    <span class="text-gray-500 dark:text-gray-400 text-sm font-semibold">Saldo Projetado do Período</span>
                                    <h4
                                        class="{{ $futureSaldoProjetado >= 0 ? 'text-brand-600 dark:text-brand-400' : 'text-error-700 dark:text-error-400' }} font-bold mb-0 mt-1">
                                        {{ $money($futureSaldoProjetado) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas de Vencimento e Contas Vencidas -->
        <div class="mt-2 grid grid-cols-1 gap-3 lg:grid-cols-2">
            <!-- Vencidos -->
            <div class="min-w-0">
                <div class="flex h-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between border-b border-gray-200 p-4 dark:border-gray-700">
                        <h6 class="mb-0 font-bold text-error-700 dark:text-error-400"><x-svg.octagon-alert class="size-4 me-2" />Lançamentos Vencidos Pendentes</h6>
                        <span class="status-badge bg-error-600 text-white rounded-full">{{ $overdueCount }}</span>
                    </div>
                    <div class="flex-1 p-4 pt-0">
                        @if ($overdueCount > 0)
                            <div class="mb-3 rounded-lg border border-error-200 bg-error-50 px-3 py-2 text-sm text-error-700 dark:border-error-800 dark:bg-error-950 dark:text-error-200">
                                <strong>Atenção:</strong> Existem {{ $overdueCount }} contas vencidas totalizando
                                <strong>{{ $money($overdueValor) }}</strong>.
                            </div>
                            <div
                                class="max-h-[220px] overflow-y-auto rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                                @foreach ($overdueContas->take(5) as $c)
                                    <div
                                        class="flex items-center justify-between gap-3 border-b border-gray-200 px-3 py-2 last:border-b-0 dark:border-gray-700">
                                        <div>
                                            <div class="font-semibold text-sm">{{ $c->name }}</div>
                                            <small class="text-error-700 dark:text-error-400">Venceu em
                                                {{ \Carbon\Carbon::parse($c->maturity)->format('d/m/Y') }}</small>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-bold text-gray-900 dark:text-gray-100 text-sm">{{ $money($c->value) }}</span>
                                            <a href="{{ route('contas.show', $c->id) }}"
                                                class="action-button action-button-sm action-button-link p-0 block text-gray-600 dark:text-gray-300 text-sm">Ver</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm">
                                <x-svg.check-circle class="size-8 text-success-700 dark:text-success-400 mb-2" />
                                <div>Nenhum lançamento vencido pendente. Excelente!</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Vencendo Hoje e Próximos 7 Dias -->
            <div class="min-w-0">
                <div class="flex h-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between border-b border-gray-200 p-4 dark:border-gray-700">
                        <h6 class="mb-0 font-bold text-warning-800 dark:text-warning-300"><x-svg.time class="size-4 me-2" />Vencendo Hoje
                            & Próximos 7 Dias</h6>
                        <span
                            class="status-badge bg-warning-500 text-gray-900 dark:text-gray-100 rounded-full">{{ $dueTodayContas->count() + $upcoming7DaysContas->count() }}</span>
                    </div>
                    <div class="flex-1 p-4 pt-0">
                        @if ($dueTodayContas->count() > 0 || $upcoming7DaysContas->count() > 0)
                            <div
                                class="max-h-[250px] overflow-y-auto rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                                @foreach ($dueTodayContas as $c)
                                    <div
                                        class="flex items-center justify-between gap-3 border-b border-gray-200 px-3 py-2 last:border-b-0 dark:border-gray-700">
                                        <div>
                                            <span class="status-badge bg-error-600 text-white me-1">Hoje</span>
                                            <span class="font-semibold text-sm">{{ $c->name }}</span>
                                        </div>
                                        <span class="font-bold text-sm">{{ $money($c->value) }}</span>
                                    </div>
                                @endforeach
                                @foreach ($upcoming7DaysContas as $c)
                                    <div
                                        class="flex items-center justify-between gap-3 border-b border-gray-200 px-3 py-2 last:border-b-0 dark:border-gray-700">
                                        <div>
                                            <span
                                                class="status-badge bg-warning-500 text-gray-900 dark:text-gray-100 me-1">{{ \Carbon\Carbon::parse($c->maturity)->format('d/m') }}</span>
                                            <span class="font-semibold text-sm">{{ $c->name }}</span>
                                        </div>
                                        <span class="font-bold text-sm text-gray-500 dark:text-gray-400">{{ $money($c->value) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-gray-500 dark:text-gray-400 text-sm">
                                <x-svg.calendar class="size-8 text-brand-600 dark:text-brand-400 mb-2" />
                                <div>Nenhum vencimento nos próximos 7 dias.</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
