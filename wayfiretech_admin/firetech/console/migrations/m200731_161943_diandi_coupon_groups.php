<?php

use yii\db\Migration;

class m200731_161943_diandi_coupon_groups extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_coupon_groups}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'couponid' => "varchar(255) NOT NULL DEFAULT '' COMMENT '优惠券id'",
            'groupid' => "int(10) NOT NULL COMMENT '分组id'",
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
        $this->dropTable('{{%diandi_coupon_groups}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

