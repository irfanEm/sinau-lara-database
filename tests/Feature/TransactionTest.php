<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Throwable;

class TransactionTest extends TestCase
{
    public function testDBTransactionSukses()
    {
        DB::transaction(function(){
            DB::insert('INSERT INTO categories (id, name, description, created_at)  VALUES (?, ?, ?,?)', ['GORENG', 'GORENGAN', 'Aneka Gorengan', '2024-02-20 00:00:00']);
            DB::insert('INSERT INTO categories (id, name, description, created_at)  VALUES (?, ?, ?,?)', ['CIKI', 'CIKI-CIKI', 'Jajanan Ciki', '2024-02-20 00:00:01']);
        });

        $hasil = DB::select("SELECT * FROM categories");
        self::assertCount(2, $hasil);
    }

    public function testDBTransactionGagal()
    {
        try{
            DB::transaction(function(){
                DB::insert('INSERT INTO categories (id, name, description, created_at)  VALUES (?, ?, ?,?)', ['GORENG', 'GORENGAN', 'Aneka Gorengan', '2024-02-20 00:00:00']);
                DB::insert('INSERT INTO categories (id, name, description, created_at)  VALUES (?, ?, ?,?)', ['GORENG', 'CIKI-CIKI', 'Jajanan Ciki', '2024-02-20 00:00:01']);
            });
        }catch(QueryException $err){
            //expected
        }

        $hasil = DB::select("SELECT * FROM categories");
        self::assertCount(0, $hasil);
    }

    public function testTransactionManual()
    {
        try{
            DB::beginTransaction();
            DB::insert('INSERT INTO categories (id, name, description, created_at)  VALUES (?, ?, ?,?)', ['GORENG', 'GORENGAN', 'Aneka Gorengan', '2024-02-20 00:00:00']);
            DB::insert('INSERT INTO categories (id, name, description, created_at)  VALUES (?, ?, ?,?)', ['CIKI', 'CIKI-CIKI', 'Jajanan Ciki', '2024-02-20 00:00:01']);
            DB::commit();
        }catch(Throwable $e){
            DB::rollBack();
            throw $e;
        }

        $hasil = DB::select("SELECT * FROM categories");
        self::assertCount(2, $hasil);
    }

    public function testTransactionManualGagal()
    {
        try{
            DB::beginTransaction();
            DB::insert('INSERT INTO categories (id, name, description, created_at)  VALUES (?, ?, ?,?)', ['GORENG', 'GORENGAN', 'Aneka Gorengan', '2024-02-20 00:00:00']);
            DB::insert('INSERT INTO categories (id, name, description, created_at)  VALUES (?, ?, ?,?)', ['GORENG', 'CIKI-CIKI', 'Jajanan Ciki', '2024-02-20 00:00:01']);
            DB::commit();
        }catch(QueryException $e){
            DB::rollBack();
            // expected
        }

        $hasil = DB::select("SELECT * FROM categories");
        self::assertCount(0, $hasil);
    }
}
