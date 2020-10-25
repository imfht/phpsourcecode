<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */

return [
    // 默认输出类型后台默认不加HTML
    // 'default_return_type'    => '',

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    
'adminmenu'               => [
    [
        // 全局设置
        'name'=>'glob_set_name',
        'ico'=>'fa-cog',
        'url'=>'',
        'child'=>[
            [
            // 站点信息
            'name'=>'web_set_name',
            'ico'=>'',
            'url'=>'/admin/setting/site',
            'url_extra'=>'',
            ],
            [
            // 基本设置
            'name'=>'base_set_name',
            'ico'=>'',
            'url'=>'/admin/setting/base',
            'url_extra'=>'',
            ],

            [
            // 注册访问
            'name'=>'reg_view_name',
            'ico'=>'',
            'url'=>'/admin/setting/register',
            'url_extra'=>'',
            ],
            [
            // 站点功能
            'name'=>'web_function_name',
            'ico'=>'',
            'url'=>'/admin/setting/funcs',
            'url_extra'=>'?',
                ],
            [
            // 问题设置
            'name'=>'question_set_name',
            'ico'=>'',
            'url'=>'/admin/setting/question',
            'url_extra'=>'',
                ],
            
            [
            // 分类设置
            'name'=>'category_set_name',
            'ico'=>'',
            'url'=>'/admin/category/index'
                ],
            
             [
            // 邮件设置
            'name'=>'mail_set_name',
            'ico'=>'',
            'url'=>'/admin/setting/mail',
            'url_extra'=>'',
                ],
             [
            // 开放平台
            'name'=>'admin_open_name',
            'ico'=>'',
            'url'=>'/admin/setting/openid',
            'url_extra'=>'',
                ],
             [
            // 界面设置
            'name'=>'visual_set_name',
            'ico'=>'',
            'url'=>'/admin/setting/template',
            'url_extra'=>'',
                ],
             [
            // 导航设置
            'name'=>'navigation_cat_name',
            'ico'=>'',
            'url'=>'/admin/nv/catlist'
                ],
        ],
    ],
    'adminmenu'=>[
        // 
        'name'=>'content_model_name',
        'ico'=>'fa-puzzle-piece',
        'url'=>'',
        'child'=>[
            // [
            //     // 
            //     'name'=>'model_list',
            //     'ico'=>'',
            //     'url'=>'/admin/addons/index',
            //     'url_extra'=>'',
            //     ],
            
         
            
        ],
    ],
    [
        'name'=>'plus_model_name',
        'ico'=>'fa-random',
        'url'=>'',
        'child'=>[
             [
            // 
            'name'=>'plus_list_name',
            'ico'=>'',
            'url'=>'/admin/addons/index',
            'url_extra'=>'',
            ],
            [
            'name'=>'hook_list_name',
            'ico'=>'',
            'url'=>'/admin/addons/hooks',
            'url_extra'=>'',
            ],
            ['name'=>'caiji_role_name',
                'ico'=>'fa-hand-lizard-o',
                'url'=>'admin/caiji/index',
            ],
            ['name'=>'creat_user_name',
                'ico'=>'fa-user-plus',
                'url'=>'/admin/creatuser/creat',
               
            ],
        ],
    ],
    [
        // 用户中心
        'name'=>'user_center_name',
        'ico'=>'fa-group',
        'url'=>'',
        'child'=>[
            [
            // 用户列表
            'name'=>'user_list_name',
            'ico'=>'',
            'url'=>'admin/user/index'
                ],
            
        ],
    ],
    [
        // 授权列表
        'name'=>'power_message_name',
        'ico'=>'fa-key',
        'url'=>'',
        'child'=>[
            [
            //管理员列表
            'name'=>'admin_list_name',
            'ico'=>'',
            'url'=>'admin/user/index'
                ],
            [
            // 用户组
            'name'=>'admin_user_group',
            'ico'=>'',
            'url'=>'admin/user/group'
                ],
            
        ],
    ],
     [
        // 审核管理
        'name'=>'system_name',
        'ico'=>'fa-shirtsinbulk',
        'url'=>'',
        'child'=>[
            [
            // 数据备份
            'name'=>'datebase_backup_name',
            'ico'=>'',
            'url'=>'/admin/database/index/type/export'
            ],
            [
            // 数据恢复
            'name'=>'datebase_import_name',
            'ico'=>'',
            'url'=>'/admin/database/index/type/import'
            ],
          
        ],
    ],
     [
        // 应用商城
        'name'=>'shop_name',
        'ico'=>'fa-dropbox',
        'url'=>'',
        'child'=>[
            [
            // 数据备份
            'name'=>'shop_page_name',
            'ico'=>'',
            'url'=>'http://www.thinkask.cn/jofficial/store/store_list'
            ]
          
        ],
    ],
    [
        // 运营管理
        'name'=>'operation_management_name',
        'ico'=>'fa-drupal',
        'url'=>'',
        'child'=>[
            [
            // 
            'name'=>'adv_manager_name',
            'ico'=>'',
            'url'=>'admin/adv/advpostion'
            ]
          
        ],
    ],
 
],


];
