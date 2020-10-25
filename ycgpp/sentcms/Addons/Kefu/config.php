<?php
return array(
	'is_open'=>array(//配置在表单中的键名 ,这个会是config[random]
		'title'=>'是否开启:',	 //表单的文字
		'type'=>'select',		 //表单的类型：text、textarea、checkbox、radio、select等
		'options'=>array(		 //select 和radion、checkbox的子选项
			'1'=>'开',		 //值=>文字
			'2'=>'关',
		),
		'value'=>'1',			 //表单的默认值
	),
	'qq'=>array(
		'title'=>'客服QQ',
		'type'=>'text',
		'value'=>'',
		'tip'=>'多个QQ使用英文“,”隔开，QQ号和文字之间用“|”隔开，QQ号码在后面文字在前面'
	),
	'phone'=>array(
		'title'=>'咨询热线',
		'type'=>'text',
		'value'=>'',
		'tip'=>''
	),
	'url'=>array(
		'title'=>'留言链接',
		'type'=>'text',
		'value'=>'',
		'tip'=>'留言链接'
	),
);