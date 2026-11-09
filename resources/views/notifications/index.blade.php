@extends('layouts.app')

@section('content')
    <div class="w-full py-3">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-semibold mb-1"><i class="fa-solid fa-bell text-warning-700 dark:text-warning-400 me-2"></i>Central de Lembretes Internos</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-0">Acompanhe vencimentos, contas próximas e atrasadas em um só lugar.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="action-button action-button-outline-secondary" data-ui-modal-toggle data-ui-modal-target="#settingsModal">
                    <i class="fa-solid fa-gear me-1"></i> Configurações
                </button>
                @if ($summary['unread'] > 0)
                    <form action="{{ route('lembretes.read-all') }}" method="POST">
                        @csrf
                        <button type="submit" class="action-button action-button-outline-primary">
                            <i class="fa-solid fa-check-double me-1"></i> Marcar todas como lidas
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="mb-4 grid grid-cols-2 gap-3 md:grid-cols-4">
            <div class="min-w-0">
                <div class="h-full rounded-xl border border-l-4 border-gray-200 border-l-brand-500 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <span class="text-gray-500 dark:text-gray-400 text-sm font-semibold">Não Lidas</span>
                    <h3 class="mt-2 mb-0 font-bold text-brand-600 dark:text-brand-400">{{ $summary['unread'] }}</h3>
                </div>
            </div>
            <div class="min-w-0">
                <div class="h-full rounded-xl border border-l-4 border-gray-200 border-l-error-500 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <span class="text-gray-500 dark:text-gray-400 text-sm font-semibold">Vencem Hoje</span>
                    <h3 class="mt-2 mb-0 font-bold text-error-700 dark:text-error-400">{{ $summary['due_today'] }}</h3>
                </div>
            </div>
            <div class="min-w-0">
                <div class="h-full rounded-xl border border-l-4 border-gray-200 border-l-warning-500 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <span class="text-gray-500 dark:text-gray-400 text-sm font-semibold">Próximos do Vencimento</span>
                    <h3 class="mt-2 mb-0 font-bold text-warning-700 dark:text-warning-400">{{ $summary['upcoming'] }}</h3>
                </div>
            </div>
            <div class="min-w-0">
                <div class="h-full rounded-xl border border-l-4 border-gray-200 border-l-gray-700 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <span class="text-gray-500 dark:text-gray-400 text-sm font-semibold">Vencidos</span>
                    <h3 class="mt-2 mb-0 font-bold text-error-700 dark:text-error-400">{{ $summary['overdue'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Filter Nav -->
        <div class="mb-4 rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="p-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-semibold text-gray-500 dark:text-gray-400 me-2 text-sm">Filtrar:</span>
                    <a href="{{ route('lembretes.index', ['filter' => 'all']) }}"
                        class="action-button action-button-sm {{ $filter === 'all' ? 'action-button-primary' : 'action-button-outline-secondary' }}">
                        Todos
                    </a>
                    <a href="{{ route('lembretes.index', ['filter' => 'unread']) }}"
                        class="action-button action-button-sm {{ $filter === 'unread' ? 'action-button-primary' : 'action-button-outline-secondary' }}">
                        Não lidas ({{ $summary['unread'] }})
                    </a>
                    <a href="{{ route('lembretes.index', ['filter' => 'due_today']) }}"
                        class="action-button action-button-sm {{ $filter === 'due_today' ? 'action-button-danger' : 'action-button-outline-danger' }}">
                        Hoje ({{ $summary['due_today'] }})
                    </a>
                    <a href="{{ route('lembretes.index', ['filter' => 'upcoming']) }}"
                        class="action-button action-button-sm {{ $filter === 'upcoming' ? 'action-button-warning' : 'action-button-outline-warning' }}">
                        Próximos ({{ $summary['upcoming'] }})
                    </a>
                    <a href="{{ route('lembretes.index', ['filter' => 'overdue']) }}"
                        class="action-button action-button-sm {{ $filter === 'overdue' ? 'action-button-dark' : 'action-button-outline-dark' }}">
                        Vencidos ({{ $summary['overdue'] }})
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="p-0">
                @if ($notifications->isEmpty())
                    <div class="text-center py-5">
                        <i class="fa-regular fa-bell-slash fa-3x text-gray-500 dark:text-gray-400 mb-3"></i>
                        <h5 class="text-gray-500 dark:text-gray-400">Nenhum lembrete encontrado</h5>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Todos os seus compromissos financeiros estão em dia ou os filtros
                            selecionados não contêm avisos.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($notifications as $notification)
                            <div
                                class="flex flex-col items-start justify-between gap-3 border-l-4 p-3 md:flex-row md:items-center {{ is_null($notification->read_at) ? 'bg-gray-50 font-semibold border-l-blue-light-500 dark:bg-gray-800' : 'border-l-transparent' }}">
                                <div class="flex items-start gap-3">
                                    <div>
                                        @if ($notification->type === 'due_today')
                                            <span class="status-badge bg-error-600 text-white rounded-full"><i
                                                    class="fa-solid fa-triangle-exclamation"></i> Hoje</span>
                                        @elseif ($notification->type === 'upcoming')
                                            <span class="status-badge bg-warning-500 text-gray-900 dark:text-gray-100 rounded-full"><i
                                                    class="fa-regular fa-clock"></i> Próximo</span>
                                        @else
                                            <span class="status-badge bg-gray-900 text-white rounded-full"><i
                                                    class="fa-solid fa-circle-exclamation"></i> Vencido</span>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-1 text-gray-900 dark:text-gray-100">{{ $notification->title }}</h6>
                                        <p class="mb-1 text-gray-600 dark:text-gray-300 text-sm font-normal">{{ $notification->message }}</p>
                                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                            <span><i class="fa-regular fa-calendar me-1"></i>Vencimento:
                                                {{ $notification->reference_date ? $notification->reference_date->format('d/m/Y') : '-' }}</span>
                                            @if ($notification->read_at)
                                                <span class="text-success-700 dark:text-success-400"><i class="fa-solid fa-check me-1"></i>Lido em
                                                    {{ $notification->read_at->format('d/m/Y H:i') }}</span>
                                            @else
                                                <span class="text-brand-600 dark:text-brand-400"><i class="fa-solid fa-circle me-1"
                                                        style="font-size: 8px;"></i>Não lido</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="ml-auto flex items-center gap-2 md:ml-0">
                                    @if ($notification->conta_id)
                                        <a href="{{ route('contas.show', $notification->conta_id) }}"
                                            class="action-button action-button-sm action-button-outline-info" title="Ver Lançamento">
                                            <i class="fa-solid fa-eye me-1"></i> Ver Conta
                                        </a>
                                    @endif
                                    @if (is_null($notification->read_at))
                                        <form action="{{ route('lembretes.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="action-button action-button-sm action-button-outline-success"
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
                <div class="border-t border-gray-200 bg-transparent p-3 dark:border-gray-700">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Configurações de Lembrete -->
    <div class="ui-modal hidden" data-ui-modal id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
        <div class="ui-modal-dialog">
            <div class="ui-modal-content">
                <form action="{{ route('lembretes.settings') }}" method="POST">
                    @csrf
                    <div class="ui-modal-header">
                        <h5 class="ui-modal-title" id="settingsModalLabel"><i class="fa-solid fa-gear me-2"></i>Configurações
                            de Lembretes</h5>
                        <button type="button" class="ui-modal-close" data-ui-modal-dismiss aria-label="Fechar"></button>
                    </div>
                    <div class="ui-modal-body">
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Personalize os avisos exibidos internamente no sistema.</p>

                        <div class="mb-3 flex items-start gap-2">
                            <input class="mt-1 size-4 shrink-0 accent-brand-500" type="checkbox" role="switch" id="reminder_upcoming"
                                name="reminder_upcoming" value="1"
                                {{ old('reminder_upcoming', $user->reminder_upcoming ?? true) ? 'checked' : '' }}>
                            <label class="text-sm text-gray-700 dark:text-gray-300" for="reminder_upcoming">Alertar lançamentos próximos do
                                vencimento</label>
                        </div>

                        <div class="mb-3">
                            <label for="reminder_days_before" class="field-label text-sm font-semibold">Quantos dias de
                                antecedência para alertar?</label>
                            <input type="number" class="field-control" id="reminder_days_before"
                                name="reminder_days_before" min="1" max="30"
                                value="{{ old('reminder_days_before', $user->reminder_days_before ?? 3) }}">
                            <div class="field-help">Ex: 3 dias antes do vencimento do lançamento.</div>
                        </div>

                        <div class="mb-3 flex items-start gap-2">
                            <input class="mt-1 size-4 shrink-0 accent-brand-500" type="checkbox" role="switch" id="reminder_overdue"
                                name="reminder_overdue" value="1"
                                {{ old('reminder_overdue', $user->reminder_overdue ?? true) ? 'checked' : '' }}>
                            <label class="text-sm text-gray-700 dark:text-gray-300" for="reminder_overdue">Alertar lançamentos já vencidos</label>
                        </div>
                    </div>
                    <div class="ui-modal-footer">
                        <button type="button" class="action-button action-button-secondary" data-ui-modal-dismiss>Cancelar</button>
                        <button type="submit" class="action-button action-button-primary">Salvar Preferências</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
