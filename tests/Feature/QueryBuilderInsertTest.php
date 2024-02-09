<?php

namespace Tests\Feature;

use Illuminate\Database\Query\Builder;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class QueryBuilderInsertTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM categories");
    }
    public function testInsertQueryBuilder()
    {
        DB::table('categories')->insert([
            'id' => 'ES',
            'name' => 'Es Marimas'
        ]);

        DB::table('categories')->insert([
            'id' => 'GRNGN',
            'name' => 'Cireng'
        ]);

        $hasil = DB::select("SELECT COUNT(id) as total FROM categories");
        self::assertEquals(2, $hasil[0]->total);
    }

    public function testQueryBuilderSelect()
    {
        $this->testInsertQueryBuilder();

        $koleksi = DB::table('categories')->select(['id', 'name'])->get();
        self::assertNotNull($koleksi);

        $koleksi->each(function($records){
            Log::info(json_encode($records));
        });
    }

    public function testQueryBuilderWhere()
    {
        DB::table('categories')->insert([
            'id' => 'CIKI', 'name' => 'Polt', 'description' => 'Kacang polong dibalur trigu', 'created_at' => '2024-02-05 00:00:00'
        ]);
        DB::table('categories')->insert([
            'id' => 'ES', 'name' => 'Marimas', 'description' => 'Es Marimas dengan es batu', 'created_at' => '2024-02-05 00:01:00'
        ]);
        DB::table('categories')->insert([
            'id' => 'GORENG', 'name' => 'Cireng', 'description' => 'Cireng isi ayam suir', 'created_at' => '2024-02-05 00:02:00'
        ]);
        DB::table('categories')->insert([
            'id' => 'KUAH', 'name' => 'Seblak', 'description' => 'Seblak dengan berbagai macam toping', 'created_at' => '2024-02-05 00:03:00'
        ]);
        DB::table('categories')->insert([
            'id' => 'TTP', 'name' => 'Makaroni', 'description' => 'Makaroni goreng berbagai varian', 'created_at' => '2024-02-05 00:04:00'
        ]);
    }

    public function testWhere()
    {
        $this->testQueryBuilderWhere();

        $koleksi = DB::table('categories')->where(function(Builder $builder){
            $builder->where('id', '=', 'ES');
            $builder->orWhere('id', '=', 'CIKI');
        })->get();

        // var_dump($koleksi);
        self::assertCount(2, $koleksi);
        $koleksi->each(function($hasil){
            Log::info(json_encode($hasil));
        });
    }

    public function testWhereBetween()
    {
        $this->testQueryBuilderWhere();

        $koleksi = DB::table('categories')->whereBetween('created_at', ['2024-02-05 00:00:00', '2024-02-05 00:02:00'])->get();

        self::assertCount(3, $koleksi);
        $koleksi->each(function($hasil){
            Log::info(json_encode($hasil));
        });
    }

    public function testWhereIn()
    {
        $this->testQueryBuilderWhere();

        $koleksi = DB::table('categories')->whereIn('id', ['CIKI', 'ES'])->get();

        self::assertCount(2, $koleksi);
        $koleksi->each(function($hasil){
            Log::info(json_encode($hasil));
        });
    }

    public function testWhereNotNull()
    {
        $this->testQueryBuilderWhere();

        $koleksi = DB::table('categories')->whereNotNull('description')->get();

        self::assertCount(5, $koleksi);
        $koleksi->each(function($hasil){
            Log::info(json_encode($hasil));
        });
    }

    public function testWhereNull()
    {
        $this->testQueryBuilderWhere();

        $koleksi = DB::table('categories')->whereNull('description')->get();

        self::assertCount(0, $koleksi);
        $koleksi->each(function($hasil){
            Log::info(json_encode($hasil));
        });
    }

    public function testWhereDate()
    {
        $this->testQueryBuilderWhere();

        $koleksi = DB::table('categories')->whereDate('created_at', '2024-02-05')->get();

        self::assertCount(5, $koleksi);
        $koleksi->each(function($hasil){
            Log::info(json_encode($hasil));
        });
    }

    public function testQueryUpdate()
    {
        $this->testQueryBuilderWhere();

        DB::table('categories')->where('id', '=', 'CIKI')->update(['name' => 'Komo']);
        $koleksi = DB::table('categories')->where('id', '=', 'CIKI')->get();

        self::assertCount(1, $koleksi);
        $koleksi->each(function($hasil){
            Log::info(json_encode($hasil));
        });
    }

    public function testUpsert()
    {
        DB::table('categories')->updateOrInsert([
            'id' => 'PULSA'
        ],[
            'name' => 'Pulsa Reguler',
            'description' => 'Pulsa biasa untuk telfon / sms.',
            'created_at' => '2024-02-04 00:00:00'
        ]);

        $koleksi = DB::table('categories')->where('id', '=', 'PULSA')->get();
        self::assertCount(1, $koleksi);
        $koleksi->each(function($hasil){
            Log::info(json_encode($hasil));
        });
    }

    public function testIncrement()
    {
        DB::table('counters')->where('id', '=', 'sample')->increment('counter', 1);

        $koleksi = DB::table('counters')->where('id', '=', 'sample')->get();

        self::assertCount(1, $koleksi);
        $koleksi->each(function($hasil){
            Log::info(json_encode($hasil));
        });
    }

    public function testQueryDelete()
    {
        $this->testQueryBuilderWhere();

        DB::table('categories')->where('id', '=', 'TTP')->delete();
        $koleksi = DB::table('categories')->where('id', '=', 'TTP')->get();
        self::assertCount(0, $koleksi);
    }
}
    