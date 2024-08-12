@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Filtro</span>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('dashboard') }}">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-md-4 col-sm-12">
                                    <label for="data_inicio" class="form-label fw-bold">Data Início</label>
                                    <input type="date" class="form-control" id="data_inicio" name="data_inicio"
                                        value="{{ $data_inicio }}">
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <label for="data_fim" class="form-label fw-bold">Data Fim</label>
                                    <input type="date" class="form-control" id="data_fim" name="data_fim"
                                        value="{{ $data_fim }}">
                                </div>

                                <div class="col-md-4 col-sm-12 mt-3 pt-3">
                                    <button type="submit" class="btn btn-info">Pesquisar</button>
                                    <a href="{{ route('dashboard') }}" class="btn btn-warning">Limpar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 col-sm-12 mt-3">
                        <div class="card text-bg-success mb-3 w-100 h-100">
                            <div class="card-header fw-bold">Pago</div>
                            <div class="card-body">
                                <h5 class="card-title">{{ 'Valor: R$ ' . number_format($contasPagasValor, 2, ',', '.') }}
                                </h5>
                                <h5 class="card-title">{{ 'Quantidade: ' . $contasPagasQuantidade }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-12 mt-3">
                        <div class="card text-bg-warning mb-3 w-100 h-100">
                            <div class="card-header fw-bold">Pendente</div>
                            <div class="card-body">
                                <h5 class="card-title">
                                    {{ 'Valor: R$ ' . number_format($contasPendentesValor, 2, ',', '.') }}
                                </h5>
                                <h5 class="card-title">{{ 'Quantidade: ' . $contasPendentesQuantidade }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-12 mt-3">
                        <div class="card text-bg-danger mb-3 w-100 h-100">
                            <div class="card-header fw-bold">Cancelado</div>
                            <div class="card-body">
                                <h5 class="card-title">
                                    {{ 'Valor: R$ ' . number_format($contasCanceladasValor, 2, ',', '.') }}
                                </h5>
                                <h5 class="card-title">{{ 'Quantidade: ' . $contasCanceladasQuantidade }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-12 mt-3">
                        <div class="card text-bg-primary mb-3 w-100 h-100">
                            <div class="card-header fw-bold">Total</div>
                            <div class="card-body">
                                <h5 class="card-title">{{ 'Valor: R$ ' . number_format($total, 2, ',', '.') }}
                                </h5>
                                <h5 class="card-title">{{ 'Quantidade: ' . $totalquantidade }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-12 mt-3">
                        <div class="card text-bg-info mb-3 w-100 h-100">
                            <div class="card-header fw-bold">Entrada</div>
                            <div class="card-body">
                                <h5 class="card-title">{{ 'Valor: R$ ' . number_format($contasEntradaValor, 2, ',', '.') }}
                                </h5>
                                <h5 class="card-title">{{ 'Quantidade: ' . $contasEntradaQuantidade }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-12 mt-3">
                        <div class="card text-bg-dark mb-3 w-100 h-100">
                            <div class="card-header fw-bold">Saída</div>
                            <div class="card-body">
                                <h5 class="card-title">{{ 'Valor: R$ ' . number_format($contasSaidaValor, 2, ',', '.') }}
                                </h5>
                                <h5 class="card-title">{{ 'Quantidade: ' . $contasSaidaQuantidade }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-12 mt-3">
                        <div class="card text-bg-secondary mb-3 w-100 h-100">
                            <div class="card-header fw-bold">Total Entrada/Saída </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ 'Valor: R$ ' . number_format($MyTotal, 2, ',', '.') }}
                                </h5>
                                <h5 class="card-title">{{ 'Quantidade: ' . '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
