<?php

return array(
		'plugin'=>array(
				'title'=>'plugin',
				'sons'=>array(
							array(
								'title'=>'微信功能',
								'sons'=>array(
										array(
                                                'title'=>'微信接口设置',
                                                'link'=>'setting/index',
										        'power'=>[],
												
                                        ),
								        array(
								                'title'=>'微信回复及客服设置',
								                'link'=>'setting/index',
								                'param'=>'group=12',
								                'power'=>[],
								                
								        ),
                                        array(
                                                'title'=>'微信菜单设置',
                                                'link'=>'menu/config',
                                                'power'=>[],
                                        ),
								        array(
								                'title'=>'关键字回复设置',
								                'link'=>'weixin_autoreply/index',
								        ),
    									array(
    										'title'=>'微信聊天记录',
    										'link'=>'weixin_msg/index',
    									     'power'=>[],
    									),
								),
							),
				),
		),
);

/*
return array(
			array(
				'title'=>'功能设置',
				'sons'=>array(
					array(
						'title'=>'模型管理',
						'link'=>'module/index',
					),
					array(
						'title'=>'栏目管理',
						'link'=>'sort/index',
					),
				),
			),
		);
*/