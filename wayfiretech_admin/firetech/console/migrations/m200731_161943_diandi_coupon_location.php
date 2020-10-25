<?php

use yii\db\Migration;

class m200731_161943_diandi_coupon_location extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_coupon_location}}', [
            'id' => "int(10) unsigned NOT NULL AUTO_INCREMENT",
            'uniacid' => "int(10) unsigned NOT NULL",
            'acid' => "int(10) unsigned NOT NULL",
            'sid' => "int(10) unsigned NOT NULL",
            'location_id' => "int(10) unsigned NOT NULL",
            'business_name' => "varchar(50) NOT NULL",
            'branch_name' => "varchar(50) NOT NULL",
            'category' => "varchar(255) NOT NULL",
            'province' => "varchar(15) NOT NULL",
            'city' => "varchar(15) NOT NULL",
            'district' => "varchar(15) NOT NULL",
            'address' => "varchar(50) NOT NULL",
            'longitude' => "varchar(15) NOT NULL",
            'latitude' => "varchar(15) NOT NULL",
            'telephone' => "varchar(20) NOT NULL",
            'photo_list' => "varchar(10000) NOT NULL",
            'avg_price' => "int(10) unsigned NOT NULL",
            'open_time' => "varchar(50) NOT NULL",
            'recommend' => "varchar(255) NOT NULL",
            'special' => "varchar(255) NOT NULL",
            'introduction' => "varchar(255) NOT NULL",
            'offset_type' => "tinyint(3) unsigned NOT NULL",
            'status' => "tinyint(3) unsigned NOT NULL",
            'message' => "varchar(255) NOT NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=MyISAM DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        $this->createIndex('uniacid','{{%diandi_coupon_location}}','uniacid, acid',0);
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_coupon_location}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

