@extends('layouts.app')

@section('title', 'Cadastrar')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        Editar categoria

                        <a href="{{ route('category.index') }}" class="btn btn-secondary">Voltar</a>

                    </div>

                    <div class="card-body">
                        <x-alert />

                        <form action="{{ route('category.update', ['id' => $categorys->id]) }}" method="post">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-8 col-sm-12 mb-3">
                                    <label for="name" class="form-label">Nome</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ $categorys->name }}">
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
