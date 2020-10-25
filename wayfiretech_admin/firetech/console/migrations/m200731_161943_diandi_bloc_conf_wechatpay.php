<?php

use yii\db\Migration;

class m200731_161943_diandi_bloc_conf_wechatpay extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%diandi_bloc_conf_wechatpay}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'bloc_id' => "int(10) unsigned NOT NULL COMMENT '公司ID'",
            'mch_id' => "varchar(50) NOT NULL COMMENT '商户ID'",
            'app_id' => "varchar(100) NOT NULL DEFAULT '0' COMMENT 'APPID'",
            'key' => "varchar(255) NOT NULL DEFAULT '0' COMMENT '支付密钥'",
            'notify_url' => "varchar(100) NOT NULL COMMENT '回调地址'",
            'create_time' => "int(11) NULL",
            'update_time' => "int(11) NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%diandi_bloc_conf_wechatpay}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

