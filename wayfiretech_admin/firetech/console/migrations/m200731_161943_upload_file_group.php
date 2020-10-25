<?php

use yii\db\Migration;

class m200731_161943_upload_file_group extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%upload_file_group}}', [
            'group_id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分组ID'",
            'user_id' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID'",
            'store_id' => "int(11) NULL COMMENT '商户ID'",
            'bloc_id' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '公司ID'",
            'create_time' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'PRIMARY KEY (`group_id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        $this->createIndex('dd_upload_file_group_ibfk_2','{{%upload_file_group}}','bloc_id',0);
        $this->createIndex('dd_upload_file_group_ibfk_3','{{%upload_file_group}}','store_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%upload_file_group}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

