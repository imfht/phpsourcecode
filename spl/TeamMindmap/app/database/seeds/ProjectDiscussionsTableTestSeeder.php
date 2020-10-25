<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-12-25
 * Time: 上午12:46
 */

class ProjectDiscussionsTableTestSeeder extends Seeder
{
    public function run()
    {

        DB::table('projectDiscussions')->insert([
            ['title'=>'discussion one', 'content'=>'content one', 'project_id'=>'1', 'creater_id'=>'1', 'created_at'=>$this->getDatetime(), 'updated_at'=>$this->getDatetime()],
        ]);
        DB::table('projectDiscussions')->insert([
            ['title'=>'discussion two', 'content'=>'content two', 'project_id'=>'2', 'creater_id'=>'2', 'created_at'=>$this->getDatetime(), 'updated_at'=>$this->getDatetime()]
        ]);
        DB::table('projectDiscussions')->insert([
            ['title'=>'discussion three', 'content'=>'content three', 'project_id'=>'1', 'creater_id'=>'1', 'created_at'=>$this->getDatetime(), 'updated_at'=>$this->getDatetime()]
        ]);
    }

    private function getDatetime()
    {
        return date('Y-m-d h:m:s');
    }
}