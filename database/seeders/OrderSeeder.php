<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        $orders = [];

        for ($i = 1; $i <= 50; $i++) {
            $product = Product::inRandomOrder()->first();
            $quantity = rand(1, 10);

            $subtotal = $product
                ? round((float) $product->price * $quantity, 2)
                : round(rand(10000, 500000) / 100, 2);

            $tax = round($subtotal * (rand(0, 21) / 100), 2);
            $shippingCost = round(rand(0, 5000) / 100, 2);
            $total = round($subtotal + $tax + $shippingCost, 2);

            $orders[] = [
                'order_number' => 'O' . date('Y') . str_pad($i, 4, '0', STR_PAD_LEFT),
                'user_id' => User::inRandomOrder()->first()?->id,
                'product_id' => $product?->id,
                'quantity' => $quantity,
                'customer_name' => $faker->name,
                'customer_email' => $faker->safeEmail,
                'customer_phone' => $faker->phoneNumber,
                'billing_address' => $faker->address,
                'shipping_address' => $faker->address,
                'status' => $faker->randomElement($statuses),
                'payment_status' => $faker->randomElement($paymentStatuses),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'notes' => $faker->optional()->sentence,
                'ordered_at' => now()->subDays(rand(0, 365))->subHours(rand(0, 23)),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Order::insert($orders);
    }
}