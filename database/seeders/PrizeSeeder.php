<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('prizes')->insert([
            [
                'img' => 'iphone.png',
                'name' => 'iPhone 17',
                'point' => 300,
                'stock' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'img' => 'tv.png',
                'name' => 'Smart TV Xiaomi',
                'point' => 282,
                'stock' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'img' => 'garmin.png',
                'name' => 'Garmin Venu X1',
                'point' => 146,
                'stock' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'img' => 'emas.png',
                'name' => 'Logam Mulia 2 Gram',
                'point' => 128,
                'stock' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'img' => 'galaxy_buds.png',
                'name' => 'Galaxy Buds',
                'point' => 66,
                'stock' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'img' => 'tumblr.png',
                'name' => 'Stanley Tumbler',
                'point' => 32,
                'stock' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'img' => 'gopay.png',
                'name' => 'Saldo GoPay Rp 3 jt',
                'point' => 48,
                'stock' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'img' => 'gopay.png',
                'name' => 'Saldo GoPay Rp 1 jt',
                'point' => 18,
                'stock' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
