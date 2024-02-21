<?php

namespace Tests\Feature;

use Database\Seeders\CategorySeeder;
use Database\Seeders\CounterSeeder;
use Illuminate\Database\Query\Builder;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function PHPUnit\Framework\assertNotNull;

class QueryBuilderTest2 extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from counters');
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
        $this->seed(CategorySeeder::class);
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

        $kolek = DB::table('categories')->where('name', '=', 'REBUSAN')->get();

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
        $this->seed(CounterSeeder::class);

        DB::table('counters')->where("id", "=", "sample")->increment("counter", 1);

        $kolek = DB::table("counters")->where("id", "=", "sample")->get();
        self::assertCount(1, $kolek);
        self::assertEquals(1, $kolek[0]->counter);
        $kolek->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testDecrement()
    {
        $this->seed(CounterSeeder::class);

        DB::table('counters')->where("id", "=", "sample")->decrement("counter", 2);

        $kolek = DB::table("counters")->where("id", "=", "sample")->get();
        self::assertCount(1, $kolek);
        self::assertEquals(-2, $kolek[0]->counter);
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
                "price" => 1500
            ]);

        DB::table('products')
            ->insert([
                "id" => "2",
                "name" => "Cireng Isi",
                "category_id" => "GORENG",
                "price" => 1000
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

    public function testPaginationMnl()
    {
        $this->testInsertCategories();

        $kolek = DB::table("categories")->skip(2)->take(2)->get();
        self::assertCount(2, $kolek);
        $kolek->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testInsertBanyakCategories()
    {
        for($i=1; $i<=100; $i++)
        {
            DB::table('categories')
                ->insert([
                    "id" => "ID$i",
                    "name" => "name$i",
                    "created_at" => "2024-02-21 00:00:00"
                ]);
        }
    }

    public function testChunk()
    {
        $this->testInsertBanyakCategories();

        DB::table("categories")
                ->orderBy("id")
                ->chunk(10, function($kolek){
                    self::assertNotNull($kolek);
                    Log::info("Awal Chunk");
                    $kolek->each(function($item) {
                        Log::info(json_encode($item));
                    });
                    Log::info(json_encode("Akhir Chunk"));
                });
    }

    public function testLazy()
    {
        $this->testInsertBanyakCategories();

        $lazy = DB::table("categories")->orderBy("id")->lazy(10)->take(5);
        self::assertNotNull($lazy);

        $lazy->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testCursor()
    {
        $this->testInsertBanyakCategories();
        
        $cursor = DB::table("categories")->orderBy("id")->cursor();
        self::assertNotNull($cursor);

        $cursor->each(function($item) {
            Log::info(json_encode($item));
        });
    }

    public function testAgregate()
    {
        $this->testInsertProducts();

        $hasil = DB::table("products")->count("id");
        self::assertEquals(3, $hasil);

        $hasil = DB::table("products")->min("price");
        self::assertEquals(1000, $hasil);

        $hasil = DB::table("products")->max("price");
        self::assertEquals(2000, $hasil);

        $hasil = DB::table("products")->avg("price");
        self::assertEquals(1500, $hasil);

        $hasil = DB::table("products")->sum("price");
        self::assertEquals(4500, $hasil);
    }

    public function testRaw()
    {
        $this->testInsertProducts();

        $collect = DB::table('products')
                ->select(
                    DB::raw("count(id) as total_product"),
                    DB::raw("min(price) as min_price"),
                    DB::raw("max(price) as max_price"),
                    DB::raw("sum(price) as total_price")
                )->get();

        self::assertNotNull($collect);
        self::assertEquals(3, $collect[0]->total_product);
        self::assertEquals(1000, $collect[0]->min_price);
        self::assertEquals(2000, $collect[0]->max_price);
        self::assertEquals(4500, $collect[0]->total_price);
    }

    public function testGrouping()
    {
        $this->testInsertProducts();

        $collect = DB::table('products')
            ->select("category_id", DB::raw("count(id) as total_product"))
            ->groupBy("category_id")
            ->orderBy("category_id", "asc")
            ->get();

            self::assertCount(3, $collect);
            self::assertEquals(1, $collect[0]->total_product);
            self::assertEquals(1, $collect[1]->total_product);
            self::assertEquals(1, $collect[2]->total_product);

            self::assertEquals("BAKAR", $collect[0]->category_id);
            self::assertEquals("GORENG", $collect[1]->category_id);
            self::assertEquals("TITIP", $collect[2]->category_id);
    }

    public function testHaving()
    {
        $this->testInsertProducts();

        $collect = DB::table('products')
            ->select("category_id", DB::raw("count(id) as total_product"))
            ->groupBy("category_id")
            ->orderBy("category_id", "asc")
            ->having("category_id", "=", "TITIP")
            ->get();

            self::assertCount(1, $collect);
    }

    public function testLocking()
    {
        $this->testInsertProducts();

        DB::transaction(function(){
            $collect = DB::table("products")->where("id", "=", "3")->lockForUpdate()->get();

            self::assertCount(1, $collect);
        });
    }

    public function testPagination()
    {
        $this->testInsertProducts();

        $paginate = DB::table("products")->paginate(perPage:2, columns:["id", "name"], pageName:"test", page:2);

        self::assertEquals(2, $paginate->currentPage());
        self::assertEquals(2, $paginate->lastPage());
        self::assertEquals(2, $paginate->perPage());
        self::assertEquals(3, $paginate->total());

        $collect = $paginate->items();
        foreach($collect as $item)
        {
            Log::info(json_encode($item));
        }
    }

    public function testIteratePage()
    {
        $this->testInsertProducts();

        $page = 1;
        while(true){

            $paginate = DB::table("products")->paginate(perPage:1, columns:["id", "name"], pageName:"test$page", page:$page);
    
            if($paginate->isEmpty()){
                break;
            }else{
                $page++;
                foreach($paginate->items() as $item){
                    self::assertNotNull($item);
                    Log::info(json_encode($item));
                }
            }
        }

    }

    public function testCursorPaginator()
    {
        $this->testInsertProducts();
        
        $cursor = "id";
        while(true){

            $collect = DB::table("products")->orderBy("id")->cursorPaginate(perPage:1, cursor:$cursor);
            foreach($collect->items() as $item)
            {
                assertNotNull($item);
                Log::info(json_encode($item));
            }

            $cursor = $collect->nextCursor();
            if($cursor == null){
                break;
            }
        }
    }
}
