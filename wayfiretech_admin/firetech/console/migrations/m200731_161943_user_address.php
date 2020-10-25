<?php

use yii\db\Migration;

class m200731_161943_user_address extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%user_address}}', [
            'address_id' => "int(11) unsigned NOT NULL AUTO_INCREMENT",
            'name' => "varchar(30) NOT NULL DEFAULT ''",
            'phone' => "varchar(20) NOT NULL DEFAULT ''",
            'province_id' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'city_id' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'region_id' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'detail' => "varchar(255) NOT NULL DEFAULT ''",
            'user_id' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'wxapp_id' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'is_default' => "tinyint(4) NULL DEFAULT '0'",
            'create_time' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'update_time' => "int(11) unsigned NOT NULL DEFAULT '0'",
            'PRIMARY KEY (`address_id`)'
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
        $this->dropTable('{{%user_address}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

