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

        $typeModels = [
            'iphone' => ['iPhone 15', 'iPhone 15 Pro', 'iPhone 15 Pro Max', 'iPhone 14', 'iPhone 14 Pro', 'iPhone 14 Pro Max', 'iPhone 13', 'iPhone 13 Pro', 'iPhone SE 2022'],
            'samsung' => ['Galaxy S24', 'Galaxy S24+', 'Galaxy S24 Ultra', 'Galaxy S23', 'Galaxy S23+', 'Galaxy S23 Ultra', 'Galaxy Z Fold 6', 'Galaxy Z Flip 6', 'Galaxy A55'],
            'realme' => ['Realme GT 6', 'Realme GT 5', 'Realme 12 Pro+', 'Realme 12 Pro', 'Realme 11 Pro+', 'Realme 11 Pro', 'Realme Narzo 70', 'Realme Narzo 70 Pro', 'Realme C67'],
            'oneplus' => ['OnePlus 12', 'OnePlus 12R', 'OnePlus 11', 'OnePlus 11R', 'OnePlus Open', 'OnePlus Nord 4', 'OnePlus Nord CE 4', 'OnePlus 10 Pro', 'OnePlus 10T'],
            'oppo' => ['Oppo Find X7', 'Oppo Find X6', 'Oppo Find N3', 'Oppo Reno 12 Pro', 'Oppo Reno 11 Pro', 'Oppo Reno 10 Pro', 'Oppo A98', 'Oppo A78', 'Oppo F27'],
            'vivo' => ['Vivo X100', 'Vivo X100 Pro', 'Vivo V30', 'Vivo V30 Pro', 'Vivo Y200', 'Vivo Y100', 'Vivo T3 Pro', 'Vivo T3x', 'Vivo X90'],
        ];

        $products = [];
        foreach ($typeModels as $type => $models) {
            foreach ($models as $model) {
                $products[] = ['type' => $type, 'model' => $model];
            }
        }

        shuffle($products);
        $products = array_slice($products, 0, 50);

        $rows = [];
        foreach ($products as $product) {
            $rows[] = [
                'price_list_id' => (string) $priceList->id,
                'pdf_name' => $priceList->file_name,
                'quantity' => (string) rand(1, 1000),
                'model' => $product['model'],
                'type' => $product['type'],
                'price' => number_format(rand(5000, 99999) / 100, 2, '.', ''),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Product::insert($rows);
    }
}