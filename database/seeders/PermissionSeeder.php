<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            //Categorias
            'create-categories',
            'edit-categories',
            'delete-categories',
            'view-categories',

            //Productos
            'create-products',
            'edit-products',
            'delete-products',
            'view-products',

            //Almacenes
            'create-warehouses',
            'edit-warehouses',
            'delete-warehouses',
            'view-warehouses',

            //Proveedores
            'create-suppliers',
            'edit-suppliers',
            'delete-suppliers',
            'view-suppliers',

            //Ordenes de comprta
            'create-purchase-orders',
            'edit-purchase-orders',
            'delete-purchase-orders',
            'view-purchase-orders',
            'email-purchase-orders',

            //Compras
            'create-purchases',
            'edit-purchases',
            'delete-purchases',
            'view-purchases',
            'email-purchases',

            //Clientes
            'create-customers',
            'edit-customers',
            'delete-customers',
            'view-customers',

            //Presupuestos
            'create-quotes',
            'edit-quotes',
            'delete-quotes',
            'view-quotes',
            'email-quotes',

            //Ventas
            'create-sales',
            'edit-sales',
            'delete-sales',
            'view-sales',
            'email-sales',

            //Moviminetos
            'create-movements',
            'edit-movements',
            'delete-movements',
            'view-movements',
            'email-movements',

            //Transferencias
            'create-transfers',
            'edit-transfers',
            'delete-transfers',
            'view-transfers',
            'email-transfers',

            //Reportes
            'view-low-stock',
            'view-top-customers',
            'view-top-products',

            //Usuarios
            'create-users',
            'edit-users',
            'delete-users',
            'view-users',

            //Roles
            'create-roles',
            'edit-roles',
            'delete-roles',
            'view-roles',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        Role::create(['name' => 'Admin'])
            ->givePermissionTo(Permission::all());
    }
}
