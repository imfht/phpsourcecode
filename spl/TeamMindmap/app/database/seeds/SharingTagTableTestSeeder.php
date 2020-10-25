<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-1-28
 * Time: 上午10:48
 */

class SharingTagTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table('sharing_tag')->delete();

        DB::table('sharing_tag')->insert([
            'sharing_id' => 1,
            'tag_id' => 1,
        ]);

        DB::table('sharing_tag')->insert([
            'sharing_id' => 2,
            'tag_id' => 4,
        ]);

        DB::table('sharing_tag')->insert([
            'sharing_id' => 3,
            'tag_id' => 2,
        ]);

        DB::table('sharing_tag')->insert([
            'sharing_id' => 4,
            'tag_id' => 3
        ]);
    }
}