<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-11-9
 * Time: 下午4:18
 */
class ProjectTaskMemberTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('projectTask_member')->delete();

        DB::table('projectTask_member')->insert([
            'task_id'=>1,
            'member_id'=>3,
            'created_at'=>date('Y-m-d'),
            'updated_at'=>date('Y-m-d')
        ]);

        DB::table('projectTask_member')->insert([
            'task_id'=>2,
            'member_id'=>1,
            'created_at'=>date('Y-m-d'),
            'updated_at'=>date('Y-m-d')
        ]);

        DB::table('projectTask_member')->insert([
            'task_id'=>3,
            'member_id'=>3,
            'created_at'=>date('Y-m-d'),
            'updated_at'=>date('Y-m-d')
        ]);

        DB::table('projectTask_member')->insert([
            'task_id'=>4,
            'member_id'=>1,
            'created_at'=>date('Y-m-d'),
            'updated_at'=>date('Y-m-d')
        ]);

        DB::table('projectTask_member')->insert([
            'task_id'=>5,
            'member_id'=>1,
            'created_at'=>date('Y-m-d'),
            'updated_at'=>date('Y-m-d')
        ]);

    }
}