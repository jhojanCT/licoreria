<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'products.view',
            'products.manage',
            'warehouse.view',
            'purchases.view',
            'purchases.create',
            'sales.view',
            'sales.create',
            'cash.close_basic',
            'cash.close_admin',
            'cash.review_difference',
            'reports.view',
            'roles.manage',
            'users.manage',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $admin = Role::findOrCreate('administrador', 'web');
        $admin->syncPermissions(Permission::query()->where('guard_name', 'web')->get());

        $vendedor = Role::findOrCreate('vendedor', 'web');
        $vendedor->syncPermissions([
            'products.view',
            'sales.view',
            'sales.create',
            'cash.close_basic',
        ]);

        $almacen = Role::findOrCreate('almacen', 'web');
        $almacen->syncPermissions([
            'products.view',
            'products.manage',
            'warehouse.view',
            'purchases.view',
            'purchases.create',
        ]);
    }
}
