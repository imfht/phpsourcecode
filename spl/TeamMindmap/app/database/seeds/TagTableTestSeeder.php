<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-1-28
 * Time: 上午10:48
 */

class TagTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table('tags')->delete();

        DB::table('tags')->insert([
            'name' => 'tag_one_in_project1',
            'project_id' => 1,
            'created_at'=>date('Y-m-d h:m:s'),
            'updated_at'=>date('Y-m-d h:m:s')
        ]);

        DB::table('tags')->insert([
            'name' => 'tag_two_in_project1',
            'project_id' => 1,
            'created_at'=>date('Y-m-d h:m:s'),
            'updated_at'=>date('Y-m-d h:m:s')
        ]);


        DB::table('tags')->insert([
            'name' => 'tag_one_in_project2',
            'project_id' => 2,
            'created_at'=>date('Y-m-d h:m:s'),
            'updated_at'=>date('Y-m-d h:m:s')
        ]);

        DB::table('tags')->insert([
            'name' => 'tag_two_in_project2',
            'project_id' => 2,
            'created_at'=>date('Y-m-d h:m:s'),
            'updated_at'=>date('Y-m-d h:m:s')
        ]);


    }
}