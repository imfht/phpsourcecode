<?php

use yii\db\Migration;

class m200731_161943_upload_file extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%upload_file}}', [
            'file_id' => "int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '文件ID'",
            'storage' => "varchar(20) NOT NULL DEFAULT '' COMMENT '对象存储'",
            'group_id' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件分组'",
            'file_url' => "varchar(255) NOT NULL DEFAULT '' COMMENT '文件地址'",
            'file_name' => "varchar(255) NOT NULL DEFAULT '' COMMENT '文件名称'",
            'file_size' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件尺寸'",
            'file_type' => "varchar(20) NOT NULL DEFAULT '' COMMENT '文件类型'",
            'extension' => "varchar(20) NOT NULL DEFAULT '' COMMENT '文件后缀'",
            'is_delete' => "tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除'",
            'bloc_id' => "int(11) unsigned NULL DEFAULT '0' COMMENT '公司ID'",
            'create_time' => "int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'",
            'store_id' => "int(11) NULL COMMENT '商户ID'",
            'PRIMARY KEY (`file_id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        $this->createIndex('path_idx','{{%upload_file}}','file_name',1);
        $this->createIndex('bloc_id','{{%upload_file}}','bloc_id',0);
        $this->createIndex('dd_upload_file_ibfk_3','{{%upload_file}}','store_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%upload_file}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

