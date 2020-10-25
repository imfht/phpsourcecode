<?php

use yii\db\Migration;

class m200731_161943_diandi_coupon_store extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_coupon_store}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'uniacid' => "int(10) NOT NULL",
            'couponid' => "varchar(255) NOT NULL DEFAULT ''",
            'storeid' => "int(10) unsigned NOT NULL DEFAULT '0'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        $this->createIndex('couponid','{{%diandi_coupon_store}}','couponid',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_coupon_store}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

