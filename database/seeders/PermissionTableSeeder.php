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
            'role.list',
            'role.create',
            'role.edit',
            'role.delete',
            'tag.list',
            'tag.create',
            'tag.edit',
            'tag.delete',
            'user.list',
            'user.create',
            'user.edit',
            'user.delete',
            'store.list',
            'store.create',
            'store.edit',
            'store.delete',
            'topup_store.list',
            'topup_store.create',
            'topup_store.edit',
            'topup_store.delete',
            'topup_store.under_review',
            'topup_store.cancel',
            'topup_store.approval',
            'topup_user.list',
            'topup_user.show',
            'topup_user.create',
            'topup_user.edit',
            'topup_user.delete',
            'topup_user.cancel',
            'topup_user.under_review',
            'topup_user.approval',
            'payment_request.list',
            'payment_request.create',
            'payment_request.edit',
            'report_user.list',
            'report_topup_store.list',
            'report_topup_user.list',
            'report_received_payment.list',
            'report_mutation.list',
            'process.dailyclose',
            'process.monthlyclose',
            'process.dailybackup',
            'process.monthlybackup',
        ];
         
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
