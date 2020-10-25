<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $this->get('/')
            ->assertSuccessful()
            ->assertSeeText(env('APP_NAME'));
    }
}
