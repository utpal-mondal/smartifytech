<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Price_list;

class PriceListSeeder extends Seeder
{
    public function run(): void
    {
        Price_list::create([
            'file_name' => 'price-list-q1-2026.pdf',
            'status' => 'active',
        ]);

        Price_list::create([
            'file_name' => 'price-list-q2-2026.pdf',
            'status' => 'active',
        ]);

        Price_list::create([
            'file_name' => 'price-list-q3-2026.pdf',
            'status' => 'inactive',
        ]);
    }
}