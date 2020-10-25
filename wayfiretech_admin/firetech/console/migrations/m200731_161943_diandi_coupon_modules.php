<?php

use yii\db\Migration;

class m200731_161943_diandi_coupon_modules extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_coupon_modules}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'couponid' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券id'",
            'module' => "varchar(30) NOT NULL COMMENT '模块标识'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        $this->createIndex('cid','{{%diandi_coupon_modules}}','couponid',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_coupon_modules}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

