<?php

use yii\db\Migration;

class m200731_161942_auth_assignment_group extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%auth_assignment_group}}', [
            'group_id' => "int(11) NOT NULL",
            'item_name' => "varchar(64) NOT NULL",
            'user_id' => "varchar(64) NOT NULL",
            'created_at' => "int(11) NULL",
            'PRIMARY KEY (`item_name`,`user_id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户权限组'");
        
        /* 索引设置 */
        $this->createIndex('auth_assignment_user_id_idx','{{%auth_assignment_group}}','user_id',0);
        
        
        /* 表数据 */
        $this->insert('{{%auth_assignment_group}}',['group_id'=>'0','item_name'=>'基础权限组','user_id'=>'1','created_at'=>'1588768586']);
        $this->insert('{{%auth_assignment_group}}',['group_id'=>'551','item_name'=>'基础权限组','user_id'=>'11','created_at'=>'1589288351']);
        $this->insert('{{%auth_assignment_group}}',['group_id'=>'0','item_name'=>'基础权限组','user_id'=>'14','created_at'=>'1588816083']);
        $this->insert('{{%auth_assignment_group}}',['group_id'=>'551','item_name'=>'基础权限组','user_id'=>'15','created_at'=>'1592303323']);
        $this->insert('{{%auth_assignment_group}}',['group_id'=>'551','item_name'=>'基础权限组','user_id'=>'19','created_at'=>'1592302886']);
        $this->insert('{{%auth_assignment_group}}',['group_id'=>'0','item_name'=>'基础权限组','user_id'=>'2','created_at'=>'1588756893']);
        $this->insert('{{%auth_assignment_group}}',['group_id'=>'565','item_name'=>'店滴商城-评论','user_id'=>'20','created_at'=>'1593569319']);
        $this->insert('{{%auth_assignment_group}}',['group_id'=>'559','item_name'=>'店滴商城-运维','user_id'=>'15','created_at'=>'1589030125']);
        $this->insert('{{%auth_assignment_group}}',['group_id'=>'559','item_name'=>'店滴商城-运维','user_id'=>'20','created_at'=>'1593569319']);
        $this->insert('{{%auth_assignment_group}}',['group_id'=>'0','item_name'=>'总管理员','user_id'=>'1','created_at'=>'1588768586']);
        $this->insert('{{%auth_assignment_group}}',['group_id'=>'552','item_name'=>'总管理员','user_id'=>'11','created_at'=>'1589288348']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%auth_assignment_group}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

