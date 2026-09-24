@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="h3 mb-1"><i class="fa-solid fa-bell text-warning me-2"></i>Central de Lembretes Internos</h2>
                <p class="text-muted mb-0">Acompanhe vencimentos, contas próximas e atrasadas em um só lugar.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#settingsModal">
                    <i class="fa-solid fa-gear me-1"></i> Configurações
                </button>
                @if ($summary['unread'] > 0)
                    <form action="{{ route('lembretes.read-all') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fa-solid fa-check-double me-1"></i> Marcar todas como lidas
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
                    <div class="card-body">
                        <span class="text-muted small fw-semibold">Não Lidas</span>
                        <h3 class="mt-2 mb-0 fw-bold text-primary">{{ $summary['unread'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
                    <div class="card-body">
                        <span class="text-muted small fw-semibold">Vencem Hoje</span>
                        <h3 class="mt-2 mb-0 fw-bold text-danger">{{ $summary['due_today'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 border-start border-warning border-4 h-100">
                    <div class="card-body">
                        <span class="text-muted small fw-semibold">Próximos do Vencimento</span>
                        <h3 class="mt-2 mb-0 fw-bold text-warning">{{ $summary['upcoming'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 border-start border-dark border-4 h-100">
                    <div class="card-body">
                        <span class="text-muted small fw-semibold">Vencidos</span>
                        <h3 class="mt-2 mb-0 fw-bold text-danger">{{ $summary['overdue'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Nav -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body py-2">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="fw-semibold text-muted me-2 small">Filtrar:</span>
                    <a href="{{ route('lembretes.index', ['filter' => 'all']) }}"
                        class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Todos
                    </a>
                    <a href="{{ route('lembretes.index', ['filter' => 'unread']) }}"
                        class="btn btn-sm {{ $filter === 'unread' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Não lidas ({{ $summary['unread'] }})
                    </a>
                    <a href="{{ route('lembretes.index', ['filter' => 'due_today']) }}"
                        class="btn btn-sm {{ $filter === 'due_today' ? 'btn-danger' : 'btn-outline-danger' }}">
                        Hoje ({{ $summary['due_today'] }})
                    </a>
                    <a href="{{ route('lembretes.index', ['filter' => 'upcoming']) }}"
                        class="btn btn-sm {{ $filter === 'upcoming' ? 'btn-warning' : 'btn-outline-warning' }}">
                        Próximos ({{ $summary['upcoming'] }})
                    </a>
                    <a href="{{ route('lembretes.index', ['filter' => 'overdue']) }}"
                        class="btn btn-sm {{ $filter === 'overdue' ? 'btn-dark' : 'btn-outline-dark' }}">
                        Vencidos ({{ $summary['overdue'] }})
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                @if ($notifications->isEmpty())
                    <div class="text-center py-5">
                        <i class="fa-regular fa-bell-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum lembrete encontrado</h5>
                        <p class="text-muted small">Todos os seus compromissos financeiros estão em dia ou os filtros
                            selecionados não contêm avisos.</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($notifications as $notification)
                            <div
                                class="list-group-item p-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 {{ is_null($notification->read_at) ? 'bg-light-subtle fw-semibold border-start border-4 border-info' : '' }}">
                                <div class="d-flex align-items-start gap-3">
                                    <div>
                                        @if ($notification->type === 'due_today')
                                            <span class="badge bg-danger rounded-pill"><i
                                                    class="fa-solid fa-triangle-exclamation"></i> Hoje</span>
                                        @elseif ($notification->type === 'upcoming')
                                            <span class="badge bg-warning text-dark rounded-pill"><i
                                                    class="fa-regular fa-clock"></i> Próximo</span>
                                        @else
                                            <span class="badge bg-dark rounded-pill"><i
                                                    class="fa-solid fa-circle-exclamation"></i> Vencido</span>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-1 text-dark">{{ $notification->title }}</h6>
                                        <p class="mb-1 text-secondary small fw-normal">{{ $notification->message }}</p>
                                        <div class="d-flex align-items-center gap-3 small text-muted">
                                            <span><i class="fa-regular fa-calendar me-1"></i>Vencimento:
                                                {{ $notification->reference_date ? $notification->reference_date->format('d/m/Y') : '-' }}</span>
                                            @if ($notification->read_at)
                                                <span class="text-success"><i class="fa-solid fa-check me-1"></i>Lido em
                                                    {{ $notification->read_at->format('d/m/Y H:i') }}</span>
                                            @else
                                                <span class="text-primary"><i class="fa-solid fa-circle me-1"
                                                        style="font-size: 8px;"></i>Não lido</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2 ms-auto ms-md-0">
                                    @if ($notification->conta_id)
                                        <a href="{{ route('contas.show', $notification->conta_id) }}"
                                            class="btn btn-sm btn-outline-info" title="Ver Lançamento">
                                            <i class="fa-solid fa-eye me-1"></i> Ver Conta
                                        </a>
                                    @endif
                                    @if (is_null($notification->read_at))
                                        <form action="{{ route('lembretes.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success"
                                                title="Marcar como lido">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @if ($notifications->hasPages())
                <div class="card-footer bg-transparent py-3">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Configurações de Lembrete -->
    <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('lembretes.settings') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="settingsModalLabel"><i class="fa-solid fa-gear me-2"></i>Configurações
                            de Lembretes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">Personalize os avisos exibidos internamente no sistema.</p>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="reminder_upcoming"
                                name="reminder_upcoming" value="1"
                                {{ old('reminder_upcoming', $user->reminder_upcoming ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="reminder_upcoming">Alertar lançamentos próximos do
                                vencimento</label>
                        </div>

                        <div class="mb-3">
                            <label for="reminder_days_before" class="form-label small fw-semibold">Quantos dias de
                                antecedência para alertar?</label>
                            <input type="number" class="form-control" id="reminder_days_before"
                                name="reminder_days_before" min="1" max="30"
                                value="{{ old('reminder_days_before', $user->reminder_days_before ?? 3) }}">
                            <div class="form-text">Ex: 3 dias antes do vencimento do lançamento.</div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="reminder_overdue"
                                name="reminder_overdue" value="1"
                                {{ old('reminder_overdue', $user->reminder_overdue ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="reminder_overdue">Alertar lançamentos já vencidos</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Preferências</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
