<?php

use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Todo::class, 10)->create()
            ->each(function ($todo) {
                $todo->withDescription()->save(factory(\App\TodoDescription::class)->make([
                    'todo_id' => $todo->id,
                ]));
            });
    }
}
