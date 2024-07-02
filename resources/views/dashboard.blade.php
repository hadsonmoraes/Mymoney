@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-4 col-sm-12">
                <div class="card text-bg-success mb-3 w-100 h-100">
                    <div class="card-header fw-bold">Pago</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $contasPagas }}</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                            card's content.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-12l">
                <div class="card text-bg-warning mb-3 w-100 h-100">
                    <div class="card-header fw-bold">Pendente</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $contasPendentes }}</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                            card's content.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-12">
                <div class="card text-bg-danger mb-3 w-100 h-100">
                    <div class="card-header fw-bold">Cancelado</div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $contasCanceladas }}</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                            card's content.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
