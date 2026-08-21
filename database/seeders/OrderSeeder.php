<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
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
            $subtotal = round(rand(10000, 500000) / 100, 2);
            $tax = round($subtotal * (rand(0, 21) / 100), 2);
            $shippingCost = round(rand(0, 5000) / 100, 2);
            $total = round($subtotal + $tax + $shippingCost, 2);

            $orders[] = [
                'order_number' => 'ORD-' . (1000 + $i),
                'user_id' => User::inRandomOrder()->first()?->id,
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

        DB::table('orders')->insert($orders);
    }
}