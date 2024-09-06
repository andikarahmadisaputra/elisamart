<?php

namespace Database\Seeders;

use App\Models\Store;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = [
            [
                'name' => 'Elisamart', 
                'detail' => 'Elisamart',
            ],
        ];

        foreach ($stores as $store) {
            Store::create($store);
        }
    }
}
