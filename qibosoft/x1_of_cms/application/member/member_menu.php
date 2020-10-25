<?php

return array(
        'often'=>array(
                'title'=>'常用菜单',
                'icon'=>'fa fa-fw fa-star',
                'sons'=>array(
                ),
        ),
        'base'=>array(
                'title'=>'基础设置',
				'icon'=>'fa fa-windows',
                'sons'=>array(
                        array(
                                'icon'=>'fa fa-windows',
                                'title'=>'会员基础设置',
                                'sons'=>array(
                                        array(
                                            'icon'=>'fa fa-user-circle',
                                                'title'=>'修改个人资料',
                                                'link'=>'user/edit',
                                                //'type'=>'pc',
                                        ),
                                        array(
                                            'icon'=>'fa fa-envelope-o',
                                            'title'=>'站内短消息',
                                            'link'=>'msg/index',
                                            'power'=>'menu_usemsg_group',
                                        ),
                                        array(
                                            'icon'=>'fa fa-volume-up',
                                            'title'=>'消息提醒设置',
                                            'link'=>'remind/set',
                                            'power'=>'menu_msg_remind_group',
                                        ),
//                                         array(
//                                                 'title'=>'积分充值',
//                                                 'link'=>'jifen/add',
//                                         ),
//                                         array(
//                                                 'title'=>'积分消费记录',
//                                                 'link'=>'jifen/index',
//                                         ),
										array(
										    'icon'=>'fa fa-wechat',
                                            'title'=>'绑定微信QQ',
                                            'link'=>'bindlogin/weixin',
										    'power'=>'menu_bindlogin_group',
                                        ),
										array(
										    'icon'=>'glyphicon glyphicon-circle-arrow-up',
                                            'title'=>'升级会员等级',
                                            'link'=>'group/index',
										    'param'=>['tag'=>input('tag')],  //param很少用到，通过他可以传递多个参数。
										    'power'=>'menu_upgroup_group',
                                        ),
                                        array(
                                            'icon'=>'fa fa-drivers-license',
                                            'title'=>'手机身份验证',
                                            'link'=>'yz/mob',
                                            'power'=>'menu_yzmob_group',
                                        ),
                                        array(
                                            'icon'=>'fa fa-group',
                                            'title'=>'好友粉丝管理',
                                            'link'=>'friend/index',
                                            'power'=>'menu_friend_group',
                                        ),
                                        array(
                                            'icon'=>'fa fa-dropbox',
                                            'title'=>'应用市场',
                                            'link'=>'market/index',
                                            'power'=>'menu_market_group',
                                        ),
                                ),
                        ), 
                )
        ),
		'plugin'=>array(
                'title'=>'功能插件',
				'icon'=>'fa fa-fw fa-puzzle-piece',
                'sons'=>array(
                ),
        ),
		'module'=>array(
                'title'=>'频道模块',
				'icon'=>'fa fa-fw fa-cubes',
                'sons'=>array(
                ),
        ),        
);

