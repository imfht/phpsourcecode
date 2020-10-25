<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-11-9
 * Time: 上午10:42
 */
class ProjectTaskTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('projectTasks')->delete();

        DB::table('projectTasks')->insert([
            'parent_id'=>null,
            'name'=>'task one',
            'description'=>'task created by user-1',

            'project_id'=>1,
            'creater_id'=>1,
            'last_man'=>1,
            'handler_id'=>2,

            'status_id'=>$this->getStatusIdByName('undo'),
            'expected_at'=>date('Y-m-d'),
            'finished_at'=>date('Y-m-d'),

            'created_at'=>date('Y-m-d'),
            'updated_at'=>date('Y-m-d')
        ]);

        DB::table('projectTasks')->insert([
            'parent_id'=>null,
            'name'=>'task two',
            'description'=>'task created by user-2',

            'project_id'=>2,
            'creater_id'=>2,
            'last_man'=>2,
            'handler_id'=>2,

            'status_id'=>$this->getStatusIdByName('undo'),
            'expected_at'=>date('Y-m-d'),
            'finished_at'=>date('Y-m-d'),

            'created_at'=>date('Y-m-d'),
            'updated_at'=>date('Y-m-d')
        ]);

        DB::table('projectTasks')->insert([
            'parent_id'=>1,
            'name'=>'task dust',
            'description'=>'subtask created by user-1',

            'project_id'=>1,
            'creater_id'=>1,
            'last_man'=>2,
            'handler_id'=>2,

            'status_id'=>$this->getStatusIdByName('doing'),
            'expected_at'=>date('Y-m-d'),
            'finished_at'=>date('Y-m-d'),

            'created_at'=>date('Y-m-d'),
            'updated_at'=>date('Y-m-d')
        ]);

        DB::table('projectTasks')->insert([
            'parent_id'=>1,
            'name'=>'task dust doing 2',
            'description'=>'subtask created by user-1',

            'project_id'=>1,
            'creater_id'=>1,
            'last_man'=>2,
            'handler_id'=>1,

            'status_id'=>$this->getStatusIdByName('doing'),
            'expected_at'=>date('Y-m-d'),
            'finished_at'=>date('Y-m-d'),

            'created_at'=>date('Y-m-d'),
            'updated_at'=>date('Y-m-d')
        ]);

        DB::table('projectTasks')->insert([
            'parent_id'=>2,
            'name'=>'task dust2',
            'description'=>'subtask created by user-2',

            'project_id'=>2,
            'creater_id'=>2,
            'last_man'=>1,
            'handler_id'=>2,

            'status_id'=>$this->getStatusIdByName('doing'),
            'expected_at'=>date('Y-m-d'),
            'finished_at'=>date('Y-m-d'),

            'created_at'=>date('Y-m-d'),
            'updated_at'=>date('Y-m-d')
        ]);
    }

    private function getStatusIdByName($statusName)
    {
        return ProjectTaskStatus::where('name', $statusName)->firstOrFail()['id'];
    }


}