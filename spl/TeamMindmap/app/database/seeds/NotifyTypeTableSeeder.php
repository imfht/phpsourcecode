<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-4
 * Time: 下午3:51
 */
class NotifyTypeTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('notifyTypes')->delete();

        DB::table('notifyTypes')->insert([
            'name' => 'system',
            'label' => '系统通知',
            'map' => 'System'
        ]);
        DB::table('notifyTypes')->insert([
            'name' => 'project',
            'label' => '项目通知',
            'map' => 'Project'
        ]);
        DB::table('notifyTypes')->insert([
            'name' => 'task',
            'label' => '任务通知',
            'map' => 'ProjectTask'
        ]);
        DB::table('notifyTypes')->insert([
            'name' => 'project_discussion',
            'label' => '项目讨论的通知',
            'map' => 'ProjectDiscussion'
        ]);
        DB::table('notifyTypes')->insert([
            'name' => 'project_sharing',
            'label' => '项目分享的通知',
            'map' => 'ProjectSharing'
        ]);
    }
}
