<?php

use yii\db\Migration;

class m200731_161943_upload_file_used extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%upload_file_used}}', [
            'used_id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id'",
            'file_id' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件id'",
            'bloc_id' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '公司id'",
            'store_id' => "int(11) NULL COMMENT '商户id'",
            'create_time' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'PRIMARY KEY (`used_id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        $this->createIndex('file_id','{{%upload_file_used}}','file_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%upload_file_used}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

