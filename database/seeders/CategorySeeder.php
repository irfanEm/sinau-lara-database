<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            'id' => 'GORENG',
            'name' => 'GORENGAN',
            'description' => 'Aneka Gorengan',
            'created_at' => '2024-02-20 00:00:01'
        ]);
        DB::table('categories')->insert([
            'id' => 'CIKI',
            'name' => 'PERCIKIAN',
            'description' => 'Percikian Duniawi',
            'created_at' => '2024-02-20 00:00:02'
        ]);
        DB::table('categories')->insert([
            'id' => 'BAKAR',
            'name' => 'BAKARAN',
            'description' => 'Perbakaran Duniawi',
            'created_at' => '2024-02-20 00:00:03'
        ]);
        DB::table('categories')->insert([
            'id' => 'TITIP',
            'name' => 'Pertitipan Duniawi',
            'description' => 'Aneka Gorengan',
            'created_at' => '2024-02-20 00:00:04'
        ]);
        DB::table('categories')->insert([
            'id' => 'GODOG',
            'name' => 'PERGODOGAN',
            'description' => 'Pergodogan Duniawi',
            'created_at' => '2024-02-20 00:00:05'
        ]);
    }
}
