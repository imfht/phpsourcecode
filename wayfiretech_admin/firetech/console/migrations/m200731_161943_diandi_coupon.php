<?php

use yii\db\Migration;

class m200731_161943_diandi_coupon extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_coupon}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'card_id' => "varchar(50) NOT NULL",
            'type' => "varchar(15) NOT NULL COMMENT '卡券类型'",
            'logo_url' => "varchar(150) NOT NULL COMMENT '优惠券logo'",
            'code_type' => "tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'code类型（二维码/条形码/code码）'",
            'brand_name' => "varchar(15) NOT NULL COMMENT '商家名称'",
            'title' => "varchar(15) NOT NULL COMMENT '优惠券标题'",
            'sub_title' => "varchar(20) NOT NULL COMMENT '优惠券短标题'",
            'color' => "varchar(15) NOT NULL COMMENT '优惠券颜色'",
            'notice' => "varchar(15) NOT NULL COMMENT '使用说明'",
            'description' => "varchar(1000) NOT NULL COMMENT '优惠券描述'",
            'date_info' => "varchar(200) NOT NULL COMMENT '使用期限'",
            'quantity' => "int(10) unsigned NOT NULL DEFAULT '0' COMMENT '总库存'",
            'use_custom_code' => "tinyint(3) NOT NULL DEFAULT '0'",
            'bind_openid' => "tinyint(3) unsigned NOT NULL DEFAULT '0'",
            'can_share' => "tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否可分享'",
            'can_give_friend' => "tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否可转赠给朋友'",
            'get_limit' => "tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '每人领取限制'",
            'service_phone' => "varchar(20) NOT NULL COMMENT '服务电话'",
            'extra' => "varchar(1000) NOT NULL COMMENT '扩展数据'",
            'status' => "tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:审核中,2:未通过,3:已通过,4:卡券被商户删除,5:未知'",
            'is_display' => "tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否上架'",
            'is_selfconsume' => "tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否开启自助核销'",
            'promotion_url_name' => "varchar(10) NOT NULL",
            'promotion_url' => "varchar(100) NOT NULL",
            'promotion_url_sub_title' => "varchar(10) NOT NULL",
            'source' => "tinyint(3) unsigned NOT NULL DEFAULT '2' COMMENT '来源，1是系统，2是微信'",
            'dosage' => "int(10) unsigned NULL DEFAULT '0' COMMENT '已领取数量'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        $this->createIndex('card_id','{{%diandi_coupon}}','card_id',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_coupon}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

