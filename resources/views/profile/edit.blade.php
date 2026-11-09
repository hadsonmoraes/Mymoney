@extends('layouts.app')

@section('title', 'Editar perfil')

@section('content')
    <div class="w-full">
        <div class="flex flex-wrap justify-center gap-3">
            <div class="w-full">
                <div class="page-hero mb-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <div class="metric-label mb-2">Conta</div>
                            <h1 class="page-title mb-1">Editar perfil</h1>
                            <p class="page-subtitle">Atualize seus dados pessoais e senha com uma experiência mais limpa.</p>
                        </div>
                        <a href="{{ route('home') }}" class="action-button action-button-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="section-card form-surface w-full rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="form-card-body">
                        <x-alert />

                        <form action="{{ route('profile.update', ['id' => $profile->id]) }}" method="post"
                            class="form-grid">
                            @csrf
                            @method('PUT')

                            <div class="form-section">
                                <div class="form-section-title">Dados da conta</div>
                                <div class="form-section-desc">Atualize nome e e-mail para manter seu acesso em dia.</div>

                                <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                                    <div>
                                        <label for="name" class="field-label">Nome</label>
                                        <input type="text" class="field-control" id="name" name="name" required
                                            value="{{ $profile->name }}">
                                    </div>

                                    <div>
                                        <label for="email" class="field-label">Email</label>
                                        <input type="email" class="field-control" id="email" name="email" required
                                            value="{{ $profile->email }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="form-section-title">Senha</div>
                                <div class="form-section-desc">Preencha apenas se quiser trocar sua senha atual.</div>

                                <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                                    <div>
                                        <label for="password" class="field-label">Nova senha</label>
                                        <div class="flex">
                                            <input type="password"
                                                class="field-control min-w-0 flex-1 rounded-r-none" id="password" name="password"
                                                placeholder="Deixe em branco para manter a senha atual">
                                            <button type="button"
                                                class="inline-flex shrink-0 items-center justify-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-500 hover:bg-gray-100 focus-visible:ring-2 focus-visible:ring-brand-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700"
                                                aria-label="Mostrar senha"
                                                aria-controls="password" onclick="togglePassword('password', this)">
                                                <x-svg.eye class="size-5" data-password-icon="visible" />
                                                <x-svg.eye-close class="hidden size-5" data-password-icon="hidden" />
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="password-confirm" class="field-label">Confirmar senha</label>
                                        <div class="flex">
                                            <input id="password-confirm" type="password"
                                                class="field-control min-w-0 flex-1 rounded-r-none"
                                                name="password_confirmation" autocomplete="new-password"
                                                placeholder="Repita a senha nova">
                                            <button type="button"
                                                class="inline-flex shrink-0 items-center justify-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3 text-gray-500 hover:bg-gray-100 focus-visible:ring-2 focus-visible:ring-brand-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700"
                                                aria-label="Mostrar senha"
                                                aria-controls="password-confirm"
                                                onclick="togglePassword('password-confirm', this)">
                                                <x-svg.eye class="size-5" data-password-icon="visible" />
                                                <x-svg.eye-close class="hidden size-5" data-password-icon="hidden" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="action-button action-button-primary px-4">Salvar alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
