@extends('layouts.app')

@section('title', 'Cadastrar')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Criar conta

                        <a href="{{ route('category.index') }}" class="btn btn-secondary">Voltar</a>

                    </div>

                    <div class="card-body">
                        <x-alert />

                        <form action="{{ route('category.store') }}" method="post">
                            @csrf

                            <div class="row">
                                <div class="col-md-8 col-sm-12 mb-3">
                                    <label for="name" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name') }}">
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
