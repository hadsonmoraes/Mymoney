<?php
if ($contas->situation == 'paid') {
    $situation_name = 'Pago';
} elseif ($contas->situation == 'pending') {
    $situation_name = 'Pendente';
} else {
    $situation_name = 'Cancelado';
}

?>


@extends('layouts.app')

@section('title', 'Visualizar')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Visualizar conta

                        <a href="{{ route('home') }}" class="btn btn-secondary">Voltar</a>

                    </div>

                    <div class="card-body">

                        <form action="" method="post">
                            @csrf
                            <div class="row">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="name" name="name" required
                                        value="{{ $contas->name }}" disabled>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="value" class="form-label">Valor</label>
                                    <input type="text" class="form-control" id="value" name="value" required
                                        value="{{ 'R$' . number_format($contas->value, 2, ',', '.') }}" disabled>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="maturity" class="form-label">Vencimento</label>
                                    <input type="text" class="form-control disabled" id="maturity" name="maturity"
                                        required value="{{ date('d/m/Y', strtotime($contas->maturity)) }}" disabled>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="situation" class="form-label">Situação</label>
                                    <select class="form-select" id="situation" name="situation" disabled required>
                                        <option value="{{ $contas->situation }}">{{ $situation_name }}</option>
                                    </select>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="image" class="form-label">Comprovante:</label>
                                    @if (!empty($contas->image))
                                        <a class="form-control text-decoration-none"
                                            href="{{ url('img/comprovantes' . Auth::user()->id . '/' . $contas->image) }}"
                                            target="_blank">Visualizar</a>
                                    @else
                                        <a class="form-control text-decoration-none disabled"
                                            style="background-color:#e9ecef">Sem
                                            Comprovante</a>
                                    @endif
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="cadastro" class="form-label">Cadastrado</label>
                                    <input type="text" class="form-control" id="cadastro" name="cadastro" required
                                        value="{{ date('d/m/Y', strtotime($contas->created_at)) }}" disabled>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="editado" class="form-label">Editado</label>
                                    <input type="text" class="form-control disabled" id="editado" name="editado"
                                        required value="{{ date('d/m/Y', strtotime($contas->updated_at)) }}" disabled>
                                </div>

                                <div class="col-md-12 col-sm-12 mb-3">
                                    <label for="note" class="form-label">Nota</label>
                                    <textarea name="note" id="note" class="form-control" rows="5" disabled>{{ $contas->note }}</textarea>
                                </div>


                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
