<?php

return [
    [
        'type' => 'header',
        'title' => 'Principal',
    ],
    [
        'type' => 'link',
        'title' => 'Dashboard',
        'icon' => 'svg/dashboard.svg',
        'route' => 'admin.dashboard',
        'active' => 'admin.dashboard',
    ],
    [
        'type' => 'group',
        'title' => 'Inventario',
        'icon' => 'svg/packages.svg',
        'active' => ['admin.categories.*', 'admin.products.*', 'admin.warehouses.*'],
        'items' => [
            [
                'type' => 'link',
                'title' => 'Categorías',
                'route' => 'admin.categories.index',
                'icon' => 'svg/list-check.svg',
                'active' => 'admin.categories.*',
            ],
            [
                'type' => 'link',
                'title' => 'Productos',
                'route' => 'admin.products.index',
                'icon' => 'svg/box.svg',
                'active' => 'admin.products.*',
            ],
            [
                'type' => 'link',
                'title' => 'Almacenes',
                'route' => 'admin.warehouses.index',
                'icon' => 'svg/building-warehouse.svg',
                'active' => 'admin.warehouses.*',
            ],
        ],
    ],

    [
        'type' => 'header',
        'title' => 'Gestion',
    ],

    [
        'type' => 'group',
        'title' => 'Compras',
        'icon' => 'svg/shopping-cart.svg',
        'active' => ['admin.suppliers.*', 'admin.purchase_orders.*', 'admin.purchases.*'],
        'items' => [
            [
                'type' => 'link',
                'title' => 'Proveedores',
                'icon' => 'svg/truck.svg',
                'route' => 'admin.suppliers.index',
                'active' => 'admin.suppliers.*',
            ],
            [
                'type' => 'link',
                'title' => 'Ordenes de compra',
                'icon' => 'svg/report-money.svg',
                'route' => 'admin.purchase_orders.index',
                'active' => 'admin.purchase_orders.*',
            ],
            [
                'type' => 'link',
                'title' => 'Compras',
                'icon' => 'svg/clipboard-check.svg',
                'route' => 'admin.purchases.index',
                'active' => 'admin.purchases.*',
            ],
        ],
    ],
    [
        'type' => 'group',
        'title' => 'Ventas',
        'icon' => 'svg/cash-register.svg',
        'active' => ['admin.customers.*', 'admin.quotes.*', 'admin.sales.*'],
        'items' => [
            [
                'type' => 'link',
                'title' => 'Clientes',
                'icon' => 'svg/users.svg',
                'route' => 'admin.customers.index',
                'active' => 'admin.customers.*',
            ],
            [
                'type' => 'link',
                'title' => 'Cotizaciones',
                'icon' => 'svg/clipboard-list.svg',
                'route' => 'admin.quotes.index',
                'active' => 'admin.quotes.*',
            ],
            [
                'type' => 'link',
                'title' => 'Ventas',
                'icon' => 'svg/shopping-cart-copy.svg',
                'route' => 'admin.sales.index',
                'active' => 'admin.sales.*',
            ],
        ],
    ],

    [
        'type' => 'group',
        'title' => 'Movimientos',
        'icon' => 'svg/rotate-clockwise.svg',
        'active' => ['admin.movements.*', 'admin.transfers.*'],
        'items' => [
            [
                'type' => 'link',
                'title' => 'Entradas y Salidas',
                'icon' => 'svg/arrows-exchange.svg',
                'route' => 'admin.movements.index',
                'active' => 'admin.movements.*',
            ],
            [
                'type' => 'link',
                'title' => 'Transferencias',
                'icon' => 'svg/transfer-in.svg',
                'route' => 'admin.transfers.index',
                'active' => 'admin.transfers.*',
            ],
        ],
    ],

    [
        'type' => 'group',
        'title' => 'Reportes',
        'icon' => 'svg/chart-bar.svg',
        'active' => ['admin.reports.*'],
        'items' => [
            [
                'type' => 'link',
                'title' => 'Productos más vendidos',
                'icon' => 'svg/shopping-cart-share.svg',
                'route' => 'admin.reports.top-products',
                'active' => 'admin.reports.top-products',
            ],
            [
                'type' => 'link',
                'title' => 'Productos con poco stock',
                'icon' => 'svg/shopping-cart-down.svg',
                'route' => 'admin.reports.low-stock',
                'active' => 'admin.reports.low-stock',
            ],
            [
                'type' => 'link',
                'title' => 'Clientes más frecuentes',
                'icon' => 'svg/user-up.svg',
                'route' => 'admin.reports.top-costumers',
                'active' => 'admin.reports.top-costumers',
            ],
        ],
    ],

    [
        'type' => 'header',
        'title' => 'Configuraión',
    ],

    [
        'type' => 'link',
        'title' => 'Usuarios',
        'icon' => 'svg/users.svg',
        'route' => 'admin.users.index',
        'active' => ['admin.users.*'],
    ],

    [
        'type' => 'link',
        'title' => 'Roles',
        'icon' => 'svg/circles.svg',
        'route' => 'admin.customers.index',
        'active' => ['admin.categories.*'],
    ],

    [
        'type' => 'link',
        'title' => 'Permisos',
        'icon' => 'svg/shield-lock.svg',
        'route' => 'admin.customers.index',
        'active' => ['admin.categories.*'],
    ],

    [
        'type' => 'link',
        'title' => 'Ajustes',
        'icon' => 'svg/settings.svg',
        'route' => 'admin.customers.index',
        'active' => ['admin.categories.*'],
    ],
];
