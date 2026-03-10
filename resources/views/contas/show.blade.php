@extends('layouts.app')

@section('title', 'Visualizar')

@section('content')

    <div class="container-fluid p-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Visualizar conta
                        <a href="{{ route('home', session('filtros_contas')) }}" class="btn btn-secondary">Voltar</a>
                    </div>

                    <div class="card-body">
                        <div class="row">
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

                            <div class="col-md-12 col-sm-12 mb-3">
                                <label for="note" class="form-label">Nota:</label>
                                {!! $contas->note !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
