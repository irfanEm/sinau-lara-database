<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RawQueryTestPart2 extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from categories');
    }
}
