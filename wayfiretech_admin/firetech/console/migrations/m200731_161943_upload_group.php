<?php

use yii\db\Migration;

class m200731_161943_upload_group extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%upload_group}}', [
            'group_id' => "int(11) unsigned NOT NULL AUTO_INCREMENT",
            'group_type' => "varchar(10) NOT NULL DEFAULT ''",
            'group_name' => "varchar(30) NOT NULL DEFAULT ''",
            'sort' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'wxapp_id' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'create_time' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'update_time' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'PRIMARY KEY (`group_id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        $this->createIndex('type_index','{{%upload_group}}','group_type',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%upload_group}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

