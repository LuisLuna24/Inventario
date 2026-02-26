<?php

namespace App\Services\Sidebar;

class ItemHeader implements ItemInterface
{
    private string $title;
    private array $can;

    // Corrección: 'string' en minúscula y valor por defecto para $can
    public function __construct(string $title, array $can = [])
    {
        $this->title = $title;
        $this->can = $can;
    }

    public function render(): string
    {
        return <<<HTML
            <div class="pt-4 pb-1 pl-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                {$this->title}
            </div>
        HTML;
    }

    public function authorize(): bool
    {
        if (empty($this->can)) {
            return true;
        }
        return \Illuminate\Support\Facades\Gate::any($this->can);
    }
}
