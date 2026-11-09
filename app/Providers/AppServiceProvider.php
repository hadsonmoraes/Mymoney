<?php

namespace App\Providers;

use App\Models\User;
use App\Services\ReminderService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        View::composer('components.header.notification-dropdown', function (ViewInstance $view): void {
            $user = auth()->user();

            if (! $user instanceof User) {
                $view->with([
                    'reminderNotifications' => collect(),
                    'unreadReminderCount' => 0,
                ]);

                return;
            }

            app(ReminderService::class)->syncRemindersForUser($user);

            $unreadNotifications = $user->internalNotifications()->unread();

            $view->with([
                'reminderNotifications' => (clone $unreadNotifications)
                    ->latest('reference_date')
                    ->latest('id')
                    ->limit(5)
                    ->get(['id', 'type', 'title', 'message', 'reference_date']),
                'unreadReminderCount' => (clone $unreadNotifications)->count(),
            ]);
        });
    }
}
