<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-1-28
 * Time: 上午10:49
 */

class ResourceLinkTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table('resourceLinks')->delete();

        DB::table('resourceLinks')->insert([
            'description' => '外链1',
            'resource_ids' => '1|3',
            'link' => 'http:://TeamMindmap/yuiwiw',
            'fetch_code' => 'ksioew',
        ]);

        DB::table('resourceLinks')->insert([
            'description' => '外链1',
            'resource_ids' => '1|3',
            'link' => 'http:://TeamMindmap/hfueis',
            'fetch_code' => 'ijdywm',
        ]);

    }
}