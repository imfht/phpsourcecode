<?php

use Illuminate\Database\Seeder;

class BijiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Biji::class,3)->create();
    }
}
