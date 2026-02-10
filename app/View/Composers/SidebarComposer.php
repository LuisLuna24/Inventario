<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Services\Sidebar\ItemGroup;
use App\Services\Sidebar\ItemHeader;
use App\Services\Sidebar\ItemLink;
use App\Services\Sidebar\ItemInterface;

class SidebarComposer
{
    public function compose(View $view)
    {
        $configItems = config('sidebar', []);

        $items = [];

        foreach ($configItems as $item) {
            if (is_array($item)) {
                $items[] = $this->parseItem($item);
            }
        }

        $view->with('itemsSidebar', $items);
    }
    /**
     * Convierte un array de configuración en un Objeto (Link, Group, Header).
     */
    protected function parseItem(array $item): ItemInterface
    {
        $isActive = isset($item['active']) ? request()->routeIs($item['active']) : false;

        // Valores por defecto seguros
        $title = $item['title'] ?? '';
        $icon  = $item['icon'] ?? '';
        $can   = $item['can'] ?? [];

        switch ($item['type']) {
            case 'header':
                return new ItemHeader(
                    title: $title,
                    can: $can
                );

            case 'link':
                return new ItemLink(
                    title: $title,
                    icon: $icon,
                    route: isset($item['route']) ? $item['route'] : '#',
                    active: $isActive,
                    can: $can
                );

            case 'group':
                $group = new ItemGroup(
                    title: $title,
                    icon: $icon,
                    items: [],
                    active: $isActive,
                    can: $can
                );

                if (isset($item['items']) && is_array($item['items'])) {
                    foreach ($item['items'] as $subitem) {
                        $group->add($this->parseItem($subitem));
                    }
                }

                return $group;

            default:
                throw new \InvalidArgumentException("Tipo de item desconocido: {$item['type']}");
        }
    }
}
