<?php

namespace App\Services\Sidebar;

class ItemGroup implements ItemInterface
{
    private string $title;
    private string $icon;
    private bool $active;
    private array $items;
    private array $can;

    public function __construct(string $title, string $icon, array $items = [], bool $active = false, array $can = [])
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->items = $items;
        $this->active = $active;
        $this->can = $can;
    }

    public function add(ItemInterface $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    public function render(): string
    {
        $isExpanded = $this->active ? 'true' : 'false';

        $buttonClasses = $this->active
            ? 'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10'
            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800';

        $iconHtml = '';
        $iconPath = public_path($this->icon);

        if ($this->icon && file_exists($iconPath)) {
            $iconHtml = file_get_contents($iconPath);
        } else {
            $iconHtml = <<<SVG
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            SVG;
        }

        $childrenHtml = '';
        foreach ($this->items as $item) {
            if ($item instanceof ItemInterface) {
                $childrenHtml .= $item->render();
            }
        }

        return <<<HTML
            <div x-data="{ isExpanded: {$isExpanded} }" class="space-y-1">
                <button @click="isExpanded = !isExpanded"
                        class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium transition-colors rounded-lg group {$buttonClasses}">

                    <div class="flex items-center gap-3">
                        <span class="w-5 h-5 flex items-center justify-center">
                            {$iconHtml}
                        </span>
                        <span>{$this->title}</span>
                    </div>

                    <svg class="w-4 h-4 transition-transform duration-200"
                         :class="isExpanded ? 'rotate-180' : ''"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="isExpanded" x-collapse x-cloak class="pl-10 space-y-1">
                    {$childrenHtml}
                </div>
            </div>
        HTML;
    }

    public function authorize(): bool
    {
        return true;
    }
}
