<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-12-23
 * Time: 下午4:37
 */

/**
 * Class ProjectTaskPrioritiesTableSeeder
 *
 * 用于填充项目中任务的优先级信息
 */
class ProjectTaskPrioritiesTableSeeder extends Seeder
{
    public function run()
    {

        DB::table($this->tableName)->delete();

        $this->insertOneRecorder([
           'name'=>'ordinary',
            'label'=>'普通'
        ]);

        $this->insertOneRecorder([
            'name'=>'quickly',
            'label'=>'尽快'
        ]);

        $this->insertOneRecorder([
           'name'=>'urgency',
            'label'=>'紧急'
        ]);
    }

    private function insertOneRecorder($data){
        DB::table($this->tableName)->insert($data);
    }


    private $tableName = 'projectTaskPriorities';
}
