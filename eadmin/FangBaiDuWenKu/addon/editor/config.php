<?php
return array(
	'editor_resize_type'=>array(//配置在表单中的键名 ,这个会是config[random]
		'title'=>'高度自动:',//表单的文字
		'type'=>'radio',		 //表单的类型：text、textarea、checkbox、radio、select等
		'options'=>array(		 //select 和radion、checkbox的子选项
			'1'=>'开启',		 //值=>文字
			'0'=>'关闭',
		),
		'value'=>'1',			 //表单的默认值
		'labelwidth'=>'150px',
		//textarea表单含有以下元素colsrows,password和text含有以下元素width，所有元素都含有标签宽度labelwidth
	),
	'editor_height'=>array(//配置在表单中的键名 ,这个会是config[random]
		'title'=>'编辑器高度:',//表单的文字
		'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
		'value'=>'300',			 //表单的默认值
		'labelwidth'=>'150px',
	    'width'=>'250px',
	'tip'=>'单位为px'
		//textarea表单含有以下元素colsrows,password和text含有以下元素width，所有元素都含有标签宽度labelwidth
	),
);
					