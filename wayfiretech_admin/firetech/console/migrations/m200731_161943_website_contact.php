<?php

use yii\db\Migration;

class m200731_161943_website_contact extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%website_contact}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'name' => "varchar(255) NULL",
            'contact' => "varchar(255) NULL",
            'createtime' => "varchar(30) NULL",
            'updatetime' => "varchar(30) NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='幻灯片'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%website_contact}}',['id'=>'19','name'=>'陈先生','contact'=>'18628258028','createtime'=>'1587458261','updatetime'=>'1587458261']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%website_contact}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

