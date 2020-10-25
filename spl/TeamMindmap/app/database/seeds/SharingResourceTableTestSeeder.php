<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-1-28
 * Time: 上午10:47
 */

class SharingResourceTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table('sharing_resource')->delete();

        DB::table('sharing_resource')->insert([
            'sharing_id' => 1,
            'resource_id' => 1,
        ]);

        DB::table('sharing_resource')->insert([
            'sharing_id' => 2,
            'resource_id' => 2,
        ]);

        DB::table('sharing_resource')->insert([
            'sharing_id' => 3,
            'resource_id' => 3,
        ]);

        DB::table('sharing_resource')->insert([
            'sharing_id' => 4,
            'resource_id' => 4,
        ]);

    }
}
