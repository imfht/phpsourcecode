<?php

use yii\db\Migration;

class m200731_161942_auth_item extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%auth_item}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'name' => "varchar(64) NOT NULL",
            'type' => "smallint(6) NOT NULL",
            'description' => "text NULL",
            'rule_name' => "varchar(64) NULL",
            'parent_id' => "int(11) NULL",
            'data' => "blob NULL",
            'module_name' => "varchar(50) NULL",
            'created_at' => "int(11) NULL",
            'updated_at' => "int(11) NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        $this->createIndex('rule_name','{{%auth_item}}','rule_name',0);
        $this->createIndex('type','{{%auth_item}}','type',0);
        $this->createIndex('name','{{%auth_item}}','name',0);
        
        
        /* 表数据 */
        $this->insert('{{%auth_item}}',['id'=>'1','name'=>'人脸库管理','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'sys','created_at'=>'1582139590','updated_at'=>'1582141243']);
        $this->insert('{{%auth_item}}',['id'=>'2','name'=>'人脸识别','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'1','data'=>NULL,'module_name'=>'sys','created_at'=>'1582139583','updated_at'=>'1582141300']);
        $this->insert('{{%auth_item}}',['id'=>'3','name'=>'会员管理','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'sys','created_at'=>'1582139598','updated_at'=>'1582139598']);
        $this->insert('{{%auth_item}}',['id'=>'4','name'=>'基础权限组','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'sys','created_at'=>'1585578773','updated_at'=>'1585578773']);
        $this->insert('{{%auth_item}}',['id'=>'6','name'=>'开发示例','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'sys','created_at'=>'1585611530','updated_at'=>'1585611530']);
        $this->insert('{{%auth_item}}',['id'=>'7','name'=>'扩展功能','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'sys','created_at'=>'1582139605','updated_at'=>'1582139605']);
        $this->insert('{{%auth_item}}',['id'=>'8','name'=>'权限控制','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'sys','created_at'=>'1582139364','updated_at'=>'1582139364']);
        $this->insert('{{%auth_item}}',['id'=>'9','name'=>'模块生成','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'sys','created_at'=>'1585473562','updated_at'=>'1585473562']);
        $this->insert('{{%auth_item}}',['id'=>'10','name'=>'模块统一入口','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'','created_at'=>'1588808930','updated_at'=>'1588808930']);
        $this->insert('{{%auth_item}}',['id'=>'11','name'=>'测试','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'sys','created_at'=>'1588851182','updated_at'=>'1588851182']);
        $this->insert('{{%auth_item}}',['id'=>'12','name'=>'测试实施','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'sys','created_at'=>'1588573267','updated_at'=>'1588573267']);
        $this->insert('{{%auth_item}}',['id'=>'13','name'=>'站点管理','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'sys','created_at'=>'1582139575','updated_at'=>'1582139575']);
        $this->insert('{{%auth_item}}',['id'=>'14','name'=>'管理员管理','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'','created_at'=>'1588779477','updated_at'=>'1588779477']);
        $this->insert('{{%auth_item}}',['id'=>'15','name'=>'系统设置','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'sys','created_at'=>'1582139568','updated_at'=>'1582139568']);
        $this->insert('{{%auth_item}}',['id'=>'17','name'=>'订单管理','type'=>'1','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'diandi_shop','created_at'=>'1588927400','updated_at'=>'1588927400']);
        $this->insert('{{%auth_item}}',['id'=>'18','name'=>'订座','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'sys','created_at'=>'1585413762','updated_at'=>'1585413762']);
        $this->insert('{{%auth_item}}',['id'=>'19','name'=>'资源上传','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'sys','created_at'=>'1585451956','updated_at'=>'1585451956']);
        $this->insert('{{%auth_item}}',['id'=>'20','name'=>'订单操作','type'=>'1','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'17','data'=>NULL,'module_name'=>'diandi_shop','created_at'=>'1588945445','updated_at'=>'1588945445']);
        $this->insert('{{%auth_item}}',['id'=>'24','name'=>'商品管理','type'=>'1','description'=>NULL,'rule_name'=>'模块访问','parent_id'=>'0','data'=>'a:1:{s:2:"id";s:2:"23";}','module_name'=>'diandi_shop','created_at'=>'1588952740','updated_at'=>'1589126545']);
        $this->insert('{{%auth_item}}',['id'=>'25','name'=>'商品分类','type'=>'1','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'24','data'=>NULL,'module_name'=>'diandi_shop','created_at'=>'1588953479','updated_at'=>'1588953479']);
        $this->insert('{{%auth_item}}',['id'=>'26','name'=>'商家','type'=>'1','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'diandi_shop','created_at'=>'1588953578','updated_at'=>'1588953578']);
        $this->insert('{{%auth_item}}',['id'=>'27','name'=>'商家信息维护','type'=>'1','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'26','data'=>NULL,'module_name'=>'diandi_shop','created_at'=>'1588953596','updated_at'=>'1588953596']);
        $this->insert('{{%auth_item}}',['id'=>'28','name'=>'默认入口','type'=>'1','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'diandi_shop','created_at'=>'1589034059','updated_at'=>'1589034059']);
        $this->insert('{{%auth_item}}',['id'=>'29','name'=>'数据库','type'=>'0','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'15','data'=>NULL,'module_name'=>'sys','created_at'=>'1592915692','updated_at'=>'1592915723']);
        $this->insert('{{%auth_item}}',['id'=>'30','name'=>'配置权限','type'=>'1','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'diandi_distribution','created_at'=>'1593569358','updated_at'=>'1593569512']);
        $this->insert('{{%auth_item}}',['id'=>'31','name'=>'价格配置','type'=>'1','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'30','data'=>NULL,'module_name'=>'diandi_distribution','created_at'=>'1593569540','updated_at'=>'1593569540']);
        $this->insert('{{%auth_item}}',['id'=>'32','name'=>'分销商品管理','type'=>'1','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'diandi_distribution','created_at'=>'1593573986','updated_at'=>'1593573986']);
        $this->insert('{{%auth_item}}',['id'=>'33','name'=>'评论管理','type'=>'1','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'26','data'=>NULL,'module_name'=>'diandi_shop','created_at'=>'1593574124','updated_at'=>'1593574124']);
        $this->insert('{{%auth_item}}',['id'=>'34','name'=>'配送点管理','type'=>'1','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'24','data'=>NULL,'module_name'=>'diandi_shop','created_at'=>'1593575097','updated_at'=>'1593575097']);
        $this->insert('{{%auth_item}}',['id'=>'35','name'=>'商品标签管理','type'=>'1','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'24','data'=>NULL,'module_name'=>'diandi_shop','created_at'=>'1593575124','updated_at'=>'1593575124']);
        $this->insert('{{%auth_item}}',['id'=>'36','name'=>'抽奖权限','type'=>'1','description'=>NULL,'rule_name'=>NULL,'parent_id'=>'0','data'=>NULL,'module_name'=>'diandi_lottery','created_at'=>'1593679648','updated_at'=>'1593679648']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%auth_item}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

