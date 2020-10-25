<?php

use yii\db\Migration;

class m200731_161943_diandi_video_slide extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_video_slide}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'video_id' => "int(11) NULL COMMENT '视频id'",
            'images' => "varchar(255) NULL COMMENT '幻灯片'",
            'store_id' => "int(11) NULL",
            'bloc_id' => "int(11) NULL",
            'createtime' => "int(30) NULL",
            'updatetime' => "int(30) NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='幻灯片'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%diandi_video_slide}}',['id'=>'1','video_id'=>NULL,'images'=>'202007/11/0682d687-1bd4-3e28-a78a-c337eb383fe0.jpg','store_id'=>NULL,'bloc_id'=>NULL,'createtime'=>NULL,'updatetime'=>NULL]);
        $this->insert('{{%diandi_video_slide}}',['id'=>'2','video_id'=>NULL,'images'=>'202007/11/671340bf-408b-3854-b40b-5d0aa03d3942.jpg','store_id'=>NULL,'bloc_id'=>NULL,'createtime'=>NULL,'updatetime'=>NULL]);
        $this->insert('{{%diandi_video_slide}}',['id'=>'3','video_id'=>NULL,'images'=>'202007/11/dd6e4f4f-3bf7-3fc6-ac53-51287af721bf.jpg','store_id'=>NULL,'bloc_id'=>NULL,'createtime'=>NULL,'updatetime'=>NULL]);
        $this->insert('{{%diandi_video_slide}}',['id'=>'4','video_id'=>NULL,'images'=>'202007/11/664802ea-c6f6-33a9-acfa-c18198360d91.jpg','store_id'=>NULL,'bloc_id'=>NULL,'createtime'=>NULL,'updatetime'=>NULL]);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_video_slide}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

