<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-25
 * Time: 下午3:40
 */
class ProjectDiscussionCommentsTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table($this->table)->delete();

        DB::table($this->table)->insert([
            'projectDiscussion_id' => 1,
            'content' => 'The 1st comment created by user-1 belongs to discussion-1',
            'creater_id' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);
        DB::table($this->table)->insert([
            'projectDiscussion_id' => 1,
            'content' => 'The 2nd comment created by user-2 belongs to discussion-1',
            'creater_id' => 2,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')

        ]);
        DB::table($this->table)->insert([
            'projectDiscussion_id' => 2,
            'content' => 'The 1st comment created by user-1 belongs to discussion-2',
            'creater_id' => 1,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);
    }

    private $table = 'projectDiscussionComments';
}