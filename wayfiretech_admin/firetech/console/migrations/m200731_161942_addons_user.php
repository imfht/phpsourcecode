<?php

use yii\db\Migration;

class m200731_161942_addons_user extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%addons_user}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'type' => "smallint(6) NULL COMMENT '用户类型'",
            'module_name' => "varchar(50) NULL COMMENT '所属模块'",
            'user_id' => "int(11) NULL COMMENT '用户id'",
            'status' => "smallint(6) NULL COMMENT '审核状态'",
            'create_time' => "int(11) NULL",
            'update_time' => "int(11) NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='扩展模块用户表'");
        
        /* 索引设置 */
        $this->createIndex('module_name','{{%addons_user}}','module_name',0);
        $this->createIndex('user_id','{{%addons_user}}','user_id',0);
        
        
        /* 表数据 */
        $this->insert('{{%addons_user}}',['id'=>'34','type'=>'1','module_name'=>'diandi_shop','user_id'=>'15','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        $this->insert('{{%addons_user}}',['id'=>'35','type'=>'1','module_name'=>'diandi_shop','user_id'=>'17','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        $this->insert('{{%addons_user}}',['id'=>'36','type'=>'1','module_name'=>'diandi_store','user_id'=>'19','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        $this->insert('{{%addons_user}}',['id'=>'37','type'=>'0','module_name'=>'sys','user_id'=>'20','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        $this->insert('{{%addons_user}}',['id'=>'38','type'=>'1','module_name'=>'diandi_shop','user_id'=>'20','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        $this->insert('{{%addons_user}}',['id'=>'39','type'=>'1','module_name'=>'diandi_distribution','user_id'=>'20','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        $this->insert('{{%addons_user}}',['id'=>'40','type'=>'1','module_name'=>'diandi_lottery','user_id'=>'21','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        $this->insert('{{%addons_user}}',['id'=>'41','type'=>'1','module_name'=>'diandi_shop','user_id'=>'11','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        $this->insert('{{%addons_user}}',['id'=>'42','type'=>'1','module_name'=>'diandi_lottery','user_id'=>'11','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        $this->insert('{{%addons_user}}',['id'=>'43','type'=>'1','module_name'=>'diandi_distribution','user_id'=>'11','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        $this->insert('{{%addons_user}}',['id'=>'44','type'=>'1','module_name'=>'diandi_operator','user_id'=>'11','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        $this->insert('{{%addons_user}}',['id'=>'45','type'=>'1','module_name'=>'diandi_video','user_id'=>'11','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        $this->insert('{{%addons_user}}',['id'=>'46','type'=>'1','module_name'=>'diandi_video','user_id'=>'11','status'=>'1','create_time'=>NULL,'update_time'=>NULL]);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%addons_user}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

