<?php

use yii\db\Migration;

class m200731_161942_ai_groups extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%ai_groups}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'ai_group_status' => "int(11) NULL COMMENT '百度ai用户组创建状态'",
            'name' => "varchar(50) NULL COMMENT '分组名称'",
            'createtime' => "varchar(10) NULL",
            'updatetime' => "varchar(10) NULL",
            'is_default' => "int(11) NULL DEFAULT '0'",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='ai人脸库分组'");
        
        /* 索引设置 */
        
        
        /* 表数据 */
        $this->insert('{{%ai_groups}}',['id'=>'7','ai_group_status'=>'0','name'=>'第一组','createtime'=>'1579356698','updatetime'=>'1579356249','is_default'=>'0']);
        $this->insert('{{%ai_groups}}',['id'=>'8','ai_group_status'=>'0','name'=>'第二组','createtime'=>'1579356254','updatetime'=>'1579356254','is_default'=>'0']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%ai_groups}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

