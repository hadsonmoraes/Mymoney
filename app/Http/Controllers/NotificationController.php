<?php

namespace App\Http\Controllers;

use App\Models\InternalNotification;
use App\Services\ReminderService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected ReminderService $reminderService;

    public function __construct(ReminderService $reminderService)
    {
        $this->reminderService = $reminderService;
    }

    /**
     * Exibe a central de lembretes internos.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $this->reminderService->syncRemindersForUser($user);

        $filter = $request->get('filter', 'all');

        $query = InternalNotification::with('conta')
            ->where('user_id', $user->id)
            ->latest('id');

        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'due_today') {
            $query->where('type', 'due_today');
        } elseif ($filter === 'upcoming') {
            $query->where('type', 'upcoming');
        } elseif ($filter === 'overdue') {
            $query->where('type', 'overdue');
        }

        $notifications = $query->paginate(15)->appends(['filter' => $filter]);
        $summary = $this->reminderService->getSummary($user);

        return view('notifications.index', compact('notifications', 'summary', 'filter', 'user'));
    }

    /**
     * Marca uma notificação individual como lida.
     */
    public function markAsRead($id, Request $request)
    {
        $user = auth()->user();
        $this->reminderService->markAsRead((int) $id, $user);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Lembrete marcado como lido.');
    }

    /**
     * Marca todas as notificações como lidas.
     */
    public function markAllAsRead(Request $request)
    {
        $user = auth()->user();
        $this->reminderService->markAllAsRead($user);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Todos os lembretes foram marcados como lidos.');
    }

    /**
     * Atualiza as preferências de lembrete interno do usuário.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'reminder_upcoming' => 'nullable|boolean',
            'reminder_days_before' => 'required|integer|min:1|max:30',
            'reminder_overdue' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $user->update([
            'reminder_upcoming' => $request->boolean('reminder_upcoming'),
            'reminder_days_before' => (int) $validated['reminder_days_before'],
            'reminder_overdue' => $request->boolean('reminder_overdue'),
        ]);

        return back()->with('success', 'Configurações de lembretes atualizadas com sucesso.');
    }
}
