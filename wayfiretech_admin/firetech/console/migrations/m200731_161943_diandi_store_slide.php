<?php

use yii\db\Migration;

class m200731_161943_diandi_store_slide extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_store_slide}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'images' => "varchar(255) NULL COMMENT '图片'",
            'background' => "varchar(255) NULL COMMENT '背景色'",
            'url' => "varchar(255) NOT NULL COMMENT '链接地址'",
            'createtime' => "varchar(30) NULL",
            'updatetime' => "varchar(30) NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='幻灯片'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%diandi_store_slide}}',['id'=>'5','images'=>'202002/02/c5ae51fd-21d7-31bb-99c1-b70547cfa7ff.jpg','background'=>'#e06666','url'=>'','createtime'=>'1580637128','updatetime'=>'1580617100']);
        $this->insert('{{%diandi_store_slide}}',['id'=>'6','images'=>'202002/02/1ee6a69f-2e06-3b9e-ac89-b05cda5c98f9.jpg','background'=>'#d9ead3','url'=>'','createtime'=>'1580637149','updatetime'=>'1580617235']);
        $this->insert('{{%diandi_store_slide}}',['id'=>'7','images'=>'202002/02/f75080df-b020-3e40-92e7-7f294b58f500.jpg','background'=>'#e6b8af','url'=>'','createtime'=>'1580637162','updatetime'=>'1580617248']);
        $this->insert('{{%diandi_store_slide}}',['id'=>'8','images'=>'202004/04/8dbbabe8-e260-3b10-9433-816bb5648c30.jpg','background'=>'#4c1130','url'=>'1','createtime'=>NULL,'updatetime'=>NULL]);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_store_slide}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

