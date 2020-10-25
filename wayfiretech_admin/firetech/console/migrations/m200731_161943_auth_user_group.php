<?php

use yii\db\Migration;

class m200731_161943_auth_user_group extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%auth_user_group}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'name' => "varchar(64) NOT NULL COMMENT '用户组名称'",
            'type' => "smallint(6) NOT NULL COMMENT '用户组类型'",
            'description' => "text NULL COMMENT '用户组名称'",
            'module_name' => "varchar(50) NULL",
            'created_at' => "int(11) NULL",
            'updated_at' => "int(11) NULL",
            'PRIMARY KEY (`id`,`name`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='后台用户组'");
        
        /* 索引设置 */
        $this->createIndex('type','{{%auth_user_group}}','type',0);
        $this->createIndex('name','{{%auth_user_group}}','name',0);
        
        
        /* 表数据 */
        $this->insert('{{%auth_user_group}}',['id'=>'551','name'=>'基础权限组','type'=>'0','description'=>'','module_name'=>'sys','created_at'=>'1588976797','updated_at'=>'1588837647']);
        $this->insert('{{%auth_user_group}}',['id'=>'552','name'=>'总管理员','type'=>'0','description'=>'','module_name'=>'sys','created_at'=>'1588976797','updated_at'=>NULL]);
        $this->insert('{{%auth_user_group}}',['id'=>'559','name'=>'店滴商城-运维','type'=>'1','description'=>'','module_name'=>'diandi_shop','created_at'=>'1588983602','updated_at'=>'1588989817']);
        $this->insert('{{%auth_user_group}}',['id'=>'565','name'=>'店滴商城-评论','type'=>'1','description'=>'','module_name'=>'diandi_shop','created_at'=>'1588989802','updated_at'=>'1588989802']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%auth_user_group}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

