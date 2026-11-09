@php
    $user = auth()->user();
@endphp

@if ($user)
<x-ui.dropdown-menu>
    <x-ui.dropdown-menu.trigger class="flex cursor-pointer items-center text-gray-700 dark:text-gray-400">
        <span class="me-3 flex h-11 w-11 items-center justify-center overflow-hidden rounded-full bg-brand-50 font-semibold text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
            {{ mb_substr($user->name, 0, 1) }}
        </span>

        <span class="me-1 block max-w-32 truncate text-sm font-medium">{{ $user->name }}</span>

        <x-svg.chevron-down class="size-5 text-gray-500 duration-200 dark:text-gray-400" x-bind:class="open ? 'rotate-180' : 'rotate-0'" />
    </x-ui.dropdown-menu.trigger>

    <x-ui.dropdown-menu.content align="end" :side-offset="12" class="w-65 p-3">
        <div class="px-3 py-2">
            <span class="block truncate text-sm font-medium text-gray-700 dark:text-gray-400">{{ $user->name }}</span>
            <span class="mt-0.5 block truncate text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
        </div>

        <x-ui.dropdown-menu.separator />

        {{-- <x-ui.dropdown-menu.item :to="route('profile.edit', ['id' => $user->id])">
            <x-svg.user-circle class="size-6" />
            {{ __('userDropdown.editProfile') }}
        </x-ui.dropdown-menu.item> --}}

        <x-ui.dropdown-menu.item :to="route('profile.edit', ['id' => $user->id])">
            <x-svg.gear class="size-6" />
            {{ __('userDropdown.accountSettings') }}
        </x-ui.dropdown-menu.item>

        {{-- <x-ui.dropdown-menu.item :to="route('profile.edit', ['id' => $user->id])">
            <x-svg.info-circle class="size-6" />
            {{ __('userDropdown.support') }}
        </x-ui.dropdown-menu.item> --}}

        <x-ui.dropdown-menu.separator />

        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
        <x-ui.dropdown-menu.item variant="destructive" x-on:click.capture="document.getElementById('logout-form').submit()">
            <x-svg.log-out />
            {{ __('userDropdown.signOut') }}
        </x-ui.dropdown-menu.item>
    </x-ui.dropdown-menu.content>
</x-ui.dropdown-menu>
@endif
