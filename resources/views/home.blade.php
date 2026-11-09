@extends('layouts.app')

@section('title', 'Home')

@section('content')
    @php
        $money = fn($value) => 'R$ ' . number_format($value, 2, ',', '.');
        $saldoClass = $MyTotal >= 0
            ? 'text-success-700 dark:text-success-400'
            : 'text-error-700 dark:text-error-400';
    @endphp
    <div class="w-full">
        <div class="flex flex-wrap justify-center gap-3">
            <div class="w-full">
                <div class="page-hero mb-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="metric-label mb-2">Lançamentos</div>
                            <h1 class="page-title mb-1">Contas</h1>
                            <p class="page-subtitle">Filtre, visualize e gerencie suas entradas e saídas com mais rapidez.
                            </p>
                        </div>
                        <div class="tool-row">
                            <a href="{{ route('contas.create') }}" class="action-button action-button-primary">
                                <x-svg.plus class="size-4 me-1" /> Nova conta
                            </a>
                            <button type="button" class="action-button action-button-outline-success" data-ui-modal-toggle
                                data-ui-modal-target="#modalImportarExcel">
                                <x-svg.upload class="size-4 me-1" /> Importar Excel
                            </button>
                            <a href="{{ route('contas.importar.modelo') }}" class="action-button action-button-outline-secondary"
                                title="Baixar modelo de planilha CSV">
                                <x-svg.arrow-down class="size-4 me-1" /> Modelo
                            </a>
                            <a href="{{ url('gerar-csv?' . request()->getQueryString()) }}"
                                class="action-button action-button-outline-secondary">
                                <x-svg.table class="size-4 me-1" /> Exportar Excel
                            </a>
                            <button class="action-button action-button-outline-secondary" type="button" data-ui-modal-toggle
                                data-ui-modal-target="#modalFiltros">
                                <x-svg.horizontal-slide class="size-4 me-1" />
                                Filtros
                            </button>
                        </div>
                    </div>
                </div>


                <div class="ui-modal hidden" data-ui-modal id="modalFiltros" tabindex="-1" aria-labelledby="modalFiltrosLabel"
                    aria-hidden="true">
                    <div class="ui-modal-dialog ui-modal-dialog-wide">
                        <div class="ui-modal-content filter-modal">

                            <div class="ui-modal-header filter-modal-header">
                                <div class="flex items-center gap-3">

                                    <div class="filter-modal-icon">
                                        <x-svg.horizontal-slide class="size-5" />
                                    </div>

                                    <div>
                                        <h5 class="ui-modal-title mb-1" id="modalFiltrosLabel">
                                            Filtros
                                        </h5>

                                        <p class="filter-modal-description mb-0">
                                            Refine os lançamentos para encontrar exatamente o que procura.
                                        </p>
                                    </div>

                                </div>

                                <button type="button" class="ui-modal-close" data-ui-modal-dismiss
                                    aria-label="Fechar"></button>
                            </div>

                            <div class="ui-modal-body filter-modal-body">

                                <form action="{{ route('home') }}" id="formFiltros">

                                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">

                                        <div class="md:col-span-2">
                                            <div class="filter-field">
                                                <label for="name" class="field-label">
                                                    <x-svg.box class="size-4" />
                                                    Nome
                                                </label>

                                                <input type="text" class="field-control" id="name" name="name"
                                                    value="{{ $name }}"
                                                    placeholder="Ex.: salário, aluguel, mercado...">
                                            </div>
                                        </div>

                                        <div>
                                            <div class="filter-field">
                                                <label for="data_inicio" class="field-label">
                                                    <x-svg.calendar class="size-4" />
                                                    Data início
                                                </label>

                                                <input type="date" class="field-control" id="data_inicio"
                                                    name="data_inicio" value="{{ $data_inicio }}">
                                            </div>
                                        </div>

                                        <div>
                                            <div class="filter-field">
                                                <label for="data_fim" class="field-label">
                                                    <x-svg.calendar class="size-4" />
                                                    Data fim
                                                </label>

                                                <input type="date" class="field-control" id="data_fim" name="data_fim"
                                                    value="{{ $data_fim }}">
                                            </div>
                                        </div>

                                        <div>
                                            <div class="filter-field">
                                                <label for="situation" class="field-label">
                                                    <x-svg.check-circle class="size-4" />
                                                    Situação
                                                </label>

                                                <select class="field-select" id="situation" name="situation">
                                                    <option value="">Todas as situações</option>

                                                    <option value="paid" @selected($situation == 'paid')>
                                                        Pago
                                                    </option>

                                                    <option value="pending" @selected($situation == 'pending')>
                                                        Pendente
                                                    </option>

                                                    <option value="canceled" @selected($situation == 'canceled')>
                                                        Cancelado
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="filter-field">
                                                <label for="type" class="field-label">
                                                    <x-svg.arrow-right class="size-4" />
                                                    Tipo
                                                </label>

                                                <select class="field-select" id="type" name="type">
                                                    <option value="">Todos os tipos</option>

                                                    <option value="entrada" @selected($type == 'entrada')>
                                                        Entrada
                                                    </option>

                                                    <option value="saida" @selected($type == 'saida')>
                                                        Saída
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        @if ($perPage)
                                            <input type="hidden" name="perPage" value="{{ $perPage }}">
                                        @endif

                                    </div>

                                </form>

                            </div>

                            <div class="ui-modal-footer filter-modal-footer">

                                <a href="{{ route('home') }}" class="action-button action-button-outline-secondary">
                                    <x-svg.arrow-left class="size-4 me-1" />
                                    Limpar
                                </a>

                                <button type="submit" form="formFiltros" class="action-button action-button-primary">
                                    <x-svg.search class="size-4 me-1" />
                                    Aplicar filtros
                                </button>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="mb-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div class="min-w-0">
                        <div class="dash-stat-card">
                            <div class="flex items-start justify-between mb-2">
                                <span class="metric-label">Entradas</span>
                                <span class="icon bg-success-50 dark:bg-success-500/10"><x-svg.arrow-up class="size-5" /></span>
                            </div>
                            <h3 class="metric-value mb-1">{{ $money($entrada) }}</h3>
                        </div>
                    </div>

                    <div class="min-w-0">
                        <div class="dash-stat-card">
                            <div class="flex items-start justify-between mb-2">
                                <span class="metric-label">Saídas</span>
                                <span class="icon bg-error-50 dark:bg-error-500/10"><x-svg.arrow-down class="size-5" /></span>
                            </div>
                            <h4 class="metric-value mb-1">{{ $money($saida) }}</h4>
                        </div>
                    </div>

                    <div class="min-w-0">
                        <div class="dash-stat-card">
                            <div class="flex items-start justify-between mb-2">
                                <span class="metric-label">Saldo</span>
                                <span class="icon bg-gray-100 dark:bg-gray-700"><x-svg.bar-chart class="size-5" /></span>
                            </div>
                            <h4 class="metric-value mb-1 {{ $saldoClass }}">{{ $money($MyTotal) }}</h4>
                        </div>
                    </div>

                </div>


                <div
                    class="section-card min-w-0 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="p-5">


                        <x-alert />
                        <div class="w-full overflow-x-auto">
                            <table class="data-table align-middle">
                                <thead>
                                    <tr>
                                        {{-- <th scope="col">Id</th> --}}
                                        <th scope="col">Nome</th>
                                        <th scope="col">Valor</th>
                                        <th scope="col">Vencimento</th>
                                        <th scope="col">Situação</th>
                                        <th scope="col">Categoria</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contas as $conta)
                                        <tr>
                                            {{-- <th class="align-middle">{{ $conta->id }}</th> --}}
                                            <td class="align-middle">
                                                <div class="flex flex-wrap items-center gap-1">
                                                    <span class="font-medium">{{ $conta->name }}</span>
                                                    @if ($conta->is_repeated)
                                                        <span class="status-badge bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border"
                                                            title="Lançamento repetido">
                                                            <x-svg.copy class="size-4 me-1" />{{ $conta->repeat_label ?: 'Repetido' }}
                                                        </span>
                                                    @endif
                                                    @if ($conta->is_recurring)
                                                        <span class="status-badge bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 border"
                                                            title="Regra de recorrência ativa">
                                                            <x-svg.time class="size-4 me-1" />Recorrente
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <span
                                                    class="font-semibold">{{ 'R$' . number_format($conta->value, 2, ',', '.') }}</span>
                                            </td>
                                            <td class="align-middle">
                                                @if ($conta->maturity < now() && $conta->situation != 'paid')
                                                    <span class="text-error-700 dark:text-error-400 font-medium">{{ date('d/m/Y', strtotime($conta->maturity)) }}</span>
                                                @else
                                                    {{ date('d/m/Y', strtotime($conta->maturity)) }}
                                                @endif

                                            </td>
                                            <td class="align-middle">
                                                <a href="{{ route('situacao.alterar', ['id' => $conta->id]) }}">
                                                    <span @class([
                                                        'status-pill',
                                                        'bg-success-600 text-white dark:bg-success-500/15 dark:text-success-300' => $conta->status === 'success',
                                                        'bg-yellow-400 text-black dark:bg-warning-500/15 dark:text-warning-300' => $conta->status === 'warning',
                                                        'bg-error-600 text-white dark:bg-error-500/15 dark:text-error-300' => $conta->status === 'danger',
                                                    ])>{{ $conta->situation_name }}</span>
                                                </a>
                                            </td>
                                            <td class="align-middle">{{ $conta->category->name }}</td>
                                            <td class="align-middle"
                                                style="color: {{ ucfirst($conta->type) == 'Entrada' ? 'green' : 'red' }};">
                                                <span class="inline-flex items-center gap-1 whitespace-nowrap">
                                                    @if (ucfirst($conta->type) == 'Entrada')
                                                        <x-svg.arrow-up class="size-4 shrink-0" /> Entrada
                                                    @else
                                                        <x-svg.arrow-down class="size-4 shrink-0" /> Saída
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="hidden align-middle md:table-cell">
                                                <div class="flex items-center justify-center gap-1">
                                                    <a href="{{ route('contas.show', ['id' => $conta->id]) }}"
                                                        class="action-button action-button-outline-primary me-1" title="Visualizar">
                                                        <x-svg.eye class="size-4" />
                                                    </a>
                                                    <a href="{{ route('contas.edit', ['id' => $conta->id]) }}"
                                                        class="action-button action-button-outline-secondary me-1" title="Editar">
                                                        <x-svg.pencil class="size-4" />
                                                    </a>
                                                    <button type="button" class="action-button action-button-outline-info me-1 btn-repeat"
                                                        data-id="{{ $conta->id }}" data-name="{{ $conta->name }}"
                                                        data-value="{{ number_format($conta->value, 2, ',', '.') }}"
                                                        data-maturity="{{ date('d/m/Y', strtotime($conta->maturity)) }}"
                                                        data-raw-maturity="{{ date('Y-m-d', strtotime($conta->maturity)) }}"
                                                        data-ui-modal-toggle data-ui-modal-target="#modalRepetirLancamento"
                                                        title="Repetir lançamento">
                                                        <x-svg.copy class="size-4" />
                                                    </button>
                                                    @if ($conta->is_recurring)
                                                        <button type="button" class="action-button action-button-outline-warning me-1"
                                                            onclick="confirmarCancelamentoRecorrencia({{ $conta->id }}, '{{ addslashes($conta->name) }}')"
                                                            title="Cancelar recorrência">
                                                            <x-svg.x class="size-4" />
                                                        </button>
                                                    @endif
                                                    @if ($conta->is_repeated)
                                                        <button type="button" class="action-button action-button-outline-danger"
                                                            onclick="confirmarExclusaoSequencia({{ $conta->id }}, '{{ addslashes($conta->name) }}', '{{ $conta->repeat_label ?? 'Repetido' }}', {{ $conta->is_recurring ? 'true' : 'false' }})"
                                                            title="Apagar">
                                                            <x-svg.trash-bin class="size-4" />
                                                        </button>
                                                    @else
                                                        <form id="formExcluir{{ $conta->id }}"
                                                            action="{{ route('contas.destroy', ['id' => $conta->id]) }}"
                                                            method="post">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="action-button action-button-outline-danger"
                                                                onclick="confirmarExclusao(event, {{ $conta->id }})"
                                                                title="Apagar">
                                                                <x-svg.trash-bin class="size-4" />
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="align-middle md:hidden">
                                                <div class="flex justify-center">
                                                    <div class="relative inline-flex items-center gap-2" role="group" data-ui-dropdown>
                                                        <button type="button" class="action-button action-button-primary"
                                                            data-ui-dropdown-toggle aria-expanded="false">
                                                            Ações
                                                        </button>
                                                        <ul class="hidden floating-menu">
                                                            <li><a class="floating-menu-item"
                                                                    href="{{ route('contas.show', ['id' => $conta->id]) }}">Visualizar</a>
                                                            </li>
                                                            <li><a class="floating-menu-item"
                                                                    href="{{ route('contas.edit', ['id' => $conta->id]) }}">Editar</a>
                                                            </li>
                                                            <li>
                                                                <button type="button"
                                                                    class="floating-menu-item text-blue-light-700 dark:text-blue-light-300 btn-repeat"
                                                                    data-id="{{ $conta->id }}"
                                                                    data-name="{{ $conta->name }}"
                                                                    data-value="{{ number_format($conta->value, 2, ',', '.') }}"
                                                                    data-maturity="{{ date('d/m/Y', strtotime($conta->maturity)) }}"
                                                                    data-raw-maturity="{{ date('Y-m-d', strtotime($conta->maturity)) }}"
                                                                    data-ui-modal-toggle
                                                                    data-ui-modal-target="#modalRepetirLancamento">
                                                                    <x-svg.copy class="size-4 me-1" />Repetir lançamento
                                                                </button>
                                                            </li>
                                                            @if ($conta->is_recurring)
                                                                <li>
                                                                    <button type="button"
                                                                        class="floating-menu-item text-warning-700 dark:text-warning-400"
                                                                        onclick="confirmarCancelamentoRecorrencia({{ $conta->id }}, '{{ addslashes($conta->name) }}')">Cancelar
                                                                        recorrência</button>
                                                                </li>
                                                            @endif
                                                            <li>
                                                                @if ($conta->is_repeated)
                                                                    <a class="floating-menu-item text-error-700 dark:text-error-400"
                                                                        href="javascript:void(0)"
                                                                        onclick="confirmarExclusaoSequencia({{ $conta->id }}, '{{ addslashes($conta->name) }}', '{{ $conta->repeat_label ?? 'Repetido' }}', {{ $conta->is_recurring ? 'true' : 'false' }})">Apagar</a>
                                                                @else
                                                                    <form id="formExcluir{{ $conta->id }}"
                                                                        action="{{ route('contas.destroy', ['id' => $conta->id]) }}"
                                                                        method="post">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <a class="floating-menu-item" type="submit"
                                                                            onclick="confirmarExclusao(event, {{ $conta->id }})">Apagar</a>
                                                                    </form>
                                                                @endif
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach


                                    @if (count($contas) == 0)
                                        <tr>

                                            <td colspan="8">
                                                <div class="empty-state">
                                                    <div class="icon"><x-svg.box class="size-6" /></div>
                                                    <div class="font-semibold text-gray-800 dark:text-gray-200">Nenhuma conta encontrada</div>
                                                    <div>Use os filtros acima ou crie um novo lançamento para começar.</div>
                                                </div>
                                            </td>

                                        </tr>
                                    @endif


                                </tbody>

                            </table>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center justify-between gap-5">
                            <form id="filterForm" action="{{ route('home') }}" class="flex items-center gap-2">
                                <label for="perPage" class="whitespace-nowrap">Linhas por página:</label>
                                <select class="field-select form-select-sm" id="perPage" name="perPage"
                                    onchange="document.getElementById('filterForm').submit()">
                                    <option value="5" @selected($perPage == '5')>5</option>
                                    <option value="10" @selected($perPage == '10')>10</option>
                                    <option value="25" @selected($perPage == '25')>25</option>
                                    <option value="50" @selected($perPage == '50')>50</option>
                                    <option value="100" @selected($perPage == '100')>100</option>
                                    <option value="150" @selected($perPage == '150')>150</option>
                                    <option value="200" @selected($perPage == '200')>200</option>
                                </select>
                                @if ($name)
                                    <input type="hidden" name="name" value="{{ $name }}">
                                @endif
                                @if ($data_inicio)
                                    <input type="hidden" name="data_inicio" value="{{ $data_inicio }}">
                                @endif
                                @if ($data_fim)
                                    <input type="hidden" name="data_fim" value="{{ $data_fim }}">
                                @endif
                                @if ($situation)
                                    <input type="hidden" name="situation" value="{{ $situation }}">
                                @endif
                                @if ($type)
                                    <input type="hidden" name="type" value="{{ $type }}">
                                @endif
                            </form>
                            <div class="min-w-[25%]">
                                {{ $contas->onEachSide(0)->links() }}
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
                    <form id="formRepetirLancamento" method="POST" action="">
                        @csrf
                        <input type="hidden" name="operation_token" id="repeat_operation_token" value="">
                        <div class="ui-modal-header">
                            <h5 class="ui-modal-title" id="modalRepetirLancamentoLabel">
                                <x-svg.copy class="size-4 text-blue-light-700 dark:text-blue-light-300 me-2" />Repetir lançamento
                            </h5>
                            <button type="button" class="ui-modal-close" data-ui-modal-dismiss
                                aria-label="Fechar"></button>
                        </div>
                        <div class="ui-modal-body">
                            <div
                                class="mb-3 rounded-lg border border-gray-200 bg-gray-50 p-3 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Lançamento base:</div>
                                <div class="font-bold" id="modalRepeatContaName"></div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Valor: <strong id="modalRepeatContaValue"></strong> | Vencimento: <strong
                                        id="modalRepeatContaMaturity"></strong>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                <div class="min-w-0">
                                    <label for="repeat_count" class="field-label">Quantidade de repetições <span
                                            class="text-error-700 dark:text-error-400">*</span></label>
                                    <input type="number" min="1" max="120" class="field-control"
                                        id="repeat_count" name="count" value="5" required>
                                    <div class="field-help">Ex: 5 gerará 5 novas ocorrências.</div>
                                </div>

                                <div class="min-w-0">
                                    <label for="repeat_frequency" class="field-label">Frequência <span
                                            class="text-error-700 dark:text-error-400">*</span></label>
                                    <select class="field-select" id="repeat_frequency" name="frequency" required>
                                        <option value="monthly" selected>Mensal</option>
                                        <option value="weekly">Semanal</option>
                                        <option value="biweekly">Quinzenal (a cada 15 dias)</option>
                                        <option value="yearly">Anual</option>
                                        <option value="custom">A cada X dias</option>
                                    </select>
                                </div>

                                <div class="hidden min-w-0 md:col-span-2" id="repeat_custom_interval_wrapper">
                                    <label for="repeat_interval" class="field-label">Intervalo personalizado em dias <span
                                            class="text-error-700 dark:text-error-400">*</span></label>
                                    <div class="flex flex-col gap-2 sm:flex-row">
                                        <span
                                            class="inline-flex shrink-0 items-center rounded-lg border border-gray-300 bg-gray-50 px-3 text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">A
                                            cada</span>
                                        <input type="number" min="1" max="365" class="field-control min-w-0 flex-1"
                                            id="repeat_interval" name="interval">
                                        <span
                                            class="inline-flex shrink-0 items-center rounded-lg border border-gray-300 bg-gray-50 px-3 text-gray-600 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">dias</span>
                                    </div>
                                </div>

                                <div class="min-w-0">
                                    <label for="repeat_start_date" class="field-label">Data da 1ª repetição</label>
                                    <input type="date" class="field-control" id="repeat_start_date" name="start_date">
                                    <div class="field-help">Deixe em branco para calcular a partir do vencimento.</div>
                                </div>

                                <div class="min-w-0">
                                    <label for="repeat_custom_value" class="field-label">Valor das cópias
                                        (opcional)</label>
                                    <input type="text" class="field-control" id="repeat_custom_value"
                                        name="custom_value" placeholder="Manter valor original">
                                    <div class="field-help">Deixe em branco para manter o mesmo valor.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ui-modal-footer">
                            <button type="button" class="action-button action-button-outline-secondary"
                                data-ui-modal-dismiss>Cancelar</button>
                            <button type="submit" class="action-button action-button-primary" id="btnSubmitRepetir">
                                <x-svg.check class="size-4 me-1" />Repetir lançamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Importar Excel / CSV -->
        <div class="ui-modal hidden" data-ui-modal id="modalImportarExcel" tabindex="-1" aria-labelledby="modalImportarExcelLabel"
            aria-hidden="true">
            <div class="ui-modal-dialog ui-modal-dialog-wide">
                <div class="ui-modal-content shadow">
                    <div class="ui-modal-header">
                        <h5 class="ui-modal-title" id="modalImportarExcelLabel">
                            <x-svg.table class="size-4 text-success-700 dark:text-success-400 me-2" />Importar Lançamentos via Excel / CSV
                        </h5>
                        <button type="button" class="ui-modal-close" data-ui-modal-dismiss aria-label="Fechar"></button>
                    </div>
                    <div class="ui-modal-body">
                        <div
                            class="flex flex-col items-start justify-between gap-3 rounded-lg border border-blue-light-200 bg-blue-light-50 px-3 py-2 text-sm text-blue-light-900 dark:border-blue-light-800 dark:bg-blue-light-950 dark:text-blue-light-100 sm:flex-row sm:items-center">
                            <div>
                                <x-svg.info-circle class="size-4 me-1" />
                                Formatos aceitos: <strong>.xlsx</strong> e <strong>.csv</strong>. Use a coluna
                                <em>Situação</em> (pago/pendente), <em>Tipo</em> (entrada/saida), <em>Valor</em> (R$
                                1.250,50) e <em>Vencimento</em> (dd/mm/aaaa).
                            </div>
                            <a href="{{ route('contas.importar.modelo') }}"
                                class="action-button action-button-sm action-button-outline-primary shrink-0 whitespace-nowrap ms-2">
                                <x-svg.arrow-down class="size-4 me-1" />Baixar Modelo
                            </a>
                        </div>

                        <!-- Formulário de Upload e Análise -->
                        <div class="mb-3 rounded-xl border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                            <label for="excel_file_input" class="field-label font-bold text-sm">Selecione o arquivo de
                                planilha</label>
                            <div class="flex flex-col gap-2 sm:flex-row">
                                <input type="file" class="field-control" id="excel_file_input"
                                    accept=".xlsx,.csv,.txt">
                                <button type="button" class="action-button action-button-primary" id="btnAnalisarExcel">
                                    <x-svg.search class="size-4 me-1" /> Analisar Arquivo
                                </button>
                            </div>
                            <div class="hidden mt-1 text-sm text-error-600" id="excelFileError"></div>
                        </div>

                        <!-- Spinner de Carregamento -->
                        <div class="hidden py-4 text-center" id="excelLoadingSpinner">
                            <div class="mx-auto size-6 animate-spin rounded-full border-2 border-gray-200 border-t-brand-500" role="status">
                                <span class="sr-only">Processando...</span>
                            </div>
                            <div class="mt-2 text-gray-500 dark:text-gray-400 text-sm">Analisando e validando dados do arquivo...</div>
                        </div>

                        <!-- Resultado da Análise -->
                        <div class="hidden" id="excelPreviewContainer">
                            <div class="mb-3 grid grid-cols-2 gap-2 text-center sm:grid-cols-4">
                                <div>
                                    <div class="rounded border border-gray-200 bg-gray-50 p-2 dark:border-gray-700 dark:bg-gray-800">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Total Linhas</div>
                                        <h5 class="mb-0 font-bold" id="previewTotalRows">0</h5>
                                    </div>
                                </div>
                                <div>
                                    <div
                                        class="rounded border border-success-200 bg-success-50 p-2 text-success-700 dark:border-success-800 dark:bg-success-950 dark:text-success-200">
                                        <div class="text-sm">Válidas</div>
                                        <h5 class="mb-0 font-bold" id="previewValidRows">0</h5>
                                    </div>
                                </div>
                                <div>
                                    <div
                                        class="rounded border border-warning-200 bg-warning-50 p-2 text-warning-700 dark:border-warning-800 dark:bg-warning-950 dark:text-warning-200">
                                        <div class="text-sm">Avisos</div>
                                        <h5 class="mb-0 font-bold" id="previewWarnings">0</h5>
                                    </div>
                                </div>
                                <div>
                                    <div
                                        class="rounded border border-error-200 bg-error-50 p-2 text-error-700 dark:border-error-800 dark:bg-error-950 dark:text-error-200">
                                        <div class="text-sm">Erros</div>
                                        <h5 class="mb-0 font-bold" id="previewErrors">0</h5>
                                    </div>
                                </div>
                            </div>

                            <!-- Erros Detalhados se houver -->
                            <div
                                class="hidden rounded-lg border border-error-200 bg-error-50 px-3 py-2 text-sm text-error-700 dark:border-error-800 dark:bg-error-950 dark:text-error-200"
                                id="excelErrorsBox">
                                <h6 class="notice-heading text-sm font-bold mb-1"><i
                                        class="fa-solid fa-circle-exclamation me-1"></i>Erros encontrados (estas linhas
                                    serão ignoradas):</h6>
                                <ul class="mb-0 ps-3" id="excelErrorsList"></ul>
                            </div>

                            <!-- Avisos / Duplicatas se houver -->
                            <div
                                class="hidden rounded-lg border border-warning-200 bg-warning-50 px-3 py-2 text-sm text-warning-700 dark:border-warning-800 dark:bg-warning-950 dark:text-warning-200"
                                id="excelWarningsBox">
                                <h6 class="notice-heading text-sm font-bold mb-1"><i
                                        class="fa-solid fa-triangle-exclamation me-1"></i>Avisos de duplicidade potencial:
                                </h6>
                                <ul class="mb-0 ps-3" id="excelWarningsList"></ul>
                            </div>

                            <!-- Prévia dos lançamentos válidos -->
                            <h6 class="font-bold text-sm mb-2"><x-svg.table class="size-4 me-1" />Pré-visualização dos
                                Lançamentos (primeiras 10 linhas):</h6>
                            <div
                                class="mb-3 max-h-[250px] overflow-auto rounded border border-gray-200 dark:border-gray-700">
                                <table class="data-table table-sm table-striped mb-0 text-sm">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>Linha</th>
                                            <th>Nome</th>
                                            <th>Vencimento</th>
                                            <th>Valor</th>
                                            <th>Tipo</th>
                                            <th>Categoria</th>
                                            <th>Situação</th>
                                        </tr>
                                    </thead>
                                    <tbody id="excelPreviewTableBody"></tbody>
                                </table>
                            </div>

                            <!-- Formulário de Confirmação -->
                            <form action="{{ route('contas.importar.confirm') }}" method="POST"
                                id="formConfirmarImportacao">
                                @csrf
                                <div class="mb-3 rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                                    <input class="mr-2 size-4 shrink-0 align-middle accent-brand-500" type="checkbox" role="switch"
                                        id="auto_create_categories" name="auto_create_categories" value="1" checked>
                                    <label class="font-semibold" for="auto_create_categories">
                                        Criar automaticamente categorias novas identificadas na planilha
                                    </label>
                                    <div class="field-help text-sm text-gray-500 dark:text-gray-400">Se desativado, contas com categorias novas
                                        serão importadas sem categoria ou com a padrão.</div>
                                </div>

                                <div class="flex justify-end gap-2">
                                    <button type="button" class="action-button action-button-secondary"
                                        data-ui-modal-dismiss>Cancelar</button>
                                    <button type="submit" class="action-button action-button-success" id="btnConfirmarImportacao">
                                        <x-svg.check class="size-4 me-1" /> Confirmar Importação
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
