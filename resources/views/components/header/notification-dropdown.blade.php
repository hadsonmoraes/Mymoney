<div class="contents">
    <x-ui.dropdown-menu>
        <x-ui.dropdown-menu.trigger as-child>
            <x-ui.button variant="outline" size="icon-lg" class="relative"
                aria-label="Lembretes ({{ $unreadReminderCount }})">
                @if ($unreadReminderCount > 0)
                    <span
                        class="absolute -end-1 -top-1 z-10 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-error-500 px-1 text-[10px] font-semibold text-white">
                        {{ $unreadReminderCount > 99 ? '99+' : $unreadReminderCount }}
                    </span>
                @endif
                <x-svg.bell class="size-auto!" />
            </x-ui.button>
        </x-ui.dropdown-menu.trigger>

        <x-ui.dropdown-menu.content align="end" :side-offset="12"
            class="w-87.5 overflow-hidden p-3 sm:w-90.25 dark:bg-[#1E2635]">
            <div class="flex items-center justify-between pb-3">
                <div class="flex items-center gap-2">
                    <h5 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Lembretes</h5>
                    @if ($unreadReminderCount > 0)
                        <span
                            class="rounded-full bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-300">
                            {{ $unreadReminderCount }}
                        </span>
                    @endif
                </div>
                <x-ui.button variant="ghost" size="icon-xs" aria-label="Fechar lembretes" x-on:click="close()">
                    <x-svg.x class="size-5" />
                </x-ui.button>
            </div>

            <x-ui.dropdown-menu.separator class="my-0" />

            <div class="max-h-96 overflow-y-auto overflow-x-hidden custom-scrollbar">
                @forelse ($reminderNotifications as $notification)
                    <x-ui.dropdown-menu.item
                        :to="route('lembretes.index', ['filter' => $notification->type])"
                        class="items-start gap-3 rounded-lg p-3">
                        <span class="mt-1 size-2 shrink-0 rounded-full bg-brand-500"></span>
                        <span class="min-w-0 flex-1 text-left">
                            <span class="mb-1 flex items-center justify-between gap-2">
                                <span class="truncate text-sm font-medium text-gray-800 dark:text-white/90">
                                    {{ $notification->title }}
                                </span>
                                <span class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600 dark:bg-white/10 dark:text-gray-300">
                                    {{ $notification->type_label }}
                                </span>
                            </span>
                            <span class="block max-h-10 overflow-hidden text-xs text-gray-500 dark:text-gray-400">
                                {{ $notification->message }}
                            </span>
                            <span class="mt-1 block text-[11px] text-gray-400 dark:text-gray-500">
                                Vencimento: {{ $notification->reference_date->format('d/m/Y') }}
                            </span>
                        </span>
                    </x-ui.dropdown-menu.item>
                    <x-ui.dropdown-menu.separator class="my-0.5 bg-gray-100 dark:bg-gray-700" />
                @empty
                    <p class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Não há lembretes não lidos.
                    </p>
                @endforelse
            </div>

            <x-ui.button as="a" :href="route('lembretes.index')" variant="outline" size="default" class="mt-3 w-full"
                x-on:click="close()">
                Ver todos os lembretes
            </x-ui.button>
        </x-ui.dropdown-menu.content>
    </x-ui.dropdown-menu>
</div>
