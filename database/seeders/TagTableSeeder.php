<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Tag;

class TagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tag = Tag::create([
            'name' => 'Staff RS Elisabeth', 
            'detail' => 'Staff Rumah Sakit Elisabeth'
        ]);

        $tag = Tag::create([
            'name' => 'Perawat', 
            'detail' => 'Perawat Rumah Sakit Elisabeth'
        ]);
    }
}
