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
                'can' => ['view-categories'],
            ],
            [
                'type' => 'link',
                'title' => 'Productos',
                'route' => 'admin.products.index',
                'icon' => 'svg/box.svg',
                'active' => 'admin.products.*',
                'can' => ['view-products'],
            ],
            [
                'type' => 'link',
                'title' => 'Almacenes',
                'route' => 'admin.warehouses.index',
                'icon' => 'svg/building-warehouse.svg',
                'active' => 'admin.warehouses.*',
                'can' => ['view-warehouses'],
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
                'can' => ['view-suppliers'],
            ],
            [
                'type' => 'link',
                'title' => 'Ordenes de compra',
                'icon' => 'svg/report-money.svg',
                'route' => 'admin.purchase_orders.index',
                'active' => 'admin.purchase_orders.*',
                'can' => ['view-purchase-orders'],
            ],
            [
                'type' => 'link',
                'title' => 'Compras',
                'icon' => 'svg/clipboard-check.svg',
                'route' => 'admin.purchases.index',
                'active' => 'admin.purchases.*',
                'can' => ['view-purchases'],
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
                'can' => ['view-customers'],
            ],
            [
                'type' => 'link',
                'title' => 'Cotizaciones',
                'icon' => 'svg/clipboard-list.svg',
                'route' => 'admin.quotes.index',
                'active' => 'admin.quotes.*',
                'can' => ['view-quotes'],
            ],
            [
                'type' => 'link',
                'title' => 'Ventas',
                'icon' => 'svg/shopping-cart-copy.svg',
                'route' => 'admin.sales.index',
                'active' => 'admin.sales.*',
                'can' => ['view-sales'],
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
                'can' => ['view-movements'],
            ],
            [
                'type' => 'link',
                'title' => 'Transferencias',
                'icon' => 'svg/transfer-in.svg',
                'route' => 'admin.transfers.index',
                'active' => 'admin.transfers.*',
                'can' => ['view-transfers'],
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
                'can' => ['view-top-products'],
            ],
            [
                'type' => 'link',
                'title' => 'Productos con poco stock',
                'icon' => 'svg/shopping-cart-down.svg',
                'route' => 'admin.reports.low-stock',
                'active' => 'admin.reports.low-stock',
                'can' => ['view-low-stock'],
            ],
            [
                'type' => 'link',
                'title' => 'Clientes más frecuentes',
                'icon' => 'svg/user-up.svg',
                'route' => 'admin.reports.top-costumers',
                'active' => 'admin.reports.top-costumers',
                'can' => ['view-top-customers'],
            ],
        ],
    ],

    [
        'type' => 'header',
        'title' => 'Configuración',
        // Opcional: El header solo se ve si puede ver usuarios o roles
        'can' => ['view-users', 'view-roles', 'view-settings'],
    ],

    [
        'type' => 'link',
        'title' => 'Usuarios',
        'icon' => 'svg/users.svg',
        'route' => 'admin.users.index',
        'active' => ['admin.users.*'],
        'can' => ['view-users'],
    ],

    [
        'type' => 'link',
        'title' => 'Roles',
        'icon' => 'svg/circles.svg',
        'route' => 'admin.roles.index',
        'active' => ['admin.roles.*'],
        'can' => ['view-roles'],
    ],

    [
        'type' => 'link',
        'title' => 'Ajustes',
        'icon' => 'svg/settings.svg',
        'route' => 'admin.customers.index', // Nota: Aquí tenías admin.customers.index, cámbialo si tienes una ruta de ajustes
        'active' => ['admin.settings.*'],
        'can' => ['view-settings'],
    ],
];
