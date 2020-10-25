<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-11-18
 * Time: 下午7:47
 */
class ProjectTaskStatusTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('projectTaskStatus')->delete();

        DB::table('projectTaskStatus')->insert([
            'name' => 'undo',
            'label' => '未开始',
        ]);
        DB::table('projectTaskStatus')->insert([
            'name' => 'doing',
            'label' => '进行中',
        ]);
        DB::table('projectTaskStatus')->insert([
            'name' => 'finished',
            'label' => '已完成',
        ]);

    }
}
