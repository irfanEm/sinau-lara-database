<?php

namespace Tests\Feature;

use Illuminate\Database\Query\Builder;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QueryBuilderTest2 extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from products');
        DB::delete('delete from categories');
    }

    public function testQueryBuilderInsert()
    {
        DB::table('categories')->insert([
            'id' => 'GORENG',
            'name' => 'GORENGAN',
            'description' => 'Aneka Gorengan',
            'created_at' => '2024-02-20 00:00:01'
        ]);

        DB::table('categories')->insert([
            'id' => 'CIKI',
            'name' => 'CIKI-CIKI',
            'description' => 'Aneka Gorengan',
            'created_at' => '2024-02-20 00:00:01'
        ]);

        $hasil = DB::select('SELECT COUNT(id) as total FROM categories');
        self::assertEquals(2, $hasil[0]->total);
    }

    public function testHasilColumn()
    {
        DB::table('categories')->insert([
            'id' => 'GORENG',
            'name' => 'GORENGAN',
            'description' => 'Aneka Gorengan',
            'created_at' => '2024-02-20 00:00:01'
        ]);

        DB::table('categories')->insert([
            'id' => 'CIKI',
            'name' => 'CIKI-CIKI',
            'description' => 'Aneka Gorengan',
            'created_at' => '2024-02-20 00:00:01'
        ]);

        $hasil = DB::table('categories')->select('id', 'name')->get();
        self::assertEquals(2, count($hasil));
        $hasil->each(function($record) {
            Log::info(json_encode($record));
        });
    }

    public function testInsertCategories()
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

    public function testWhere()
    {
        $this->testInsertCategories();

        $kolek = DB::table('categories')->where(function(Builder $builder) {
            $builder->where('id', '=', 'GORENG');
            $builder->orWhere('id', '=', 'CIKI');
        })->get();

        self::assertCount(2, $kolek);

        $kolek->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereBetween()
    {
        $this->testInsertCategories();

        $kolek = DB::table('categories')
            ->whereBetween('created_at', ['2024-02-20 00:00:01', '2024-02-20 00:00:10'])
            ->get();

        self::assertCount(5, $kolek);

        $kolek->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereIn()
    {
        $this->testInsertCategories();

        $kolek = DB::table('categories')->whereIn('id',['BAKAR', 'TITIP'])->get();

        self::assertCount(2, $kolek);

        $kolek->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereNull()
    {
        $this->testInsertCategories();

        $kolek = DB::table('categories')->whereNull('description')->get();
        self::assertEquals(0, count($kolek));
        $kolek->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereNotNull()
    {
        $this->testInsertCategories();

        $kolek = DB::table('categories')->whereNotNull('description')->get();
        self::assertEquals(5, count($kolek));
        $kolek->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testWhereTime()
    {
        $this->testInsertCategories();

        $date = DB::table("categories")->whereDate('created_at', '2024-02-20')->get(['id', 'name', 'created_at']);
        $bulan = DB::table("categories")->whereMonth('created_at', '02')->get(['id', 'name', 'created_at']);
        $tahun = DB::table("categories")->whereYear('created_at', '2024')->get(['id', 'name', 'created_at']);
        $day = DB::table("categories")->whereDay('created_at', '20')->get(['id', 'name', 'created_at']);
        $time = DB::table("categories")->whereTime('created_at', '00:00:03')->get(['id', 'name', 'created_at']);

        self::assertCount(5, $date);
        self::assertCount(5, $bulan);
        self::assertCount(5, $tahun);
        self::assertCount(5, $day);
        self::assertCount(1, $time);

        $time->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testUpdate()
    {
        $this->testInsertCategories();

        DB::table("categories")->where("id", "=", "GODOG")->update(["name" => "REBUSAN"]);

        $kolek = DB::table('categories')->where('name', '=', 'PEREBUSAN')->get();

        self::assertCount(1, $kolek);
        $kolek->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testUpsertInsert()
    {
        DB::table('categories')->updateOrInsert(["id" => "ES"], [
            "name" => "PERESAN",
            "description" => "Peresan Duniawi",
            "created_at" => "2024-02-20 14:38:50"
        ]);

        $kolek = DB::table("categories")->where("id", "=", "ES")->get(["id", "name", "created_at"]);
        self::assertCount(1, $kolek);
        $kolek->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testUpsertUpdate()
    {
        $this->testInsertCategories();

        DB::table('categories')->updateOrInsert(["id" => "TITIP"], [
            "name" => "PENITIPAN",
            "description" => "Peresan Duniawi",
            "created_at" => "2024-02-20 14:38:50"
        ]);

        $kolek = DB::table("categories")->where("name", "=", "PENITIPAN")->get(["id", "name", "created_at"]);
        self::assertCount(1, $kolek);

        $kolek->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testIncrement()
    {
        DB::table("counters")->where("id", "=", "sample")->increment("counter", 1);

        $kolek = DB::table("counters")->where("id", "=", "sample")->get();
        self::assertCount(1, $kolek);
        self::assertEquals(1, $kolek[0]->counter);
        $kolek->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testDecrement()
    {
        DB::table('counters')->where("id", "=", "sample")->decrement("counter", 2);

        $kolek = DB::table("counters")->where("id", "=", "sample")->get();
        self::assertCount(1, $kolek);
        self::assertEquals(-1, $kolek[0]->counter);
        $kolek->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testDelete()
    {
        $this->testInsertCategories();

        DB::table('categories')->where("id", "=", "TITIP")->delete();

        $kolek = DB::table("categories")->where("id", "=", "TITIP")->get(["id", "name", "created_at"]);
        self::assertCount(0, $kolek);

        $kolek->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testInsertProducts()
    {
        $this->testInsertCategories();

        DB::table('products')
            ->insert([
                "id" => "1",
                "name" => "Makaroni Pedes",
                "category_id" => "TITIP",
                "price" => 2000
            ]);

        DB::table('products')
            ->insert([
                "id" => "2",
                "name" => "Cireng Isi",
                "category_id" => "GORENG",
                "price" => 2000
            ]);

        DB::table('products')
            ->insert([
                "id" => "3",
                "name" => "Bakso Bakar Spesial",
                "category_id" => "BAKAR",
                "price" => 2000
            ]);
    }

    public function testQueryBuilderJoin()
    {
        $this->testInsertProducts();

        $kolek = DB::table("products")
            ->join("categories", "products.category_id", "=", "categories.id")
            ->select("products.id", "products.name as nama", "categories.name as category_name", "products.price as harga")
            ->get();

        self::assertCount(3, $kolek);
        $kolek->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testOrdering()
    {
        $this->testInsertProducts();

        $kolek = DB::table("products")
            ->orderBy("id", "asc")
            ->orderBy("name", "desc")
            ->get();

        self::assertCount(3, $kolek);
        $kolek->each(function($item) {
            Log::info(json_encode($item));
        });
    }


}
