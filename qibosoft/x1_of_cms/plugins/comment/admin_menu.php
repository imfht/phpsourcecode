<?php

return array(
		'plugins'=>array(
				'title'=>'comment',
				'sons'=>array(
							array(
								'title'=>'功能设置',
								'sons'=>array(
								        array(
								                'title'=>'评论内容管理',
								                'link'=>'content/index',
								            'power'=>['delete','edit'=>'审核'],
								            ),
								        array(
								                'title'=>'评论参数设置',
								                'link'=>'setting/index',
								                'power'=>[],
								        ),
								       
								),
							),
				),
		),
);
