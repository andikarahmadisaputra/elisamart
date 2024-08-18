<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'tag-list',
            'tag-create',
            'tag-edit',
            'tag-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'store-list',
            'store-create',
            'store-edit',
            'store-delete',
        ];
         
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
