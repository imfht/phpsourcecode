<?php

use yii\db\Migration;

class m200731_161943_diandi_coupon_record extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_coupon_record}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'card_id' => "varchar(50) NOT NULL COMMENT '会员卡id'",
            'openid' => "varchar(50) NOT NULL COMMENT 'openid'",
            'friend_openid' => "varchar(50) NOT NULL COMMENT '朋友的openid'",
            'givebyfriend' => "tinyint(3) unsigned NOT NULL COMMENT '朋友的奖励金'",
            'code' => "varchar(50) NOT NULL",
            'hash' => "varchar(32) NOT NULL",
            'addtime' => "int(10) unsigned NOT NULL COMMENT '领取时间'",
            'usetime' => "int(10) unsigned NOT NULL COMMENT '使用时间'",
            'status' => "tinyint(3) NOT NULL COMMENT '状态'",
            'clerk_name' => "varchar(15) NOT NULL",
            'clerk_id' => "int(10) unsigned NOT NULL",
            'store_id' => "int(10) unsigned NOT NULL COMMENT '商家id'",
            'clerk_type' => "tinyint(3) unsigned NOT NULL",
            'couponid' => "int(10) unsigned NOT NULL COMMENT '优惠券id'",
            'uid' => "int(10) unsigned NOT NULL COMMENT '用户id'",
            'grantmodule' => "varchar(255) NOT NULL COMMENT '发放模块'",
            'remark' => "varchar(255) NOT NULL COMMENT '备注'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        $this->createIndex('card_id','{{%diandi_coupon_record}}','card_id',0);
        $this->createIndex('hash','{{%diandi_coupon_record}}','hash',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_coupon_record}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

