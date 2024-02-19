<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RawQueryTestPart2 extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from products');
        DB::delete('delete from categories');
    }

    public function testCrud()
    {
        DB::insert('INSERT INTO categories (id, name, description, created_at)  VALUES (?, ?, ?,?)', ['GORENG', 'GORENGAN', 'Aneka Gorengan', '2024-02-20 00:00:00']);

        $hasil = DB::select("SELECT * FROM categories");

        self::assertNotNull($hasil);
        self::assertCount(1, $hasil);
        self::assertEquals('GORENG', $hasil[0]->id);
        self::assertEquals('GORENGAN', $hasil[0]->name);
        self::assertEquals('Aneka Gorengan', $hasil[0]->description);
        self::assertEquals('2024-02-20 00:00:00', $hasil[0]->created_at);
    }

    public function testCrudNamedBinding()
    {
        DB::insert('INSERT INTO categories (id, name, description, created_at)  VALUES (:id, :name, :description,:created_at)', ['id' => 'GORENG', 'name' => 'GORENGAN', 'description' => 'Aneka Gorengan', 'created_at' => '2024-02-20 00:00:00']);

        $hasil = DB::select("SELECT * FROM categories");

        self::assertNotNull($hasil);
        self::assertCount(1, $hasil);
        self::assertEquals('GORENG', $hasil[0]->id);
        self::assertEquals('GORENGAN', $hasil[0]->name);
        self::assertEquals('Aneka Gorengan', $hasil[0]->description);
        self::assertEquals('2024-02-20 00:00:00', $hasil[0]->created_at);
    }
}
