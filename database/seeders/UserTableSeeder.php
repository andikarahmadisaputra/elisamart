<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Perawat Satu', 
                'email' => 'perawat1@gmail.com',
                'username' => 'perawat1',
                'password' => bcrypt('123456'),
                'gender' => 'pria',
                'pin' => '123456',
            ],
            [
                'name' => 'Perawat Dua', 
                'email' => 'perawat2@gmail.com',
                'username' => 'perawat2',
                'password' => bcrypt('123456'),
                'gender' => 'pria',
                'pin' => '123456',
            ],
            [
                'name' => 'Perawat Tiga', 
                'email' => 'perawat3@gmail.com',
                'username' => 'perawat3',
                'password' => bcrypt('123456'),
                'gender' => 'pria',
                'pin' => '123456',
            ],
        ];
        
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
