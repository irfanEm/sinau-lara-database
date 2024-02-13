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
        DB::delete("DELETE from products");
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

    public function insertProducts()
    {
        $this->testQueryBuilderWhere();

        DB::table('products')->insert([
            'id' => '1',
            'name' => 'Komo',
            'category_id' => 'CIKI',
            'price' => 5000
        ]);

        DB::table('products')->insert([
            'id' => '4',
            'name' => 'Top1',
            'category_id' => 'CIKI',
            'price' => 1000
        ]);

        DB::table('products')->insert([
            'id' => '2',
            'name' => 'Xireng',
            'category_id' => 'GORENG',
            'price' => 2000
        ]);

        DB::table('products')->insert([
            'id' => '3',
            'name' => 'Nugget',
            'category_id' => 'GORENG',
            'price' => 2000
        ]);
    }

    public function testBuilderQueryJoin()
    {
        $this->insertProducts();

        $koleksi = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.id', 'products.name', 'categories.id as category_name', 'products.price')
            ->get();
        

        self::assertCount(3, $koleksi);
        $koleksi->each(function($hasil){
            Log::info(json_encode($hasil));
        });
    }

    public function testOrdering()
    {
        $this->insertProducts();

        $koleksi = DB::table('products')
            ->orderBy('name', 'asc')
            ->orderBy('price', 'desc')
            ->get();
        
            self::assertCount(3, $koleksi);
            $koleksi->each(function($hasil){
                Log::info(json_encode($hasil));
            });
    }

    public function testPagging()
    {
        $this->insertProducts();

        $koleksi = DB::table('products')
            ->skip(2)
            ->take(1)
            ->get();

        self::assertCount(1, $koleksi);
        $koleksi->each(function($hasil){
            Log::info(json_encode($hasil));
        });
    }

    public function testPagging2()
    {
        $this->testQueryBuilderWhere();

        $koleksi = DB::table('categories')
        ->skip(2)
        ->take(2)
        ->get();

        self::assertCount(2, $koleksi);
        $koleksi->each(function($hasil){
            Log::info(json_encode($hasil));
        });
    }

    public function insertManyCategories()
    {
        for($i = 1; $i<=100; $i++)
        {
            DB::table('categories')->insert([
                "id" => "CATEGORY-$i", 
                "name" => "Kategori $i",
                "created_at" => "2024-02-05 00:00:00"
            ]);
        }
    }

    public function testChunk()
    {
        $this->insertManyCategories();

        DB::table('categories')
        ->orderBy('id')
        ->chunk(10, function($kategori2){
            self::assertNotNull($kategori2);
            Log::info("start Chunk");
            $kategori2->each(function($kategori){
                Log::info(json_encode($kategori));
            });
            Log::info("End Chunk");
        });
    }

    public function testLazy()
    {
        $this->insertManyCategories();

        DB::table('categories')->orderBy('id')
        ->lazy(10)
        ->take(4)
        ->each(function($koleksi){
            self::assertNotNull($koleksi);
            Log::info(json_encode($koleksi));
        });
    }

    public function testCursor()
    {
        $this->insertManyCategories();

        $koleksi = DB::table('categories')->orderBy('id')->cursor();
        $koleksi->each(function($item){
            self::assertNotNull($item);
            Log::info(json_encode($item));
        });
    }

    public function testAggregate()
    {
        $this->insertProducts();

        $collection = DB::table('products')->count('id');
        self::assertEquals(3, $collection);

        $collection = DB::table('products')->max('price');
        self::assertEquals(5000, $collection);

        $collect = DB::table('products')->min('price');
        self::assertEquals(2000, $collect);

        $collect = DB::table('products')->sum('price');
        self::assertEquals(9000, $collect);

        $collect = DB::table('products')->avg('price');
        self::assertEquals(3000, $collect);
    }

    public function testQueryBuilderRawAggregate()
    {
        $this->insertProducts();

        $collect = DB::table('products')
            ->select(
                DB::raw('count(*) as total_product'),
                DB::raw('min(price) as min_price'),
                DB::raw('max(price) as max_price')
            )
            ->get();

        // dd($collect);
        self::assertEquals(3, $collect[0]->total_product);
        self::assertEquals(2000, $collect[0]->min_price);
        self::assertEquals(5000, $collect[0]->max_price);
    }

    public function testQueryBuilderGrouping()
    {
        $this->insertProducts();

        $collect = DB::table('products')
            ->select('category_id', DB::raw('count(*) as total_product'))
            ->groupBy('category_id')
            ->orderBy('category_id')
            ->get();

        // dd($collect);
        self::assertCount(2, $collect);
        self::assertEquals('CIKI', $collect[0]->category_id);
        self::assertEquals('GORENG', $collect[1]->category_id);
        self::assertEquals(2, $collect[0]->total_product);
        self::assertEquals(2, $collect[1]->total_product);
    }

    public function testQueryBuilderHaving()
    {
        $this->insertProducts();

        $collect = DB::table('products')
            ->select('category_id', DB::raw('count(*) as category_id'))
            ->groupBy('category_id')
            ->orderBy('category_id', 'desc')
            ->having(DB::raw('count(*)'), '=', 2)
            ->get();

            self::assertCount(2, $collect);
            self::assertEquals(2, $collect[0]->category_id);
            self::assertEquals(2, $collect[1]->category_id);
    }

    public function testQueryBuilderLockForUpdate()
    {
        $this->insertProducts();

        DB::transaction(function(){
            $collect = DB::table('products')
                ->where('id', '=', '1')
                ->lockForUpdate()
                ->get();
            
            self::assertCount(1, $collect);
        });
    }

    public function testPagination()
    {
        $this->insertProducts();

        $paginate = DB::table('products')->paginate(perPage:2, page:2);

        self::assertEquals(2, $paginate->currentPage());
        self::assertEquals(4, $paginate->total());
        self::assertEquals(2, $paginate->lastPage());
        self::assertEquals(2, $paginate->perPage());

        $collect = $paginate->items();
        self::assertCount(2, $collect);
        foreach($collect as $item)
        {
            Log::info(json_encode($item));
        }
    }

    public function testIterasiPerPage()
    {
        $this->insertProducts();

        $page = 1;
        while(true){
            $paginate = DB::table('products')->paginate(perPage:2, page: $page);
            if($paginate->isEmpty())
            {
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

    public function testQueryBuilderCursorPagination()
    {
        $this->insertProducts();

        $cursor = 'id';
        while(true){

            $paginate = DB::table('categories')->orderBy('id')->cursorPaginate(perPage:2, cursor:$cursor);

            foreach($paginate->items() as $item){
                self::assertNotNull($item);
                Log::info(json_encode($item));
            }

            $cursor = $paginate->nextCursor();
            if($cursor == null){
                break;
            }
        }
    }
}
    