<?php

return array(
	'link_type'=>array(//配置在表单中的键名 ,这个会是config[random]
		'title'=>'使用类型:',	 //表单的文字
		'type'=>'select',		 //表单的类型：text、textarea、checkbox、radio、select等
		'options'=>array(		 //select 和radion、checkbox的子选项
			'1'=>'文字',		 //值=>文字
			'2'=>'图片',
			'3'=>'图片+文字',
		),
		'value'=>'1',			 //表单的默认值
	),
);
