<?php

return [    
    'title'=>'线条',
	'form'=>[
		['color','line_color','线条颜色','','#cccccc'],
		['number','line_height','线条高度','',1],
		['radio','line_type','线条类型','',['solid'=>'实线','dashed'=>'虚线','dotted'=>'点状'],'dashed'],
		['number','line_top','上边距离','',5],
		['number','line_bottom','下边距离','',5],
		['number','line_left','左边距离','',10],
		['number','line_right','右边距离','',10],
	],
	'type1'=>'', //wap 或 pc
	'type2'=>'', //www 或 hy 
	'type3'=>'', //这一项是为PC考虑的,WAP不需要考虑,可设置big small 或留空. big代表很宽的 small代表窄边
];