<?php
/**
 * Created by PhpStorm.
 * User: dust2
 * Date: 15-1-28
 * Time: 上午10:46
 */
class SharingTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table('sharings')->delete();

        DB::table('sharings')->insert([
            'name' => '用户１在项目1分享',
            'content' => '用户１在项目1的分享',
            'creater_id' => 1,
            'project_id' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        DB::table('sharings')->insert([
            'name' => '用户１在项目2分享',
            'content' => '用户１在项目2的分享',
            'creater_id' => 1,
            'project_id' => 2,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        DB::table('sharings')->insert([
            'name' => '用户2在项目1分享',
            'content' => '用户2在项目１的分享',
            'creater_id' => 2,
            'project_id' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        DB::table('sharings')->insert([
            'name' => '用户2在项目2分享',
            'content' => '用户2在项目2的分享',
            'creater_id' => 2,
            'project_id' => 2,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

    }
}