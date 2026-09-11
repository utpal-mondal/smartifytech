<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BrandSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $brands = [
            [
                'id' => 1,
                'name' => 'Samsung',
                'priority' => 0,
                'description' => 'Samsung',
                'created_at' => '2024-04-22 12:38:00',
                'updated_at' => '2025-02-19 21:24:35',
            ],
            [
                'id' => 3,
                'name' => 'Apple',
                'priority' => 0,
                'description' => 'Apple',
                'created_at' => '2024-05-08 17:00:50',
                'updated_at' => '2024-05-08 17:00:50',
            ],
            [
                'id' => 10,
                'name' => 'XIAOMI',
                'priority' => 0,
                'description' => 'XIAOMI',
                'created_at' => '2025-01-06 18:47:43',
                'updated_at' => '2025-01-06 18:47:43',
            ],
            [
                'id' => 17,
                'name' => 'Kingstone',
                'priority' => 0,
                'description' => 'Kingstone',
                'created_at' => '2025-11-14 15:18:36',
                'updated_at' => '2025-11-14 15:18:36',
            ],
            [
                'id' => 18,
                'name' => 'Amazfit',
                'priority' => 0,
                'description' => 'Amazfit',
                'created_at' => '2025-12-23 20:07:16',
                'updated_at' => '2025-12-23 20:07:16',
            ],
            [
                'id' => 19,
                'name' => 'GOOGLE PIXEL',
                'priority' => 0,
                'description' => 'GOOGLE PIXEL',
                'created_at' => '2026-01-16 16:41:29',
                'updated_at' => '2026-01-16 16:41:29',
            ],
            [
                'id' => 20,
                'name' => 'KPN SIM',
                'priority' => 0,
                'description' => 'KPN SIM',
                'created_at' => '2026-04-18 19:01:02',
                'updated_at' => '2026-04-18 19:01:02',
            ],
            [
                'id' => 21,
                'name' => 'VODAFONE SIM',
                'priority' => 0,
                'description' => 'VODAFONE SIM',
                'created_at' => '2026-04-18 21:16:18',
                'updated_at' => '2026-04-18 21:16:18',
            ],
            [
                'id' => 22,
                'name' => 'INFINIX',
                'priority' => 0,
                'description' => 'INFINIX',
                'created_at' => '2026-05-26 18:31:39',
                'updated_at' => '2026-05-26 18:31:39',
            ],
            [
                'id' => 23,
                'name' => 'Garmin',
                'priority' => 0,
                'description' => 'Garmin',
                'created_at' => '2026-05-26 18:35:04',
                'updated_at' => '2026-05-26 18:35:04',
            ],
            [
                'id' => 25,
                'name' => 'NVIDIA',
                'priority' => 0,
                'description' => 'NVIDIA',
                'created_at' => '2026-07-06 18:15:19',
                'updated_at' => '2026-07-06 18:15:19',
            ],
            [
                'id' => 26,
                'name' => 'LENOVO ALL IN ONE',
                'priority' => 0,
                'description' => 'LENOVO ALL IN ONE',
                'created_at' => '2026-08-17 22:54:46',
                'updated_at' => '2026-08-17 22:54:46',
            ],
            [
                'id' => 27,
                'name' => 'USED GOODS',
                'priority' => 0,
                'description' => 'USED GOODS',
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ],
        ];

        DB::table('brands')->insert($brands);
    }
}
