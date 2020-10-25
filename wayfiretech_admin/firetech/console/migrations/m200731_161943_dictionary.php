<?php

use yii\db\Migration;

class m200731_161943_dictionary extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%dictionary}}', [
            'id' => "int(11) unsigned NOT NULL AUTO_INCREMENT",
            'type' => "varchar(30) NOT NULL DEFAULT ''",
            'name' => "varchar(255) NOT NULL DEFAULT ''",
            'value' => "varchar(255) NOT NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%dictionary}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

