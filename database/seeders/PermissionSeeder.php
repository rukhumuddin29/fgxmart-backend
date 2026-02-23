<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Permission;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'Roles',
            'Users',
            'Products',
            'Categories',
            'Attributes',
            'Orders',
            'Localization',
            'Coupons',
            'Tax',
            'Product Reviews',
            'Support Tickets',
            'Reports',
            'Permissions',
            'Countries'
        ];

        $actions = [
            'index' => 'View',
            'create' => 'Create',
            'edit' => 'Edit',
            'destroy' => 'Delete'
        ];

        foreach ($modules as $module) {
            $slugModule = Str::slug($module);

            foreach ($actions as $actionSlug => $actionName) {
                // Example: roles.index, users.create
                $permissionSlug = $slugModule . '.' . $actionSlug;
                $permissionName = $module . ' ' . $actionName;

                Permission::firstOrCreate(
                ['slug' => $permissionSlug],
                ['name' => $permissionName]
                );
            }
        }
    }
}
