<?php

use yii\db\Migration;

class m200731_161943_diandi_store_record extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_store_record}}', [
            'id' => "int(11) NOT NULL",
            'user_id' => "int(11) NULL COMMENT '用户id'",
            'create_time' => "int(11) NULL",
            'update_time' => "int(11) NULL",
            'store_id' => "int(11) NULL COMMENT '商家id'",
            'num' => "int(11) NULL COMMENT '浏览次数'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_store_record}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

