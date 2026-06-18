@extends('layouts.app')

@section('title', 'Editar perfil')

@section('content')
    <div class="container-fluid p-4">
        <div class="row justify-content-center g-3">
            <div class="col-12">
                <div class="page-hero mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div>
                            <div class="metric-label mb-2">Conta</div>
                            <h1 class="page-title mb-1">Editar perfil</h1>
                            <p class="page-subtitle">Atualize seus dados pessoais e senha com uma experiência mais limpa.</p>
                        </div>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card section-card shadow-sm form-surface w-100">
                    <div class="card-body form-card-body">
                        <x-alert />

                        <form action="{{ route('profile.update', ['id' => $profile->id]) }}" method="post" class="form-grid">
                            @csrf
                            @method('PUT')

                            <div class="form-section">
                                <div class="form-section-title">Dados da conta</div>
                                <div class="form-section-desc">Atualize nome e e-mail para manter seu acesso em dia.</div>

                                <div class="row g-3">
                                    <div class="col-12 col-lg-6">
                                        <label for="name" class="form-label">Nome</label>
                                        <input type="text" class="form-control" id="name" name="name" required
                                            value="{{ $profile->name }}">
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required
                                            value="{{ $profile->email }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">Senha</div>
                                <div class="form-section-desc">Preencha apenas se quiser trocar sua senha atual.</div>

                                <div class="row g-3">
                                    <div class="col-12 col-lg-6">
                                        <label for="password" class="form-label">Nova senha</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" name="password"
                                                placeholder="Deixe em branco para manter a senha atual">
                                            <span class="input-group-text" onclick="togglePassword('password', this)">
                                                <i class="fa-regular fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <label for="password-confirm" class="form-label">Confirmar senha</label>
                                        <div class="input-group">
                                            <input id="password-confirm" type="password" class="form-control"
                                                name="password_confirmation" autocomplete="new-password"
                                                placeholder="Repita a senha nova">
                                            <span class="input-group-text" onclick="togglePassword('password-confirm', this)">
                                                <i class="fa-regular fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary px-4">Salvar alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
