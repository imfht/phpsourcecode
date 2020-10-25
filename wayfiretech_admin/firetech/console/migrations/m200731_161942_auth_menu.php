<?php

use yii\db\Migration;

class m200731_161942_auth_menu extends Migration
{
    public function up()
    {
        /* 取消外键约束 */
        $this->execute('SET foreign_key_checks = 0');
        
        /* 创建表 */
        $this->createTable('{{%auth_menu}}', [
            'id' => "int(11) NOT NULL AUTO_INCREMENT",
            'name' => "varchar(128) NOT NULL",
            'parent' => "int(11) NULL",
            'route' => "varchar(255) NULL",
            'order' => "int(11) NULL DEFAULT '0'",
            'data' => "blob NULL",
            'type' => "varchar(20) NULL",
            'icon' => "varchar(30) NULL",
            'is_sys' => "enum('system','addons') NULL DEFAULT 'system'",
            'module_name' => "varchar(30) NULL",
            'PRIMARY KEY (`id`)'
        ], "ENGINE=InnoDB  DEFAULT CHARSET=utf8");
        
        /* 索引设置 */
        $this->createIndex('parent','{{%auth_menu}}','parent',0);
        
        /* 外键约束设置 */
        $this->addForeignKey('fk_menu_963_00','{{%auth_menu}}', 'parent', '{{%menu}}', 'id', 'CASCADE', 'CASCADE' );
        
        /* 表数据 */
        $this->insert('{{%auth_menu}}',['id'=>'1','name'=>'权限管理','parent'=>NULL,'route'=>'/admin/default/index','order'=>'3','data'=>NULL,'type'=>'sysai','icon'=>'fa fa-fw fa-sitemap','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'2','name'=>'权限维护','parent'=>'1','route'=>'/admin/permission/index','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'3','name'=>'菜单路由','parent'=>'1','route'=>'/admin/route/index','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'4','name'=>'用户组','parent'=>'27','route'=>'/admin/role/index','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'5','name'=>'权限分配','parent'=>'1','route'=>'/admin/assignment/index','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'6','name'=>'系统菜单','parent'=>'1','route'=>'/admin/menu/index','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'9','name'=>'权限规则','parent'=>'1','route'=>'/admin/rule/index','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'11','name'=>'系统设置','parent'=>'75','route'=>'/system/settings/weburl','order'=>'1','data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'12','name'=>'扩展功能','parent'=>NULL,'route'=>NULL,'order'=>'7','data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'25','name'=>'会员管理','parent'=>NULL,'route'=>'/member/dd-member/index','order'=>NULL,'data'=>NULL,'type'=>'member','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'26','name'=>'AI会员管理','parent'=>'33','route'=>'/diandiai/dd-ai-member/index','order'=>NULL,'data'=>NULL,'type'=>'aimember','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'27','name'=>'管理员管理','parent'=>NULL,'route'=>NULL,'order'=>'2','data'=>NULL,'type'=>'sysai','icon'=>'glyphicon glyphicon-th-large','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'28','name'=>'管理员','parent'=>'27','route'=>'/admin/user/index','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'33','name'=>'人脸识别','parent'=>NULL,'route'=>'/diandiai/dd-ai-member/index','order'=>NULL,'data'=>NULL,'type'=>'aimember','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'35','name'=>'人脸库管理','parent'=>NULL,'route'=>'/diandiai/dd-ai-faces/index','order'=>NULL,'data'=>NULL,'type'=>'aimember','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'36','name'=>'应用管理','parent'=>'35','route'=>'/diandiai/dd-ai-applications/index','order'=>NULL,'data'=>NULL,'type'=>'aimember','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'37','name'=>'人脸库分组','parent'=>'35','route'=>'/diandiai/dd-ai-groups/index','order'=>NULL,'data'=>NULL,'type'=>'aimember','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'38','name'=>'人脸管理','parent'=>'35','route'=>'/diandiai/dd-ai-faces/index','order'=>NULL,'data'=>NULL,'type'=>'aimember','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'39','name'=>'站点管理','parent'=>NULL,'route'=>'/website/dd-website-slide/index','order'=>'1','data'=>NULL,'type'=>'sysai','icon'=>'fa fa-fw fa-cubes','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'40','name'=>'幻灯片','parent'=>'39','route'=>'/website/dd-website-slide/index','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'41','name'=>'文章管理','parent'=>'39','route'=>'/article/dd-article/index','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'42','name'=>'文章分类','parent'=>'41','route'=>'/article/dd-article-category/index','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'43','name'=>'文章列表','parent'=>'41','route'=>'/article/dd-article/index','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'44','name'=>'联系我们','parent'=>'39','route'=>'/website/dd-website-contact/index','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'45','name'=>'顶部导航','parent'=>'1','route'=>'/admin/menu-top/index','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'46','name'=>'会员管理','parent'=>'25','route'=>'/member/dd-member/index','order'=>NULL,'data'=>NULL,'type'=>'member','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'49','name'=>'会员等级','parent'=>'25','route'=>'/member/dd-member-group/index','order'=>NULL,'data'=>NULL,'type'=>'member','icon'=>'','is_sys'=>'system','module_name'=>NULL]);
        $this->insert('{{%auth_menu}}',['id'=>'58','name'=>'店滴商城','parent'=>NULL,'route'=>'/diandi_shop/default/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'60','name'=>'商品管理','parent'=>'63','route'=>'/diandi_shop/goods/dd-goods/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_shop']);
        $this->insert('{{%auth_menu}}',['id'=>'61','name'=>'商品分类','parent'=>'63','route'=>'/diandi_shop/goods/dd-category/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_shop']);
        $this->insert('{{%auth_menu}}',['id'=>'62','name'=>'订单管理','parent'=>NULL,'route'=>NULL,'order'=>'4','data'=>NULL,'type'=>'plugins','icon'=>'fa fa-fw fa-list','is_sys'=>'addons','module_name'=>'diandi_shop']);
        $this->insert('{{%auth_menu}}',['id'=>'63','name'=>'商品管理','parent'=>NULL,'route'=>NULL,'order'=>'3','data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_shop']);
        $this->insert('{{%auth_menu}}',['id'=>'64','name'=>'订单管理','parent'=>'62','route'=>'/diandi_shop/order/dd-order/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'fa fa-fw fa-clone','is_sys'=>'addons','module_name'=>'diandi_shop']);
        $this->insert('{{%auth_menu}}',['id'=>'69','name'=>'商家设置','parent'=>NULL,'route'=>'/diandi_shop/setting/store/index','order'=>'2','data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_shop']);
        $this->insert('{{%auth_menu}}',['id'=>'70','name'=>'商家设置','parent'=>'69','route'=>'/diandi_shop/setting/store/setting','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_shop']);
        $this->insert('{{%auth_menu}}',['id'=>'71','name'=>'评论管理','parent'=>'69','route'=>'/diandi_shop/setting/comment/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_shop']);
        $this->insert('{{%auth_menu}}',['id'=>'74','name'=>'配送点管理','parent'=>'69','route'=>'/diandi_shop/setting/area/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_shop']);
        $this->insert('{{%auth_menu}}',['id'=>'75','name'=>'系统管理','parent'=>NULL,'route'=>NULL,'order'=>'4','data'=>NULL,'type'=>'sysai','icon'=>'fa fa-fw fa-cogs','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'76','name'=>'扩展模块','parent'=>'75','route'=>'/addons/addons/index','order'=>'2','data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'197','name'=>'站点设置','parent'=>'39','route'=>'/website/setting/website','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'221','name'=>'商品标签','parent'=>'63','route'=>'/diandi_shop/goods/label/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_shop']);
        $this->insert('{{%auth_menu}}',['id'=>'222','name'=>'公司管理','parent'=>'75','route'=>'/bloc/bloc/index','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'223','name'=>'开发示例','parent'=>NULL,'route'=>NULL,'order'=>'5','data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'224','name'=>'表单示例','parent'=>'223','route'=>'/demo/form/index','order'=>NULL,'data'=>NULL,'type'=>'sysai','icon'=>'','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'225','name'=>'商家管理','parent'=>NULL,'route'=>'/diandi_store/default/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'system','module_name'=>'']);
        $this->insert('{{%auth_menu}}',['id'=>'227','name'=>'基础设置','parent'=>NULL,'route'=>'/diandi_store/slide/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_store']);
        $this->insert('{{%auth_menu}}',['id'=>'228','name'=>'幻灯片','parent'=>NULL,'route'=>'/diandi_store/store-slide/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_store']);
        $this->insert('{{%auth_menu}}',['id'=>'229','name'=>'商家管理','parent'=>NULL,'route'=>'/diandi_store/store/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_store']);
        $this->insert('{{%auth_menu}}',['id'=>'230','name'=>'商家座位','parent'=>NULL,'route'=>'/diandi_store/store-seat/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_store']);
        $this->insert('{{%auth_menu}}',['id'=>'232','name'=>'服务管理','parent'=>NULL,'route'=>'/diandi_store/store-service/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_store']);
        $this->insert('{{%auth_menu}}',['id'=>'242','name'=>'营销管理','parent'=>NULL,'route'=>'/diandi_store/coupon/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_store']);
        $this->insert('{{%auth_menu}}',['id'=>'243','name'=>'优惠券分类','parent'=>NULL,'route'=>'/diandi_store/coupon-groups/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_store']);
        $this->insert('{{%auth_menu}}',['id'=>'244','name'=>'优惠券','parent'=>NULL,'route'=>'/diandi_store/coupon/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_store']);
        $this->insert('{{%auth_menu}}',['id'=>'245','name'=>'领取记录','parent'=>NULL,'route'=>'/diandi_store/coupon-record/index','order'=>NULL,'data'=>NULL,'type'=>'plugins','icon'=>'','is_sys'=>'addons','module_name'=>'diandi_store']);
        
        /* 设置外键约束 */
        $this->execute('SET foreign_key_checks = 1;');
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        /* 删除表 */
        $this->dropTable('{{%auth_menu}}');
        $this->execute('SET foreign_key_checks = 1;');
    }
}

