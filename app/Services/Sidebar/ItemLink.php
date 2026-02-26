<?php

namespace App\Services\Sidebar;

class ItemLink implements ItemInterface
{
    private string $title;
    private string $icon;
    private string $route;
    private bool $active;
    private array $can;

    public function __construct(string $title, string $icon, string $route, bool $active, array $can = [])
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->route = $route;
        $this->active = $active;
        $this->can = $can;
    }

    public function render(): string
    {
        $url = route($this->route);

        $cssClasses = $this->active
            ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400'
            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800';

        $iconHtml = '';
        $iconPath = public_path($this->icon);

        if ($this->icon && file_exists($iconPath)) {
            $iconHtml = file_get_contents($iconPath);
        } else {
            $iconHtml = <<<SVG
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            SVG;
        }
        return <<<HTML
            <a href="{$url}"
               class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg group {$cssClasses}">
                <span class="w-5 h-5 flex items-center justify-center">
                    {$iconHtml}
                </span>
                <span>{$this->title}</span>
            </a>
        HTML;
    }

    public function authorize(): bool
    {
        // Si no hay permisos definidos, el link es público
        if (empty($this->can)) {
            return true;
        }

        // Verificamos si el usuario tiene al menos uno de los permisos (usando el Gate de Laravel)
        return \Illuminate\Support\Facades\Gate::any($this->can);
    }
}
