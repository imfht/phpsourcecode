<?php

return array(
		'plugin'=>array(
				'title'=>'插件',
				'sons'=>array(
							array(
								'title'=>'城市地区管理',
								'sons'=>array(
								        array(
								                'title'=>'省份管理',
								                'link'=>'province/index',
												'power'=>[
													'index' =>'首页',
													'delete'=>'删除',
													'add'   =>'新增',
													'edit'  =>'编辑',
													'readcity' =>'导入地址库',
												],
								        ),
										array(
								                'title'=>'城市管理',
								                'link'=>'city/index',
								        ),
								        array(
								                'title'=>'县(区域)管理',
								                'link'=>'zone/index',
								        ),
								        array(
								                'title'=>'镇(街道)管理',
								                'link'=>'street/index',
								        ),
								),
							),
				),
		),
);
