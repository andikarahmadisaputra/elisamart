<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PermissionTableSeeder::class,
            TagTableSeeder::class,
            CreateAdminUserSeeder::class,
            StoreTableSeeder::class,
            UserTableSeeder::class,
        ]);
    }
}
