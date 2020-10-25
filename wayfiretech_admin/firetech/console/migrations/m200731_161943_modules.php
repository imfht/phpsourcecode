<?php

use yii\db\Migration;

class m200731_161943_modules extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%modules}}', [
            'mid' => "int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '模块id'",
            'name' => "varchar(100) NOT NULL COMMENT '英文标识'",
            'type' => "enum('base','business','marketing','member','other','system','enterprise','services') NOT NULL DEFAULT 'base' COMMENT '模块类型'",
            'title' => "varchar(100) NOT NULL COMMENT '名称'",
            'version' => "varchar(15) NOT NULL COMMENT '版本'",
            'identifie' => "varchar(255) NULL",
            'ability' => "varchar(500) NOT NULL COMMENT '简介'",
            'description' => "varchar(1000) NOT NULL COMMENT '描述'",
            'author' => "varchar(50) NOT NULL COMMENT '作者'",
            'url' => "varchar(255) NOT NULL COMMENT '社区地址'",
            'settings' => "tinyint(1) NOT NULL COMMENT '配置'",
            'logo' => "varchar(250) NOT NULL COMMENT 'logo'",
            'PRIMARY KEY (`mid`)'
        ], "ENGINE=MyISAM  DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        $this->createIndex('idx_name','{{%modules}}','name',0);
        
        
        /* 表数据 */
        $this->insert('{{%modules}}',['mid'=>'17','name'=>'diandi_shop','type'=>'business','title'=>'店滴会员卡','version'=>'1.0.0','identifie'=>NULL,'ability'=>'店滴会员卡','description'=>'店滴会员卡','author'=>'chunchun','url'=>'23','settings'=>'1','logo'=>'202002/16/b4b9135c-5c04-38bf-ae78-2eb751fb428a.png']);
        $this->insert('{{%modules}}',['mid'=>'18','name'=>'Nihao','type'=>'business','title'=>'你好','version'=>'1.0.0','identifie'=>NULL,'ability'=>'简介','description'=>'描述','author'=>'王春生','url'=>'www','settings'=>'1','logo'=>'202002/23/3c29f377-bd1e-3587-a345-fe9af72cad42.png']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%modules}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

