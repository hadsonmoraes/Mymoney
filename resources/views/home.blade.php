@extends('layouts.app')

@section('title', 'Home')

@section('content')
    @php
        $money = fn($value) => 'R$ ' . number_format($value, 2, ',', '.');
        $saldoClass = $MyTotal >= 0 ? 'text-success' : 'text-danger';
    @endphp
    <div class="container-fluid p-4">
        <div class="row justify-content-center g-3">
            <div class="col-md-12">
                <div class="page-hero mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <div class="metric-label mb-2">Lançamentos</div>
                            <h1 class="page-title mb-1">Contas</h1>
                            <p class="page-subtitle">Filtre, visualize e gerencie suas entradas e saídas com mais rapidez.
                            </p>
                        </div>
                        <div class="tool-row">
                            <a href="{{ route('contas.create') }}" class="btn btn-primary">
                                <i class="fa-solid fa-circle-plus me-1"></i> Nova conta
                            </a>
                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                                data-bs-target="#modalImportarExcel">
                                <i class="fa-solid fa-file-import me-1"></i> Importar Excel
                            </button>
                            <a href="{{ route('contas.importar.modelo') }}" class="btn btn-outline-secondary"
                                title="Baixar modelo de planilha CSV">
                                <i class="fa-solid fa-download me-1"></i> Modelo
                            </a>
                            <a href="{{ url('gerar-csv?' . request()->getQueryString()) }}"
                                class="btn btn-outline-secondary">
                                <i class="fa-solid fa-file-excel me-1"></i> Exportar Excel
                            </a>
                            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filtro">
                                <i class="fas fa-filter me-1"></i> Filtros
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm collapse mb-3 filter-card" id="filtro">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Filtros rápidos</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('home') }}">
                            <div class="row">
                                <div class="col-md-2 col-sm-12">
                                    <label for="name" class="form-label fw-bold">Nome</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $name }}">
                                </div>

                                <div class="col-md-2 col-sm-12">
                                    <label for="data_inicio" class="form-label fw-bold">Data Início</label>
                                    <input type="date" class="form-control" id="data_inicio" name="data_inicio"
                                        value="{{ $data_inicio }}">
                                </div>

                                <div class="col-md-2 col-sm-12">
                                    <label for="data_fim" class="form-label fw-bold">Data Fim</label>
                                    <input type="date" class="form-control" id="data_fim" name="data_fim"
                                        value="{{ $data_fim }}">
                                </div>

                                <div class="col-md-2 col-sm-12">
                                    <label for="situation" class="form-label fw-bold">Situação</label>
                                    <select class="form-select" id="situation" name="situation">
                                        <option value="">Todos</option>
                                        <option value="paid" @selected($situation == 'paid')>Pago</option>
                                        <option value="pending" @selected($situation == 'pending')>Pendente</option>
                                        <option value="canceled" @selected($situation == 'canceled')>Cancelado</option>
                                    </select>
                                </div>

                                <div class="col-md-2 col-sm-12">
                                    <label for="type" class="form-label fw-bold">Tipo</label>
                                    <select class="form-select" id="type" name="type">
                                        <option value="">Todos</option>
                                        <option value="entrada" @selected($type == 'entrada')>Entrada</option>
                                        <option value="saida" @selected($type == 'saida')>Saída</option>
                                    </select>
                                </div>

                                @if ($perPage)
                                    <input type="hidden" name="perPage" id="perPage" value="{{ $perPage }}">
                                @endif

                                <div class="d-flex col-md-2 col-sm-12 mt-3 pt-3">
                                    <button type="submit" class="btn btn-primary me-1">Buscar</button>
                                    <a href="{{ route('home') }}"
                                        class="btn btn-outline-secondary mt-xl-0 mt-lg-2 mt-md-2 ">Limpar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <div class="row mb-3">
                    <div class="col-xl-4 col-md-6 mb-3 mb-md-3 mb-xl-0">
                        <div class="card dash-stat-card h-100 ">
                            <div class="card-body pe-4 ">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="metric-label">Entradas</span>
                                    <span class="icon bg-soft-success"><i class="fa-solid fa-arrow-up"></i></span>
                                </div>
                                <h3 class="metric-value mb-1 ">{{ $money($entrada) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-3 mb-md-3 mb-xl-0">
                        <div class="card dash-stat-card h-100">
                            <div class="card-body pe-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="metric-label">Saídas</span>
                                    <span class="icon bg-soft-danger"><i class="fa-solid fa-arrow-down"></i></i></span>
                                </div>
                                <h4 class="metric-value mb-1">{{ $money($saida) }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-12 mb-0 mb-md-0 mb-xl-0">
                        <div class="card dash-stat-card h-100">
                            <div class="card-body pe-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="metric-label">Saldo</span>
                                    <span class="icon bg-soft-secondary"><i class="fa-solid fa-scale-balanced"></i></span>
                                </div>
                                <h4 class="metric-value mb-1 {{ $saldoClass }}">{{ $money($MyTotal) }}</h4>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="card shadow-sm section-card">
                    <div class="card-body">


                        <x-alert />
                        <div class="table-responsive">
                            <table class="table align-middle">
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
                                                <div class="d-flex align-items-center flex-wrap gap-1">
                                                    <span class="fw-medium">{{ $conta->name }}</span>
                                                    @if ($conta->is_installment)
                                                        <span class="badge bg-info text-white border cursor-pointer"
                                                            role="button"
                                                            onclick="carregarDetalhesParcelamento({{ $conta->id }})"
                                                            title="Clique para ver o progresso do parcelamento">
                                                            <i
                                                                class="fa-solid fa-layer-group me-1"></i>{{ $conta->installment_label }}
                                                        </span>
                                                    @endif
                                                    @if ($conta->is_repeated)
                                                        <span class="badge bg-secondary-subtle text-secondary border"
                                                            title="Lançamento repetido">
                                                            <i
                                                                class="fa-solid fa-clone me-1"></i>{{ $conta->repeat_label ?: 'Repetido' }}
                                                        </span>
                                                    @endif
                                                    @if ($conta->is_recurring)
                                                        <span class="badge bg-primary-subtle text-primary border"
                                                            title="Regra de recorrência ativa">
                                                            <i class="fa-solid fa-arrows-rotate me-1"></i>Recorrente
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <span
                                                    class="fw-semibold">{{ 'R$' . number_format($conta->value, 2, ',', '.') }}</span>
                                            </td>
                                            <td class="align-middle">
                                                @if ($conta->maturity < now() && $conta->situation != 'paid')
                                                    <s
                                                        class="text-danger fw-medium">{{ date('d/m/Y', strtotime($conta->maturity)) }}</s>
                                                @else
                                                    {{ date('d/m/Y', strtotime($conta->maturity)) }}
                                                @endif

                                            </td>
                                            <td class="align-middle">
                                                <a href="{{ route('situacao.alterar', ['id' => $conta->id]) }}">
                                                    {!! '<span class="status-pill text-bg-' . $conta->status . ' ">' . $conta->situation_name . '</span>' !!}
                                                </a>
                                            </td>
                                            <td class="align-middle">{{ $conta->category->name }}</td>
                                            <td class="align-middle"
                                                style="color: {{ ucfirst($conta->type) == 'Entrada' ? 'green' : 'red' }};">
                                                @if (ucfirst($conta->type) == 'Entrada')
                                                    <i class="fas fa-arrow-up"></i> Entrada
                                                @else
                                                    <i class="fas fa-arrow-down"></i> Saída
                                                @endif
                                            </td>
                                            <td class="d-none d-md-flex justify-content-center collapse">
                                                <a href="{{ route('contas.show', ['id' => $conta->id]) }}"
                                                    class="btn btn-primary btn-sm me-1">Visualizar</a>
                                                <a href="{{ route('contas.edit', ['id' => $conta->id]) }}"
                                                    class="btn btn-outline-secondary btn-sm me-1">Editar</a>
                                                <button type="button" class="btn btn-outline-info btn-sm me-1 btn-repeat"
                                                    data-id="{{ $conta->id }}" data-name="{{ $conta->name }}"
                                                    data-value="{{ number_format($conta->value, 2, ',', '.') }}"
                                                    data-maturity="{{ date('d/m/Y', strtotime($conta->maturity)) }}"
                                                    data-raw-maturity="{{ date('Y-m-d', strtotime($conta->maturity)) }}"
                                                    data-bs-toggle="modal" data-bs-target="#modalRepetirLancamento"
                                                    title="Repetir lançamento">
                                                    <i class="fa-solid fa-clone me-1"></i>Repetir
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm me-1"
                                                    onclick="abrirModalConfigParcelamento({{ $conta->id }}, '{{ addslashes($conta->name) }}', {{ $conta->is_installment ? 'true' : 'false' }}, {{ $conta->installment_number ?? 1 }}, {{ $conta->installments_total ?? 1 }})"
                                                    title="Configurar parcelamento">
                                                    <i class="fa-solid fa-layer-group me-1"></i>Parcela
                                                </button>
                                                @if ($conta->is_recurring)
                                                    <form
                                                        action="{{ route('contas.cancelar-recorrencia', ['id' => $conta->id]) }}"
                                                        method="post" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-warning btn-sm me-1"
                                                            onclick="return confirm('Cancelar regra de recorrência desta conta? Os lançamentos existentes serão mantidos.')">Cancelar
                                                            recorrência</button>
                                                    </form>
                                                @endif
                                                @if ($conta->is_repeated || $conta->is_installment)
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        onclick="abrirModalExclusaoSequencia({{ $conta->id }}, '{{ addslashes($conta->name) }}', '{{ $conta->is_installment ? $conta->installment_label : $conta->repeat_label ?? 'Repetido' }}', {{ $conta->is_recurring ? 'true' : 'false' }}, {{ $conta->is_installment ? 'true' : 'false' }})">Apagar</button>
                                                @else
                                                    <form id="formExcluir{{ $conta->id }}"
                                                        action="{{ route('contas.destroy', ['id' => $conta->id]) }}"
                                                        method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                            onclick="confirmarExclusao(event, {{ $conta->id }})">Apagar</button>
                                                    </form>
                                                @endif

                                            </td>
                                            <td class="d-flex d-md-none justify-content-center">
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        Ações
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('contas.show', ['id' => $conta->id]) }}">Visualizar</a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('contas.edit', ['id' => $conta->id]) }}">Editar</a>
                                                        </li>
                                                        <li>
                                                            <button type="button"
                                                                class="dropdown-item text-info btn-repeat"
                                                                data-id="{{ $conta->id }}"
                                                                data-name="{{ $conta->name }}"
                                                                data-value="{{ number_format($conta->value, 2, ',', '.') }}"
                                                                data-maturity="{{ date('d/m/Y', strtotime($conta->maturity)) }}"
                                                                data-raw-maturity="{{ date('Y-m-d', strtotime($conta->maturity)) }}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalRepetirLancamento">
                                                                <i class="fa-solid fa-clone me-1"></i>Repetir lançamento
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-secondary"
                                                                href="javascript:void(0)"
                                                                onclick="abrirModalConfigParcelamento({{ $conta->id }}, '{{ addslashes($conta->name) }}', {{ $conta->is_installment ? 'true' : 'false' }}, {{ $conta->installment_number ?? 1 }}, {{ $conta->installments_total ?? 1 }})">
                                                                <i class="fa-solid fa-layer-group me-1"></i>Configurar
                                                                parcelamento
                                                            </a>
                                                        </li>
                                                        @if ($conta->is_recurring)
                                                            <li>
                                                                <form
                                                                    action="{{ route('contas.cancelar-recorrencia', ['id' => $conta->id]) }}"
                                                                    method="post">
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="dropdown-item text-warning"
                                                                        onclick="return confirm('Cancelar regra de recorrência desta conta? Os lançamentos existentes serão mantidos.')">Cancelar
                                                                        recorrência</button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            @if ($conta->is_repeated || $conta->is_installment)
                                                                <a class="dropdown-item text-danger"
                                                                    href="javascript:void(0)"
                                                                    onclick="abrirModalExclusaoSequencia({{ $conta->id }}, '{{ addslashes($conta->name) }}', '{{ $conta->is_installment ? $conta->installment_label : $conta->repeat_label ?? 'Repetido' }}', {{ $conta->is_recurring ? 'true' : 'false' }}, {{ $conta->is_installment ? 'true' : 'false' }})">Apagar</a>
                                                            @else
                                                                <form id="formExcluir{{ $conta->id }}"
                                                                    action="{{ route('contas.destroy', ['id' => $conta->id]) }}"
                                                                    method="post">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <a class="dropdown-item" type="submit"
                                                                        onclick="confirmarExclusao(event, {{ $conta->id }})">Apagar</a>
                                                                </form>
                                                            @endif
                                                        </li>
                                                    </ul>
                                                </div>

                                            </td>
                                        </tr>
                                    @endforeach


                                    @if (count($contas) == 0)
                                        <tr>

                                            <td colspan="8">
                                                <div class="empty-state">
                                                    <div class="icon"><i class="fa-solid fa-wallet"></i></div>
                                                    <div class="fw-semibold text-body">Nenhuma conta encontrada</div>
                                                    <div>Use os filtros acima ou crie um novo lançamento para começar.</div>
                                                </div>
                                            </td>

                                        </tr>
                                    @endif


                                </tbody>

                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-5 flex-wrap mt-3">
                            <form id="filterForm" action="{{ route('home') }}" class="d-flex align-items-center gap-2">
                                <label for="perPage" class="text-nowrap">Linhas por página:</label>
                                <select class="form-select form-select-sm" id="perPage" name="perPage"
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
                            <div style="min-width: 25%;">
                                {{ $contas->onEachSide(0)->links() }}
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
                    <form id="formRepetirLancamento" method="POST" action="">
                        @csrf
                        <input type="hidden" name="operation_token" id="repeat_operation_token" value="">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalRepetirLancamentoLabel">
                                <i class="fa-solid fa-clone text-info me-2"></i>Repetir lançamento
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-light border mb-3">
                                <div class="small text-muted">Lançamento base:</div>
                                <div class="fw-bold" id="modalRepeatContaName"></div>
                                <div class="small text-muted mt-1">
                                    Valor: <strong id="modalRepeatContaValue"></strong> | Vencimento: <strong
                                        id="modalRepeatContaMaturity"></strong>
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
                                    <label for="repeat_frequency" class="form-label">Frequência <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="repeat_frequency" name="frequency" required>
                                        <option value="monthly" selected>Mensal</option>
                                        <option value="weekly">Semanal</option>
                                        <option value="biweekly">Quinzenal (a cada 15 dias)</option>
                                        <option value="yearly">Anual</option>
                                        <option value="custom">A cada X dias</option>
                                    </select>
                                </div>

                                <div class="col-12 d-none" id="repeat_custom_interval_wrapper">
                                    <label for="repeat_interval" class="form-label">Intervalo personalizado em dias <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">A cada</span>
                                        <input type="number" min="1" max="365" class="form-control"
                                            id="repeat_interval" name="interval">
                                        <span class="input-group-text">dias</span>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="repeat_start_date" class="form-label">Data da 1ª repetição</label>
                                    <input type="date" class="form-control" id="repeat_start_date" name="start_date">
                                    <div class="form-text">Deixe em branco para calcular a partir do vencimento.</div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="repeat_custom_value" class="form-label">Valor das cópias
                                        (opcional)</label>
                                    <input type="text" class="form-control" id="repeat_custom_value"
                                        name="custom_value" placeholder="Manter valor original">
                                    <div class="form-text">Deixe em branco para manter o mesmo valor.</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" id="btnSubmitRepetir">
                                <i class="fa-solid fa-check me-1"></i>Repetir lançamento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Excluir Sequência -->
        <div class="modal fade" id="modalExcluirSequencia" tabindex="-1" aria-labelledby="modalExcluirSequenciaLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow">
                    <form id="formExcluirSequencia" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalExcluirSequenciaLabel">
                                <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Excluir lançamento
                                repetido
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                Este lançamento pertence a uma sequência de repetição (<strong
                                    id="modalExcluirSequenciaLabelBadge"></strong>).
                                Como deseja prosseguir com a exclusão?
                            </div>

                            <div class="d-flex flex-column gap-2 border p-3 rounded-2 bg-light-subtle">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delete_scope"
                                        id="delete_scope_only" value="only_this" checked>
                                    <label class="form-check-label" for="delete_scope_only">
                                        <strong>Excluir somente este lançamento</strong>
                                        <div class="small text-muted">Apenas este registro será apagado. Os demais
                                            continuam inalterados.</div>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delete_scope"
                                        id="delete_scope_this_and_next" value="this_and_next">
                                    <label class="form-check-label" for="delete_scope_this_and_next">
                                        <strong>Excluir este e os lançamentos posteriores</strong>
                                        <div class="small text-muted">Apaga este lançamento e todas as ocorrências futuras
                                            desta mesma sequência.</div>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="delete_scope"
                                        id="delete_scope_all" value="all_sequence">
                                    <label class="form-check-label" for="delete_scope_all">
                                        <strong>Excluir toda a sequência</strong>
                                        <div class="small text-muted">Apaga todos os lançamentos gerados nesta repetição.
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="form-check mt-3 d-none" id="cancelRecurrenceCheckboxWrapper">
                                <input class="form-check-input" type="checkbox" name="cancel_recurrence"
                                    id="cancel_recurrence" value="1">
                                <label class="form-check-label text-warning-emphasis" for="cancel_recurrence">
                                    Cancelar também a regra de recorrência automática futura
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fa-solid fa-trash me-1"></i>Confirmar exclusão
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Importar Excel / CSV -->
        <div class="modal fade" id="modalImportarExcel" tabindex="-1" aria-labelledby="modalImportarExcelLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content shadow">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalImportarExcelLabel">
                            <i class="fa-solid fa-file-excel text-success me-2"></i>Importar Lançamentos via Excel / CSV
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info py-2 small d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fa-solid fa-circle-info me-1"></i>
                                Formatos aceitos: <strong>.xlsx</strong> e <strong>.csv</strong>. Use a coluna
                                <em>Situação</em> (pago/pendente), <em>Tipo</em> (entrada/saida), <em>Valor</em> (R$
                                1.250,50) e <em>Vencimento</em> (dd/mm/aaaa).
                            </div>
                            <a href="{{ route('contas.importar.modelo') }}"
                                class="btn btn-sm btn-outline-primary text-nowrap ms-2">
                                <i class="fa-solid fa-download me-1"></i>Baixar Modelo
                            </a>
                        </div>

                        <!-- Formulário de Upload e Análise -->
                        <div class="card p-3 bg-light-subtle border mb-3">
                            <label for="excel_file_input" class="form-label fw-bold small">Selecione o arquivo de
                                planilha</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="excel_file_input"
                                    accept=".xlsx,.csv,.txt">
                                <button type="button" class="btn btn-primary" id="btnAnalisarExcel">
                                    <i class="fa-solid fa-magnifying-glass me-1"></i> Analisar Arquivo
                                </button>
                            </div>
                            <div class="text-danger small mt-1 d-none" id="excelFileError"></div>
                        </div>

                        <!-- Spinner de Carregamento -->
                        <div class="text-center py-4 d-none" id="excelLoadingSpinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Processando...</span>
                            </div>
                            <div class="mt-2 text-muted small">Analisando e validando dados do arquivo...</div>
                        </div>

                        <!-- Resultado da Análise -->
                        <div class="d-none" id="excelPreviewContainer">
                            <div class="row g-2 mb-3 text-center">
                                <div class="col-3">
                                    <div class="border rounded p-2 bg-light">
                                        <div class="small text-muted">Total Linhas</div>
                                        <h5 class="mb-0 fw-bold" id="previewTotalRows">0</h5>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2 bg-success-subtle text-success">
                                        <div class="small">Válidas</div>
                                        <h5 class="mb-0 fw-bold" id="previewValidRows">0</h5>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2 bg-warning-subtle text-warning-emphasis">
                                        <div class="small">Avisos</div>
                                        <h5 class="mb-0 fw-bold" id="previewWarnings">0</h5>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2 bg-danger-subtle text-danger">
                                        <div class="small">Erros</div>
                                        <h5 class="mb-0 fw-bold" id="previewErrors">0</h5>
                                    </div>
                                </div>
                            </div>

                            <!-- Erros Detalhados se houver -->
                            <div class="alert alert-danger d-none py-2 small" id="excelErrorsBox">
                                <h6 class="alert-heading small fw-bold mb-1"><i
                                        class="fa-solid fa-circle-exclamation me-1"></i>Erros encontrados (estas linhas
                                    serão ignoradas):</h6>
                                <ul class="mb-0 ps-3" id="excelErrorsList"></ul>
                            </div>

                            <!-- Avisos / Duplicatas se houver -->
                            <div class="alert alert-warning d-none py-2 small" id="excelWarningsBox">
                                <h6 class="alert-heading small fw-bold mb-1"><i
                                        class="fa-solid fa-triangle-exclamation me-1"></i>Avisos de duplicidade potencial:
                                </h6>
                                <ul class="mb-0 ps-3" id="excelWarningsList"></ul>
                            </div>

                            <!-- Prévia dos lançamentos válidos -->
                            <h6 class="fw-bold small mb-2"><i class="fa-solid fa-table-list me-1"></i>Pré-visualização dos
                                Lançamentos (primeiras 10 linhas):</h6>
                            <div class="table-responsive border rounded mb-3" style="max-height: 250px;">
                                <table class="table table-sm table-striped mb-0 small">
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
                                <div class="form-check form-switch mb-3 p-3 bg-light rounded border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch"
                                        id="auto_create_categories" name="auto_create_categories" value="1" checked>
                                    <label class="form-check-label fw-semibold" for="auto_create_categories">
                                        Criar automaticamente categorias novas identificadas na planilha
                                    </label>
                                    <div class="form-text small text-muted">Se desativado, contas com categorias novas
                                        serão importadas sem categoria ou com a padrão.</div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-success" id="btnConfirmarImportacao">
                                        <i class="fa-solid fa-check me-1"></i> Confirmar Importação
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Configurar Parcelamento Estruturado -->
        <div class="modal fade" id="modalConfigurarParcelamento" tabindex="-1"
            aria-labelledby="modalConfigurarParcelamentoLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow">
                    <form id="formConfigurarParcelamento" method="POST" action="">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalConfigurarParcelamentoLabel">
                                <i class="fa-solid fa-layer-group text-primary me-2"></i>Configurar Parcelamento
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-light border mb-3">
                                <div class="small text-muted">Lançamento selecionado:</div>
                                <div class="fw-bold" id="configParcelaContaName"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Faz parte de um parcelamento?</label>
                                <select class="form-select" id="config_is_installment" name="is_installment" required>
                                    <option value="1">Sim, é um lançamento parcelado</option>
                                    <option value="0">Não (remover parcelamento estruturado)</option>
                                </select>
                            </div>

                            <div id="configParcelamentoFields">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label for="config_installment_number" class="form-label fw-bold small">Parcela
                                            atual <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="config_installment_number"
                                            name="installment_number" min="1" max="999" value="1">
                                    </div>
                                    <div class="col-6">
                                        <label for="config_installments_total" class="form-label fw-bold small">Total de
                                            parcelas <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="config_installments_total"
                                            name="installments_total" min="1" max="999" value="12">
                                    </div>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="config_link_related"
                                        name="link_related" value="1" checked>
                                    <label class="form-check-label small" for="config_link_related">
                                        Vincular automaticamente outros lançamentos antigos que parecem pertencer a este
                                        mesmo parcelamento (mesmo nome base e padrão "X/Y").
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

        <!-- Modal Detalhes do Parcelamento Estruturado -->
        <div class="modal fade" id="modalDetalhesParcelamento" tabindex="-1"
            aria-labelledby="modalDetalhesParcelamentoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content shadow">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-0" id="detalhesParcelaTitle">
                                <i class="fa-solid fa-layer-group text-primary me-2"></i>Progresso do Parcelamento
                            </h5>
                            <small class="text-muted" id="detalhesParcelaSubtitle"></small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Barra de progresso -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1 small fw-semibold">
                                <span id="detalhesProgressoLabel">Progresso: 0 de 0 pagas</span>
                                <span id="detalhesProgressoPercent" class="text-primary fw-bold">0%</span>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar progress-bar-striped bg-success" id="detalhesProgressBar"
                                    role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                    aria-valuemax="100"></div>
                            </div>
                        </div>

                        <!-- Cards com métricas consolidadas -->
                        <div class="row g-2 mb-4 text-center">
                            <div class="col-4">
                                <div class="border rounded p-2 bg-light">
                                    <div class="small text-muted">Valor Total</div>
                                    <h6 class="mb-0 fw-bold" id="detalhesValorTotal">R$ 0,00</h6>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 bg-success-subtle text-success">
                                    <div class="small">Total Pago</div>
                                    <h6 class="mb-0 fw-bold" id="detalhesValorPago">R$ 0,00</h6>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded p-2 bg-danger-subtle text-danger">
                                    <div class="small">Restante</div>
                                    <h6 class="mb-0 fw-bold" id="detalhesValorRestante">R$ 0,00</h6>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-secondary" id="badgeTotalParcelas">Total: 0</span>
                            <span class="badge bg-success" id="badgePagas">Pagas: 0</span>
                            <span class="badge bg-warning text-dark" id="badgePendentes">Pendentes: 0</span>
                            <span class="badge bg-danger" id="badgeVencidas">Vencidas: 0</span>
                        </div>

                        <!-- Tabela de todas as parcelas -->
                        <h6 class="fw-bold small mb-2"><i class="fa-solid fa-list-ol me-1"></i>Parcelas cadastradas:</h6>
                        <div class="table-responsive border rounded">
                            <table class="table table-sm table-hover mb-0 align-middle small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Parcela</th>
                                        <th>Vencimento</th>
                                        <th>Valor</th>
                                        <th>Situação</th>
                                    </tr>
                                </thead>
                                <tbody id="detalhesParcelasTbody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection
