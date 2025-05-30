<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Andika Rahmadi Saputra', 
            'email' => 'andikars811@gmail.com',
            'password' => 'P@ssw0rd',
            'nik' => '3315142601960001',
            'username' => 'andikars811',
            'gender' => 'pria',
            'phone' => '082242145737',
            'pin' => bcrypt('123456'),
        ]);
        
        $role = Role::create(['name' => 'Admin']);
         
        $permissions = Permission::pluck('id','id')->all();
       
        $role->syncPermissions($permissions);
         
        $user->assignRole([$role->id]);
    }
}
