<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $startId = 100;
        for($i=0;$i<20;$i++) {
            factory('App\User')->create([
                'name' => $startId + $i,
                'password' => bcrypt(666),
                'privilege' => 1,
            ]);
        }

    }
}
