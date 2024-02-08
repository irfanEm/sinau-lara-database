<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RawQueryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM categories");
    }

    public function testRawQuery()
    {
        DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (?, ?, ?, ?)", ['GDGT', 'PONSEL', 'Smartphone dan Gadget', '2024-02-08 00:00:00']);

        $hasil = DB::select("SELECT * FROM categories WHERE id = ?", ['GDGT']);

        self::assertCount(1, $hasil);
        self::assertEquals('GDGT', $hasil[0]->id);
        self::assertEquals('PONSEL', $hasil[0]->name);
        self::assertEquals('Smartphone dan Gadget', $hasil[0]->description);
        self::assertEquals('2024-02-08 00:00:00', $hasil[0]->created_at);
    }

    public function testRawQueryNamed()
    {
        DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (:id, :name, :description, :created_at)", [
            'id' => 'GDGT', 
            'name' => 'PONSEL', 
            'description' => 'Smartphone dan Gadget', 
            'created_at' => '2024-02-08 00:00:00'
        ]);

        $hasil = DB::select("SELECT * FROM categories WHERE id = :id", ['id' => 'GDGT']);

        self::assertCount(1, $hasil);
        self::assertEquals('GDGT', $hasil[0]->id);
        self::assertEquals('PONSEL', $hasil[0]->name);
        self::assertEquals('Smartphone dan Gadget', $hasil[0]->description);
        self::assertEquals('2024-02-08 00:00:00', $hasil[0]->created_at);
    }

    public function testTransaction()
    {
        DB::transaction(function(){
            DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (:id, :name, :description, :created_at)", [
                "id" => "JJN",
                "name" => "Go Potato",
                "description" => "Ciki2",
                "created_at" => "2024-02-09 00:00:00"
            ]);
            DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (?, ?, ?, ?)", [
               "Made", "Cireng", "Cireng goreng isi", "2024-02-09 00:01:00"
            ]);
        });

        $hasil = DB::select("SELECT * FROM categories");
        self::assertEquals(2, count($hasil));
    }

    public function testTransactionFail()
    {
        try{
            DB::transaction(function(){
                DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (:id, :name, :description, :created_at)", [
                    "id" => "JJN",
                    "name" => "Go Potato",
                    "description" => "Ciki2",
                    "created_at" => "2024-02-09 00:00:00"
                ]);
                DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (?, ?, ?, ?)", [
                   "JJN", "Cireng", "Cireng goreng isi", "2024-02-09 00:01:00"
                ]);
            });
        }catch(QueryException $err){
            
        }

        $hasil = DB::select("SELECT * FROM categories");
        self::assertEquals(0, count($hasil));
    }

    public function testTransactionManual()
    {
        try{
            DB::beginTransaction();

            DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (:id, :name, :description, :created_at)", [
                "id" => "JJN",
                "name" => "Go Potato",
                "description" => "Ciki2",
                "created_at" => "2024-02-09 00:00:00"
            ]);
            DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (?, ?, ?, ?)", [
               "MADE", "Cireng", "Cireng goreng isi", "2024-02-09 00:01:00"
            ]);

            DB::commit();
        }catch(\Throwable $err)
        {
            DB::rollBack();
            throw $err;
        }

        $hasil = DB::select("SELECT * FROM categories");
        self::assertEquals(2, count($hasil));
    }

    public function testTransactionManualGagal()
    {
        try{
            DB::beginTransaction();

            DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (:id, :name, :description, :created_at)", [
                "id" => "MADE",
                "name" => "Go Potato",
                "description" => "Ciki2",
                "created_at" => "2024-02-09 00:00:00"
            ]);
            DB::insert("INSERT INTO categories (id, name, description, created_at) VALUES (?, ?, ?, ?)", [
               "MADE", "Cireng", "Cireng goreng isi", "2024-02-09 00:01:00"
            ]);

            DB::commit();
        }catch(\Throwable $err)
        {
            DB::rollBack();
        }

        $hasil = DB::select("SELECT * FROM categories");
        self::assertEquals(0, count($hasil));
    }
}
