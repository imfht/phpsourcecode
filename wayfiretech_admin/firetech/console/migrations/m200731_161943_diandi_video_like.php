<?php

use yii\db\Migration;

class m200731_161943_diandi_video_like extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_video_like}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'video_id' => "int(11) NOT NULL COMMENT '视频id'",
            'user_id' => "int(11) NULL COMMENT '用户id'",
            'store_id' => "int(11) NULL",
            'bloc_id' => "int(11) NULL",
            'createtime' => "int(30) NULL",
            'updatetime' => "int(30) NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='幻灯片'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_video_like}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

