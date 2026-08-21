<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Price_list;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $priceList = Price_list::first();

        if (!$priceList) {
            $priceList = Price_list::create([
                'file_name' => 'default-price-list.pdf',
                'status' => 'active',
            ]);
        }

        $products = [];

        for ($i = 1; $i <= 50; $i++) {
            $products[] = [
                'price_list_id' => (string) $priceList->id,
                'pdf_name' => $priceList->file_name,
                'quantity' => (string) rand(1, 1000),
                'model' => 'SM-' . (1000 + $i),
                'price' => number_format(rand(5000, 99999) / 100, 2, '.', ''),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Product::insert($products);
    }
}