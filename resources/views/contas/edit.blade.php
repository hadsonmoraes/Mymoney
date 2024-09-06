@extends('layouts.app')

@section('title', 'Editar')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Editar conta

                        <a href="{{ route('home') }}" class="btn btn-secondary">Voltar</a>

                    </div>

                    <div class="card-body">

                        <x-alert />

                        <form action="{{ route('contas.update', ['id' => $contas->id]) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 col-sm-12 mb-3">
                                    <label for="name" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="name" name="name" required
                                        value="{{ $contas->name }}">
                                </div>

                                <div class="col-md-3 col-sm-12 mb-3">
                                    <label for="value" class="form-label">Valor</label>
                                    <input type="text" class="form-control" id="value" name="value" required
                                        value="{{ number_format($contas->value, '2', ',', '.') }}">
                                </div>

                                <div class="col-md-3 col-sm-12 mb-3">
                                    <label for="maturity" class="form-label">Vencimento</label>
                                    <input type="date" class="form-control" id="maturity" name="maturity" required
                                        value="{{ $contas->maturity }}">
                                </div>

                                <div class="col-md-3 col-sm-12 mb-3">
                                    <label for="type" class="form-label">Tipo</label>
                                    <select class="form-select" id="type" name="type">
                                        <option value="" selected disabled>selecione</option>
                                        <option value="entrada" @selected($contas->type == 'entrada')>Entrada</option>
                                        <option value="saida" @selected($contas->type == 'saida')>Saída</option>
                                    </select>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-3">
                                    <label for="situation" class="form-label">Situação</label>
                                    <select class="form-select" id="situation" name="situation" required>
                                        <option value="paid" @selected($contas->situation == 'paid')>Pago</option>
                                        <option value="pending" @selected($contas->situation == 'pending')>Pendente</option>
                                        <option value="canceled" @selected($contas->situation == 'canceled')>Cancelado</option>
                                    </select>
                                </div>


                                <div class="col-md-3 col-sm-12 mb-3">
                                    <label for="category_id" class="form-label">Categoria</label>
                                    <select name="category_id" id="category_id" class="form-select select2">
                                        <option value="" selected disabled>Selecione</option>
                                        @forelse ($categorys as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id', $contas->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}</option>
                                        @empty
                                            <option value="">Nenhuma situação da conta encontrada</option>
                                        @endforelse
                                    </select>
                                </div>

                                <div class="col-md-3 col-sm-12 mb-3">
                                    <label for="image" class="form-label">Comprovante:</label>
                                    <input class="form-control" type="file" id="image" name="image">
                                </div>

                                <div class="col-md-12 col-sm-12 mb-3">
                                    <label for="note" class="form-label">Nota</label>
                                    <textarea name="note" id="note" class="form-control" rows="5">{{ $contas->note }}</textarea>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary">Editar</button>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
