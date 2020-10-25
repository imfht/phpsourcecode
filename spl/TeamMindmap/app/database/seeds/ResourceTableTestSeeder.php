<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-1-28
 * Time: 上午10:45
 */
class ResourceTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table('resources')->delete();

        DB::table('resources')->insert([
            'creater_id' => 1,
            'project_id' => 1,
            'filename' => 'logo_picture_creater1_project1_test.png',
            'origin_name' => '静静在项目1工作.png',
            'ext_name' => 'png',
            'mime' => 'image/png',
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        DB::table('resources')->insert([
            'creater_id' => 1,
            'project_id' => 2,
            'filename' => 'logo_picture_creater1_project2_test.png',
            'origin_name' => '静静在项目2工作.png',
            'ext_name' => 'png',
            'mime' => 'image/png',
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        DB::table('resources')->insert([
            'creater_id' => 2,
            'project_id' => 1,
            'filename' => 'logo_picture_creater2_project1_test.png',
            'origin_name' => '静静在想你.png',
            'ext_name' => 'png',
            'mime' => 'image/png',
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        DB::table('resources')->insert([
            'creater_id' => 2,
            'project_id' => 2,
            'filename' => 'logo_picture_creater2_project2_test.png',
            'origin_name' => '静静在想你.png',
            'ext_name' => 'png',
            'mime' => 'image/png',
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);


    }
}