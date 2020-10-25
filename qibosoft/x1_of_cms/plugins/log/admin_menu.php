<?php

return array(
		'plugin'=>array(
				'title'=>'plugin',
				'sons'=>array(
							array(
								'title'=>'插件功能',
								'sons'=>array(
									array(
										'title'=>'后台操作日志',
										'link'=>'action/index',
									    'power'=>['delete'],
									),
									array(
										'title'=>'后台登录日志',
										 'link'=>'login/index',
									     'power'=>['delete'],
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