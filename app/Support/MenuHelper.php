<?php

namespace App\Support;

class MenuHelper
{
    /**
     * @return array<int, array{title: string, items: array<int, array{name: string, path: string, icon: string}>}>
     */
    public static function getMenuGroups(): array
    {
        return [
            [
                'title' => 'Financeiro',
                'items' => [
                    ['name' => 'Home', 'path' => '/home', 'icon' => 'home'],
                    ['name' => 'Dashboard', 'path' => '/dashboard', 'icon' => 'chart'],
                    // ['name' => 'Criar conta', 'path' => '/cadastro/create', 'icon' => 'plus'],
                ],
            ],
            [
                'title' => 'Organização',
                'items' => [
                    ['name' => 'Categorias', 'path' => '/category', 'icon' => 'category'],
                    ['name' => 'Lembretes', 'path' => '/lembretes', 'icon' => 'bell'],
                ],
            ],
        ];
    }

    public static function getIconSvg(string $icon): string
    {
        $paths = match ($icon) {
            'home' => '<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1z"/>',
            'chart' => '<path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-5 5"/>',
            'plus' => '<path d="M12 5v14"/><path d="M5 12h14"/>',
            'category' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
            'bell' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/>',
            default => throw new \InvalidArgumentException("Unknown menu icon [{$icon}]."),
        };

        return '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$paths.'</svg>';
    }
}
