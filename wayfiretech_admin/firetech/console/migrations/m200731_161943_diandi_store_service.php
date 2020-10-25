<?php

use yii\db\Migration;

class m200731_161943_diandi_store_service extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_store_service}}', [
            'service_id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '服务id'",
            'name' => "varchar(255) NULL COMMENT '服务项'",
            'store_id' => "int(11) NULL",
            'info' => "varchar(50) NULL COMMENT '简介'",
            'desc' => "varchar(255) NULL COMMENT '详细说明'",
            'images' => "text NULL COMMENT '服务相册'",
            'content' => "text NULL COMMENT '服务内容'",
            'create_time' => "varchar(30) NULL",
            'update_time' => "varchar(0) NULL",
            'status' => "enum('启用','禁用') NULL DEFAULT '启用'",
            'is_special' => "tinyint(4) NULL COMMENT '是否是特色'",
            'PRIMARY KEY (`service_id`)'
        ], "ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%diandi_store_service}}',['service_id'=>'1','name'=>'服务项目','store_id'=>'15','info'=>'特色说明','desc'=>'服务借鉴说明㕆','images'=>'a:3:{i:0;s:50:"202004/06/4c55bbbc-a4b3-38c5-9ba4-7bfec02b0e7f.jpg";i:1;s:50:"202004/06/2527b43f-9eec-3b3c-b329-bdde1c1e1fb0.jpg";i:2;s:50:"202004/06/a5ef645b-517f-30fc-943c-bc501f5e6be4.jpg";}','content'=>'45435','create_time'=>NULL,'update_time'=>NULL,'status'=>'启用','is_special'=>'1']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_store_service}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

