<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Tag;

class TagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            [
                'name' => 'Staff RS Elisabeth', 
                'detail' => 'Staff Rumah Sakit Elisabeth',
            ],
            [
                'name' => 'Perawat', 
                'detail' => 'Perawat Rumah Sakit Elisabeth',
            ],
            [
                'name' => 'Dokter', 
                'detail' => 'Dokter Rumah Sakit Elisabeth',
            ],
        ];
       
        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
