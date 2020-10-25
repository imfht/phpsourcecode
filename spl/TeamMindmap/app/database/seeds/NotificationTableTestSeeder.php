<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-4
 * Time: 下午3:50
 */
class NotificationTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table('notifications')->delete();

//        DB::table('notifications')->insert([
//            'type_id' => 1,
//            'title' => 'System',
//            'content' => 'System notification',
//            'trigger_id' => 1,
//            'source_id' => 0,
//            'created_at' => date('Y-m-d'),
//            'updated_at' => date('Y-m-d')
//        ]);
        DB::table('notifications')->insert([
            'type_id' => 2, //这里对应通知类型为项目通知
            'title' => 'Project',
            'content' => 'Project notification',
            'trigger_id' => 1,
            'source_id' => 1, //因此这里为项目id
            'project_id' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);
        DB::table('notifications')->insert([
            'type_id' => 3, //这里对应通知类型为任务通知
            'title' => 'Task',
            'content' => 'Task notification',
            'trigger_id' => 2,
            'source_id' => 3, //因此这里为任务id
            'project_id' => 2,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        DB::table('notifications')->insert([
            'type_id' => 5, //这里对应通知类型为分享通知
            'title' => 'Sharing',
            'content' => 'Project 1 Sharing notification',
            'trigger_id' => 1,
            'source_id' => 1, //因此这里为分享id
            'project_id' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

        DB::table('notifications')->insert([
            'type_id' => 5, //这里对应通知类型为分享通知
            'title' => 'Sharing',
            'content' => 'Project 2 Sharing notification',
            'trigger_id' => 1,
            'source_id' => 2, //因此这里为分享id
            'project_id' => 2,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);
    }
}