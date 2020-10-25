<?php
return array(
	'second'=>array(
		'title'=>'轮播间隔时间（单位 毫秒）:',
		'type'=>'text',
		'value'=>'3000',			 //表单的默认值
	),
    'url'=>array(
        'title'=>'图片链接（一行对应一个图片）',
        'type'=>'textarea',
        'value'=>''
    ),
    'images'=>array(
        'title' => '轮播图片（双击可移除）',
        'type'  => 'picture_union',
        'value' => ''
    )
);
					