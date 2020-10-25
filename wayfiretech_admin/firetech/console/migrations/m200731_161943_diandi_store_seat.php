<?php

use yii\db\Migration;

class m200731_161943_diandi_store_seat extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_store_seat}}', [
            'seat_id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '座位id'",
            'store_id' => "int(11) NULL",
            'name' => "varchar(255) NULL",
            'code' => "varchar(255) NULL COMMENT '座位编码'",
            'images' => "text NULL COMMENT '座位相册'",
            'thumb' => "varchar(255) NULL COMMENT '座位主图'",
            'description' => "varchar(255) NULL COMMENT '座位描述'",
            'status' => "int(10) NULL DEFAULT '1' COMMENT '座位状态:1启用,0禁用'",
            'create_time' => "varchar(0) NULL",
            'update_time' => "varchar(0) NULL",
            'PRIMARY KEY (`seat_id`)'
        ], "ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='座位'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%diandi_store_seat}}',['seat_id'=>'1','store_id'=>'15','name'=>'座位名称','code'=>'4214','images'=>NULL,'thumb'=>'202004/05/bda32976-f173-3301-a68d-9902d9c6a023.jpg','description'=>'共和国那个贺哥','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        $this->insert('{{%diandi_store_seat}}',['seat_id'=>'2','store_id'=>'15','name'=>'座位名称','code'=>'4214','images'=>NULL,'thumb'=>'202004/05/bda32976-f173-3301-a68d-9902d9c6a023.jpg','description'=>'座位描述','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        $this->insert('{{%diandi_store_seat}}',['seat_id'=>'3','store_id'=>'17','name'=>'座位名称','code'=>'4214','images'=>NULL,'thumb'=>'202004/05/bda32976-f173-3301-a68d-9902d9c6a023.jpg','description'=>'343546','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        $this->insert('{{%diandi_store_seat}}',['seat_id'=>'4','store_id'=>'17','name'=>'座位名称','code'=>'4214','images'=>NULL,'thumb'=>'202004/05/bda32976-f173-3301-a68d-9902d9c6a023.jpg','description'=>'座位描述','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_store_seat}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

