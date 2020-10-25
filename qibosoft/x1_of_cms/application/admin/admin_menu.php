<?php

return array(
		'often'=>array(
                'title'=>'常用菜单',
				'icon'=>'fa fa-fw fa-star',
                'sons'=>array(
                ),
        ),
        'base'=>array(
                'title'=>'系统功能',
				'icon'=>'fa fa-fw fa-gear',
                'sons'=>array(
                        array(
                                'icon'=>'fa fa-recycle',
                                'title'=>'系统核心设置',
                                'sons'=>array(                                        
                                        array(
                                                'title'=>'网站配置',
                                                'link'=>'setting/index',
                                                'param'=>['group'=>1],    //param很少用到，通过他可以传递多个参数。
                                                'power'=>[],
                                        ),
                                        array(
                                                'title'=>'清空缓存',
                                                'link'=>'setting/clearcache',
                                                'power'=>[],
                                        ),
                                    array(
                                        'title'=>'管理用户购买应用',
                                        'link'=>'buylist/index',
                                        'power'=>[
                                            'edit'=>'调整有效日期',
                                            'delete'=>'删除购买记录',
                                        ],
                                    ),
                                        array(
                                                'title'=>'插件管理',
                                                'link'=>'plugin/index',
                                                'power'=>['add'=>'安装本地插件','market'=>'安装应用市场插件','edit'=>'修改配置信息','delete'=>'卸载系统插件','copy'=>'复制系统插件'],
                                        ),
                                        array(
                                                'title'=>'系统模块管理',
                                                'link'=>'module/index',
                                                'power'=>[
                                                        'add'=>'安装本地模块',
                                                        'market'=>'安装应用市场模块',
                                                        'edit'=>'修改配置信息',
                                                        'delete'=>'卸载频道模块',
                                                        'copy'=>'复制频道模块',
                                                ],
                                        ),
                                        array(
                                                'title'=>'接口与钩子管理',
                                                'link'=>'hook/index',
                                                'power'=>[
                                                        'add'=>'添加接口',                                                        
                                                        'edit'=>'修改接口',
                                                        'delete'=>'删除接口',
                                                        'hook_plugin/market'=>'钩子云市场',
                                                        'hook_plugin/index'=>'管理钩子',
                                                        'hook_plugin/add'=>'添加钩子',
                                                        'hook_plugin/edit'=>'修改钩子',
                                                        'hook_plugin/delete'=>'删除钩子',
                                                ],
                                        ),
                                    array(
                                        'title'=>'定时任务管理',
                                        'link'=>'timedtask/index',
                                        'power'=>[
                                            'add'=>'添加定时任务',
                                            'edit'=>'修改定时任务',
                                            'delete'=>'删除定时任务',
                                            'log'=>'定时任务日志管理',
                                        ],
                                    ),
                                        array(
                                                'title'=>'后台常用菜单设置',
                                                'link'=>'admin_menu/index',
                                        ),
                                        array(
                                                'title'=>'会员个性菜单设置',
                                                'link'=>'member_menu/index',
                                                'power'=>['add'=>'手工添加菜单','edit'=>'修改菜单','delete'=>'删除菜单','copy'=>'复制菜单'],
                                        ),
										array(
                                                'title'=>'网站菜单设置',
                                                'link'=>'webmenu/index',
                                        ),
                                        array(
                                            'title'=>'群聊菜单设置',
                                            'link'=>'chatmod/index',
                                        ),
                                        array(
                                                'title'=>'独立页管理',
                                                'link'=>'alonepage/index',
                                        ),
                                        array(
                                                'title'=>'风格市场',
                                                'link'=>'style/market',
                                                'power'=>[
                                                        'add'=>'安装',
                                                        'market'=>'查看',
                                                ],
                                        ),
                                        array(
                                                'title'=>'系统在线升级',
                                                'link'=>'upgrade/index',
                                                'power'=>[
                                                        'index'=>'查看系统升级',
                                                        'sysup'=>'执行升级操作',
                                                        'check_files'=>'核对升级文件',
                                                        'view_file'=>'查看升级文件',
                                                ],
                                        ),
                                ),
                        ),
                        
                        array(
                                'title'=>'微信营销设置',
                                'sons'=>array(
                                        
                                        

                                ),
                        ),
                        
                        array(
                                'title'=>'微信对话设置',
                                'sons'=>array(
                                   //     array(
                                   //             'title'=>'用户关注时回复内容',
                                     //           'link'=>'weixin_autoreply/FirstReply',
                                     //   ),
                                     //   array(
                                     //           'title'=>'用户提问自动回复',
                                     //           'link'=>'weixin_autoreply/index',
                                    //    ),
                                      //  array(
                                        //        'title'=>'微信客服设置',
                                        //        'link'=>'weixin_kefu/config',
                                       // ),
                                        //array(
                                         //       'title'=>'微信聊天记录',
                                        //        'link'=>'weixin_msg/index',
                                       // ),
                                ),
                        ),
                        
                        array(
                                'title'=>'网站常用功能管理',
                                'sons'=>array(
                                        
                                        
                                ),
                        ),
                        
                        array(
                                'icon'=>'fa fa-database',
                                'title'=>'数据库工具',
                                'sons'=>array(
                                        array(
                                                'title'=>'备份数据库',
                                                'link'=>'mysql/index',
                                                'power'=>['backup'=>'执行备份数据','showtable'=>'查看数据表结构与数据'],
                                        ),
                                        array(
                                                'title'=>'数据库还原',
                                                'link'=>'mysql/into',
                                        ),
                                        array(
                                                'title'=>'数据库工具',
                                                'link'=>'mysql/tool',
                                        ),
                                ),
                        ),
                        
                        array(
                                'title'=>'菜单管理',
                                'sons'=>array(
                                        
                                        
                                        
                                ),
                        ),
                        

                        
                )
        ),
        'member'=>array(
                'title'=>'会员管理',
				'icon'=>'fa fa-fw fa-user-circle-o',
                'sons'=>array(
				            array(
				                'icon'=>'fa fa-user',
                                'title'=>'用户管理',
				                    'sons'=>array(
                                        array(
                                                'title'=>'用户资料管理',
                                                'link'=>'member/index',
                                        ),
                                        array(
                                                'title'=>'用户组权限管理',
                                                'link'=>'group/index',
                                                'power'=>['add','edit','delete','admin_power'=>'后台权限设置'],
                                        ),
                                        array(
                                                'title'=>'添加用户组',
                                                'link'=>'group/add',
                                        ),
				                        array(
				                                 'title'=>'用户组字段管理',
				                                 'link'=>'group_cfg/index',
				                                'power'=>['index','edit','delete','add'=>'手工加字段','autoadd'=>'批量导入内置字段'],
				                         ),
				                         array(
				                                    'title'=>'用户认证升级管理',
				                                    'link'=>'group_log/index',
				                                    'power'=>['index','delete','pass'=>'审核操作'],
				                         ),
				                        array(
				                            'title'=>'用户实名审核管理',
				                            'link'=>'yz/index',
				                            'power'=>['index','edit'=>'审核'],
				                        ),
                                ),
                        ),
                ),
        ),
        'module'=>array(
                'title'=>'模块中心',
				'icon'=>'fa fa-fw fa-cubes',
                'sons'=>array(
                ),
        ),
        'plugin'=>array(
                'title'=>'插件中心',
				'icon'=>'fa fa-fw fa-puzzle-piece',
                'sons'=>array(
                ),
        ),
);

