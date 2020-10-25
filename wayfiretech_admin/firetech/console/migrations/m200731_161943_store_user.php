<?php

use yii\db\Migration;

class m200731_161943_store_user extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%store_user}}', [
            'store_user_id' => "int(11) unsigned NOT NULL AUTO_INCREMENT",
            'user_name' => "varchar(255) NOT NULL DEFAULT ''",
            'password' => "varchar(255) NOT NULL DEFAULT ''",
            'wxapp_id' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'create_time' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'update_time' => "int(11) NOT NULL",
            'PRIMARY KEY (`store_user_id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        $this->createIndex('user_name','{{%store_user}}','user_name',1);
        
        
        /* 表数据 */
        $this->insert('{{%store_user}}',['store_user_id'=>'10001','user_name'=>'yoshop_4','password'=>'0d1d5540a26597aef3b5dc528d29bc70','wxapp_id'=>'4','create_time'=>'1574520971','update_time'=>'1574520971']);
        $this->insert('{{%store_user}}',['store_user_id'=>'10002','user_name'=>'yoshop_5','password'=>'23bd30045648d1ad3536031e3e76e6d0','wxapp_id'=>'5','create_time'=>'1574570697','update_time'=>'1574570697']);
        $this->insert('{{%store_user}}',['store_user_id'=>'10003','user_name'=>'yoshop_7','password'=>'f94df1ff7b85d12a0cd890a8a3d3b45a','wxapp_id'=>'7','create_time'=>'1575803519','update_time'=>'1575803519']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%store_user}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

