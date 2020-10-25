<?php

use yii\db\Migration;

class m200731_161943_message_notice_log extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%message_notice_log}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'bloc_id' => "int(11) NULL COMMENT '公司id'",
            'store_id' => "int(11) NULL COMMENT '商户id'",
            'message' => "varchar(255) NOT NULL COMMENT '消息内容'",
            'is_read' => "tinyint(3) NOT NULL COMMENT '是否阅读'",
            'user_id' => "int(11) NOT NULL COMMENT '用户'",
            'sign' => "varchar(22) NOT NULL",
            'type' => "tinyint(3) NOT NULL COMMENT '消息类型'",
            'status' => "tinyint(3) NULL COMMENT '消息状态'",
            'create_time' => "int(11) NOT NULL",
            'end_time' => "int(11) NOT NULL",
            'url' => "varchar(255) NOT NULL COMMENT '链接地址'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%message_notice_log}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

