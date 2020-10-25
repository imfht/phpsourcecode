<?php

use yii\db\Migration;

class m200731_161943_diandi_video_list extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_video_list}}', [
            'video_id' => "int(11) NOT NULL AUTO_INCREMENT COMMENT '视频id'",
            'cate_id' => "int(11) NULL COMMENT '分类id'",
            'cate_pid' => "int(11) NULL COMMENT '分类父id'",
            'videos' => "varchar(255) NULL COMMENT '视频'",
            'store_id' => "int(11) NULL",
            'bloc_id' => "int(11) NULL",
            'author' => "varchar(255) NULL COMMENT '作者'",
            'likenum' => "int(11) NULL COMMENT '点赞数'",
            'time_length' => "varchar(30) NULL COMMENT '视频时长'",
            'createtime' => "int(30) NULL",
            'updatetime' => "int(30) NULL",
            'PRIMARY KEY (`video_id`)'
        ], "ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='幻灯片'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%diandi_video_list}}',['video_id'=>'1','cate_id'=>NULL,'cate_pid'=>NULL,'videos'=>'202007/11/0682d687-1bd4-3e28-a78a-c337eb383fe0.jpg','store_id'=>NULL,'bloc_id'=>NULL,'author'=>NULL,'likenum'=>NULL,'time_length'=>NULL,'createtime'=>NULL,'updatetime'=>NULL]);
        $this->insert('{{%diandi_video_list}}',['video_id'=>'2','cate_id'=>NULL,'cate_pid'=>NULL,'videos'=>'202007/11/671340bf-408b-3854-b40b-5d0aa03d3942.jpg','store_id'=>NULL,'bloc_id'=>NULL,'author'=>NULL,'likenum'=>NULL,'time_length'=>NULL,'createtime'=>NULL,'updatetime'=>NULL]);
        $this->insert('{{%diandi_video_list}}',['video_id'=>'3','cate_id'=>NULL,'cate_pid'=>NULL,'videos'=>'202007/11/dd6e4f4f-3bf7-3fc6-ac53-51287af721bf.jpg','store_id'=>NULL,'bloc_id'=>NULL,'author'=>NULL,'likenum'=>NULL,'time_length'=>NULL,'createtime'=>NULL,'updatetime'=>NULL]);
        $this->insert('{{%diandi_video_list}}',['video_id'=>'4','cate_id'=>NULL,'cate_pid'=>NULL,'videos'=>'202007/11/664802ea-c6f6-33a9-acfa-c18198360d91.jpg','store_id'=>NULL,'bloc_id'=>NULL,'author'=>NULL,'likenum'=>NULL,'time_length'=>NULL,'createtime'=>NULL,'updatetime'=>NULL]);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_video_list}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

