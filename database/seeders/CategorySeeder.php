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
        DB::table('categories')->
            insert(['id' => 'CIKI', 'name' => 'Ciki', 'created_at'=>'2024-02-16 10:26:40']);
        DB::table('categories')->
            insert(['id' => 'GORENG', 'name' => 'GORENGAN', 'created_at'=>'2024-02-16 10:26:41']);
        DB::table('categories')->
            insert(['id' => 'ES', 'name' => 'Es', 'created_at'=>'2024-02-16 10:26:42']);
        DB::table('categories')->
            insert(['id' => 'BAKAR', 'name' => 'Bakaran', 'created_at'=>'2024-02-16 10:26:43']);
        DB::table('categories')->
            insert(['id' => 'BASAH', 'name' => 'Basahan', 'created_at'=>'2024-02-16 10:26:44']);
    }
}
