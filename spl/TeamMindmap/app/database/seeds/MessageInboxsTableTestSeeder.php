<?php
/**
 * Created by PhpStorm.
 * User: spatra
 * Date: 14-12-11
 * Time: 上午9:59
 */

/**
 * Class MessageInboxsTableTestSeeder
 */
class MessageInboxsTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table($this->tableName)->delete();

        DB::table($this->tableName)->insert([
          ['message_id' => 1, 'receiver_id' => 2, 'read' => false, 'created_at'=>date('Y-m-d h:m:s'), 'updated_at'=>date('Y-m-d h:m:s')],
          ['message_id' => 2, 'receiver_id' => 1, 'read' => true, 'created_at'=>date('Y-m-d h:m:s'), 'updated_at'=>date('Y-m-d h:m:s')],
          ['message_id' => 3, 'receiver_id' => 1, 'read' => false, 'created_at'=>date('Y-m-d h:m:s'), 'updated_at'=>date('Y-m-d h:m:s')]
        ]);

    }

    protected  $tableName = 'messagesInboxs';
}