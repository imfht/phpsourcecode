<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-4
 * Time: 下午3:58
 */
class NotifyInboxTableTestSeeder extends Seeder
{
    public function run()
    {
        DB::table('notifyInbox')->delete();

        DB::table('notifyInbox')->insert([
            'notification_id' => 1,
            'receiver_id' => 1,
            'read' => false,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);
        DB::table('notifyInbox')->insert([
            'notification_id' => 2,
            'receiver_id' => 2,
            'read' => false,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);
        DB::table('notifyInbox')->insert([
            'notification_id' => 3,
            'receiver_id' => 2,
            'read' => true,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);
        DB::table('notifyInbox')->insert([
            'notification_id' => 2,
            'receiver_id' => 1,
            'read' => false,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);
        DB::table('notifyInbox')->insert([
            'notification_id' => 4,
            'receiver_id' => 1,
            'read' => false,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);
        DB::table('notifyInbox')->insert([
            'notification_id' => 4,
            'receiver_id' => 2,
            'read' => false,
            'created_at' => date('Y-m-d'),
            'updated_at' => date('Y-m-d')
        ]);

//        DB::table('notifyInbox')->insert([
//            'notification_id' => 5,
//            'receiver_id' => 1,
//            'read' => false,
//            'created_at' => date('Y-m-d'),
//            'updated_at' => date('Y-m-d')
//        ]);


    }
}