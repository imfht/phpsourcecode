<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-12-11
 * Time: 上午9:40
 */

class MessagesTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table($this->tableName)->delete();
        DB::table($this->tableName)->insert([
            ['title' => 'test message 1', 'content' => 'content 1', 'sender_id' => 1, 'created_at' => date('Y-m-d h:m:s'), 'updated_at' => date('Y-m-d h:m:s')],
            ['title' => 'test message 2', 'content' => 'content 2', 'sender_id' => 2, 'created_at' => date('Y-m-d h:m:s'), 'updated_at' => date('Y-m-d h:m:s')],
            ['title' => 'test message 3', 'content' => 'content 3', 'sender_id' => 2, 'created_at' => date('Y-m-d h:m:s'), 'updated_at' => date('Y-m-d h:m:s')]
        ]);
    }

    private $tableName = 'messages';
}