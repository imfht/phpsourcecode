<?php

use yii\db\Migration;

class m200731_161943_diandi_bloc_conf_wxapp extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_bloc_conf_wxapp}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'bloc_id' => "int(10) unsigned NOT NULL",
            'name' => "varchar(50) NOT NULL COMMENT '公司名称'",
            'description' => "varchar(200) NOT NULL DEFAULT '0' COMMENT '上级商户'",
            'original' => "varchar(200) NOT NULL DEFAULT '0'",
            'AppId' => "varchar(100) NOT NULL COMMENT '省份'",
            'headimg' => "varchar(100) NOT NULL COMMENT '城市'",
            'AppSecret' => "varchar(100) NOT NULL COMMENT '区县'",
            'codeUrl' => "varchar(255) NULL",
            'create_time' => "int(11) NULL",
            'update_time' => "int(11) NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='公司小程序配置'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_bloc_conf_wxapp}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

