<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-10-25
 * Time: 上午10:30
 */

class ProjectMemberTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table('project_member')->truncate();
        DB::table('project_member')->insert([
            ['project_id'=>'1', 'member_id'=>'2', 'role_id'=>'1',  'created_at'=>date('Y-m-d'), 'updated_at'=>date('Y-m-d')],
            ['project_id'=>'1', 'member_id'=>'3', 'role_id'=>'2',  'created_at'=>date('Y-m-d'), 'updated_at'=>date('Y-m-d')],
            ['project_id'=>'2', 'member_id'=>'1', 'role_id'=>'1',  'created_at'=>date('Y-m-d'), 'updated_at'=>date('Y-m-d')]
        ]);
    }
}