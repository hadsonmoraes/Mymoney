<?php

namespace App\Services;

use App\Models\Conta;
use App\Models\InternalNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReminderService
{
    /**
     * Sincroniza e gera lembretes internos para o usuário com base nos lançamentos pendentes.
     * Possui proteção contra duplicação.
     */
    public function syncRemindersForUser(User $user): void
    {
        $today = Carbon::today();
        $daysBefore = $user->reminder_days_before ?? 3;
        $maxUpcomingDate = (clone $today)->addDays($daysBefore);

        // 1. Vencendo hoje
        $dueTodayContas = Conta::where('user_id', $user->id)
            ->where('situation', '!=', 'paid')
            ->whereDate('maturity', '=', $today)
            ->get();

        foreach ($dueTodayContas as $conta) {
            $this->createNotificationIfNotExists(
                $user->id,
                $conta->id,
                'due_today',
                $conta->maturity->format('Y-m-d'),
                "Vencendo Hoje: {$conta->name}",
                "O lançamento '{$conta->name}' no valor de R$ " . number_format($conta->value, 2, ',', '.') . " vence hoje (" . $conta->maturity->format('d/m/Y') . ")."
            );
        }

        // 2. Próximo do vencimento
        if ($user->reminder_upcoming ?? true) {
            $upcomingContas = Conta::where('user_id', $user->id)
                ->where('situation', '!=', 'paid')
                ->whereDate('maturity', '>', $today)
                ->whereDate('maturity', '<=', $maxUpcomingDate)
                ->get();

            foreach ($upcomingContas as $conta) {
                $daysRemaining = (int) $today->diffInDays($conta->maturity, false);
                $dayText = $daysRemaining === 1 ? '1 dia' : "{$daysRemaining} dias";

                $this->createNotificationIfNotExists(
                    $user->id,
                    $conta->id,
                    'upcoming',
                    $conta->maturity->format('Y-m-d'),
                    "Vencendo em breve: {$conta->name}",
                    "O lançamento '{$conta->name}' no valor de R$ " . number_format($conta->value, 2, ',', '.') . " vence em {$dayText} (" . $conta->maturity->format('d/m/Y') . ")."
                );
            }
        }

        // 3. Vencidos
        if ($user->reminder_overdue ?? true) {
            $overdueContas = Conta::where('user_id', $user->id)
                ->where('situation', '!=', 'paid')
                ->whereDate('maturity', '<', $today)
                ->get();

            foreach ($overdueContas as $conta) {
                $daysOverdue = (int) $conta->maturity->diffInDays($today, false);
                $dayText = $daysOverdue === 1 ? '1 dia' : "{$daysOverdue} dias";

                $this->createNotificationIfNotExists(
                    $user->id,
                    $conta->id,
                    'overdue',
                    $conta->maturity->format('Y-m-d'),
                    "Lançamento Vencido: {$conta->name}",
                    "O lançamento '{$conta->name}' no valor de R$ " . number_format($conta->value, 2, ',', '.') . " está vencido há {$dayText} (venceu em " . $conta->maturity->format('d/m/Y') . ")."
                );
            }
        }
    }

    /**
     * Cria notificação apenas se não existir para evitar duplicidade.
     */
    protected function createNotificationIfNotExists(
        int $userId,
        int $contaId,
        string $type,
        string $referenceDate,
        string $title,
        string $message
    ): ?InternalNotification {
        $existing = InternalNotification::where('user_id', $userId)
            ->where('conta_id', $contaId)
            ->where('type', $type)
            ->whereDate('reference_date', $referenceDate)
            ->first();

        if ($existing) {
            return $existing;
        }

        return InternalNotification::create([
            'user_id' => $userId,
            'conta_id' => $contaId,
            'type' => $type,
            'reference_date' => $referenceDate,
            'title' => $title,
            'message' => $message,
            'read_at' => null,
        ]);
    }

    /**
     * Retorna estatísticas de lembretes para o usuário.
     */
    public function getSummary(User $user): array
    {
        $this->syncRemindersForUser($user);

        $unreadCount = InternalNotification::where('user_id', $user->id)
            ->unread()
            ->count();

        $dueTodayCount = InternalNotification::where('user_id', $user->id)
            ->where('type', 'due_today')
            ->unread()
            ->count();

        $upcomingCount = InternalNotification::where('user_id', $user->id)
            ->where('type', 'upcoming')
            ->unread()
            ->count();

        $overdueCount = InternalNotification::where('user_id', $user->id)
            ->where('type', 'overdue')
            ->unread()
            ->count();

        return [
            'unread' => $unreadCount,
            'due_today' => $dueTodayCount,
            'upcoming' => $upcomingCount,
            'overdue' => $overdueCount,
        ];
    }

    /**
     * Marca uma notificação como lida.
     */
    public function markAsRead(int $id, User $user): bool
    {
        $notification = InternalNotification::where('user_id', $user->id)->find($id);
        if ($notification) {
            return $notification->markAsRead();
        }
        return false;
    }

    /**
     * Marca todas as notificações do usuário como lidas.
     */
    public function markAllAsRead(User $user): int
    {
        return InternalNotification::where('user_id', $user->id)
            ->unread()
            ->update(['read_at' => now()]);
    }
}
