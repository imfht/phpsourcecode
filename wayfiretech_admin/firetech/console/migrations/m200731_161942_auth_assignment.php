<?php

use yii\db\Migration;

class m200731_161942_auth_assignment extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%auth_assignment}}', [
            'item_id' => "int(11) NOT NULL",
            'item_name' => "varchar(64) NOT NULL",
            'user_id' => "varchar(64) NOT NULL",
            'created_at' => "int(11) NULL",
            'PRIMARY KEY (`item_name`,`user_id`)'
        ], "ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户与权限关系'");
        
        /* 索引设置 */
        $this->createIndex('auth_assignment_user_id_idx','{{%auth_assignment}}','user_id',0);
        
        
        /* 表数据 */
        $this->insert('{{%auth_assignment}}',['item_id'=>'0','item_name'=>'人脸库管理','user_id'=>'11','created_at'=>'1586678304']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'0','item_name'=>'人脸识别','user_id'=>'11','created_at'=>'1586678304']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'31','item_name'=>'价格配置','user_id'=>'20','created_at'=>'1593573915']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'0','item_name'=>'会员管理','user_id'=>'11','created_at'=>'1586678304']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'32','item_name'=>'分销商品管理','user_id'=>'20','created_at'=>'1593575206']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'25','item_name'=>'商品分类','user_id'=>'20','created_at'=>'1593569319']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'35','item_name'=>'商品标签管理','user_id'=>'20','created_at'=>'1593575206']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'24','item_name'=>'商品管理','user_id'=>'15','created_at'=>'1589108423']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'24','item_name'=>'商品管理','user_id'=>'20','created_at'=>'1593569319']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'26','item_name'=>'商家','user_id'=>'20','created_at'=>'1593569319']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'27','item_name'=>'商家信息维护','user_id'=>'20','created_at'=>'1593569319']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'0','item_name'=>'基础权限组','user_id'=>'1','created_at'=>'1588809678']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'0','item_name'=>'基础权限组','user_id'=>'11','created_at'=>'1586678304']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'4','item_name'=>'基础权限组','user_id'=>'20','created_at'=>'1593569288']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'0','item_name'=>'开发示例','user_id'=>'11','created_at'=>'1586678304']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'0','item_name'=>'扩展功能','user_id'=>'11','created_at'=>'1586678304']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'36','item_name'=>'抽奖权限','user_id'=>'21','created_at'=>'1593679676']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'0','item_name'=>'权限控制','user_id'=>'11','created_at'=>'1586678304']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'0','item_name'=>'模块生成','user_id'=>'11','created_at'=>'1586678305']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'0','item_name'=>'模块统一入口','user_id'=>'1','created_at'=>'1588809691']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'10','item_name'=>'模块统一入口','user_id'=>'20','created_at'=>'1593569292']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'0','item_name'=>'站点管理','user_id'=>'11','created_at'=>'1586678305']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'0','item_name'=>'系统设置','user_id'=>'11','created_at'=>'1586678305']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'20','item_name'=>'订单操作','user_id'=>'20','created_at'=>'1593569319']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'17','item_name'=>'订单管理','user_id'=>'15','created_at'=>'1589031171']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'17','item_name'=>'订单管理','user_id'=>'20','created_at'=>'1593569319']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'0','item_name'=>'订座','user_id'=>'11','created_at'=>'1586678305']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'33','item_name'=>'评论管理','user_id'=>'20','created_at'=>'1593575206']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'0','item_name'=>'资源上传','user_id'=>'11','created_at'=>'1586678305']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'19','item_name'=>'资源上传','user_id'=>'20','created_at'=>'1593569297']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'30','item_name'=>'配置权限','user_id'=>'20','created_at'=>'1593573915']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'34','item_name'=>'配送点管理','user_id'=>'20','created_at'=>'1593575206']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'28','item_name'=>'默认入口','user_id'=>'15','created_at'=>'1589034117']);
        $this->insert('{{%auth_assignment}}',['item_id'=>'28','item_name'=>'默认入口','user_id'=>'20','created_at'=>'1593569319']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%auth_assignment}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

