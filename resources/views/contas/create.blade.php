@extends('layouts.app')

@section('title', 'Cadastrar')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Criar conta

                        <a href="{{ route('home') }}" class="btn btn-secondary">Voltar</a>

                    </div>

                    <div class="card-body">
                        <x-alert />

                        <form action="{{ route('contas.store') }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-8 col-sm-12 mb-3">
                                    <label for="name" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name') }}">
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="value" class="form-label">Valor</label>
                                    <input type="text" class="form-control" id="value" name="value"
                                        value="{{ old('value') }}">
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="maturity" class="form-label">Vencimento</label>
                                    <input type="date" class="form-control" id="maturity" name="maturity"
                                        value="{{ old('maturity') }}">
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="situation" class="form-label">Situação</label>
                                    <select class="form-select" id="situation" name="situation">
                                        <option value="" selected disabled>selecione</option>
                                        <option value="paid" {{ old('situation') == 'paid' ? 'selected' : '' }}>Pago
                                        </option>
                                        <option value="pending" {{ old('situation') == 'pending' ? 'selected' : '' }}>
                                            Pendente</option>
                                        <option value="canceled" {{ old('situation') == 'canceled' ? 'selected' : '' }}>
                                            Cancelado</option>
                                    </select>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="category_id" class="form-label">Categoria</label>
                                    <select name="category_id" id="category_id" class="form-select">
                                        <option value="" selected disabled>Selecione</option>
                                        @forelse ($categorys as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}</option>
                                        @empty
                                            <option value="">Nenhuma situação da conta encontrada</option>
                                        @endforelse
                                    </select>
                                </div>

                                <div class="col-md-4 col-sm-12 mb-3">
                                    <label for="image" class="form-label">Comprovante:</label>
                                    <input class="form-control" type="file" id="image" name="image">
                                </div>

                                <div class="col-md-12 col-sm-12 mb-3">
                                    <label for="note" class="form-label">Nota</label>
                                    <textarea name="note" id="note" class="form-control" rows="5">{{ old('note') }}</textarea>
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary">Cadastrar</button>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
