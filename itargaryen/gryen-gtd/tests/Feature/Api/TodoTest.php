<?php

namespace Tests\Feature\Api;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    private static $COUNT = 10;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(\App\User::class)->create();

        factory(\App\Todo::class, self::$COUNT)->create()
            ->each(function ($todo) {
                $todo->withDescription()->save(factory(\App\TodoDescription::class)->make([
                    'todo_id' => $todo->id,
                ]));
            });
    }

    public function testGetList()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/todos/list?page=1&status=all');

        $response->assertSuccessful();

        $this->assertTrue($response->json('total') === self::$COUNT);
    }

    public function testUpdateOrCreate()
    {
        $faker = \Faker\Factory::create();
        $beginAt = Carbon::now();
        $endAt = Carbon::now()->addDay($faker->randomDigit);

        $postData = [
            'content' => $faker->text,
            'importance' => $faker->numberBetween(0, 2),
            'begin_at' => $beginAt,
            'end_at' => $endAt,
            'description' => $faker->text,
        ];

        $response = $this->actingAs($this->user)
            ->post('/api/todos/updateorcreate', $postData);

        $response->assertSuccessful();
        $this->assertTrue(\DB::table('todos')->count() === self::$COUNT + 1);
    }

    public function testUpdateTodo()
    {
        $todo = \DB::table('todos')->first();
        $beforeStatus = $todo->status;
        $postData = [
            'id' => $todo->id,
            'event' => $beforeStatus === 1 ? 'check' : 'start',
        ];

        $response = $this->actingAs($this->user)
            ->post('/api/todos/update', $postData);

        $response->assertSuccessful();

        $afterStatus = \DB::table('todos')->find($todo->id);

        $this->assertTrue($beforeStatus !== $afterStatus);
    }

    public function testDeleteTodo()
    {
        $todo = \DB::table('todos')->first();

        $response = $this->actingAs($this->user)
            ->post('/api/todos/delete/'.$todo->id);
        $response->assertSuccessful();

        $afterDeleteTodo = \DB::table('todos')->find($todo->id);

        $this->assertFalse(empty($afterDeleteTodo->deleted_at));
    }
}
