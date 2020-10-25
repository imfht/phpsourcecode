<?php

use yii\db\Migration;

class m200731_161943_diandi_store_share extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_store_share}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'store_id' => "int(11) NOT NULL COMMENT '商户id'",
            'name' => "varchar(255) NULL COMMENT '标题'",
            'thumb' => "varchar(255) NULL COMMENT '内容'",
            'desc' => "varchar(50) NULL COMMENT '描述'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%diandi_store_share}}',['id'=>'3','store_id'=>'15','name'=>'分享标题','thumb'=>'202004/05/ff82651d-00ae-3c75-bd8e-f7285f760538.jpg','desc'=>'分享描述']);
        $this->insert('{{%diandi_store_share}}',['id'=>'4','store_id'=>'16','name'=>'分享标题','thumb'=>'202004/05/ff82651d-00ae-3c75-bd8e-f7285f760538.jpg','desc'=>'分享描述']);
        $this->insert('{{%diandi_store_share}}',['id'=>'5','store_id'=>'17','name'=>'分享标题','thumb'=>'202004/05/ff82651d-00ae-3c75-bd8e-f7285f760538.jpg','desc'=>'分享描述']);
        $this->insert('{{%diandi_store_share}}',['id'=>'6','store_id'=>'18','name'=>'','thumb'=>'','desc'=>'']);
        $this->insert('{{%diandi_store_share}}',['id'=>'7','store_id'=>'19','name'=>'','thumb'=>'','desc'=>'']);
        $this->insert('{{%diandi_store_share}}',['id'=>'8','store_id'=>'20','name'=>'','thumb'=>'','desc'=>'']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_store_share}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

