<?php

use yii\db\Migration;

class m200731_161943_member_group extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%member_group}}', [
            'group_id' => "int(11) NOT NULL AUTO_INCREMENT",
            'level' => "int(11) NULL COMMENT '等级权重'",
            'item_name' => "varchar(64) NOT NULL COMMENT '名称'",
            'create_time' => "int(11) NULL",
            'update_time' => "int(11) NULL",
            'PRIMARY KEY (`group_id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        $this->createIndex('item_name','{{%member_group}}','item_name',1);
        
        
        /* 表数据 */
        $this->insert('{{%member_group}}',['group_id'=>'1','level'=>'1','item_name'=>'普通用户','create_time'=>'1580865152','update_time'=>NULL]);
        $this->insert('{{%member_group}}',['group_id'=>'2','level'=>'2','item_name'=>'vip1','create_time'=>'1580865194','update_time'=>NULL]);
        $this->insert('{{%member_group}}',['group_id'=>'3','level'=>'3','item_name'=>'vip2','create_time'=>'1580865207','update_time'=>NULL]);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%member_group}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

