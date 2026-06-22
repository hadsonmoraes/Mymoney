@extends('layouts.app')

@section('title', 'Home')

@section('content')
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
                            <a href="{{ url('gerar-csv?' . request()->getQueryString()) }}"
                                class="btn btn-outline-secondary">
                                <i class="fa-solid fa-file-excel me-1"></i> Exportar Excel
                            </a>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-chart-line me-1"></i> Dashboard
                            </a>
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


                <div class="card shadow-sm section-card">
                    <div class="card-body">
                        <div class="page-toolbar">
                            <form id="filterForm" action="{{ route('home') }}"
                                class="d-flex align-items-center gap-2 flex-wrap">
                                {{-- <label for="perPage" class="form-label mb-0 fw-semibold">Exibir</label> --}}
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

                            <div class="tool-row">
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#filtro">
                                    <i class="fas fa-filter me-1"></i> Filtros
                                </button>
                            </div>
                        </div>

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
                                            <td class="align-middle">{{ $conta->name }}</td>
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
                                                <form id="formExcluir{{ $conta->id }}"
                                                    action="{{ route('contas.destroy', ['id' => $conta->id]) }}"
                                                    method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                        onclick="confirmarExclusao(event, {{ $conta->id }})">Apagar</button>
                                                </form>

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
                                                            <form id="formExcluir{{ $conta->id }}"
                                                                action="{{ route('contas.destroy', ['id' => $conta->id]) }}"
                                                                method="post">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a class="dropdown-item" type="submit"
                                                                    onclick="confirmarExclusao(event, {{ $conta->id }})">Apagar</a>
                                                            </form>
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
                        {{ $contas->onEachSide(0)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
