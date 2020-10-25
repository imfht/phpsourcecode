<?php

//配置文件
return [
    // 视图输出字符串内容替换  
	'view_replace_str' => [
		'__IMG__'    => STATIC_URL . '/install/images',
        '__CSS__'    => STATIC_URL . '/install/css',
        '__JS__'     => STATIC_URL . '/install/js',
        '__LIB__'    => STATIC_URL . '/common/lib',
        '__ZUI__'    => STATIC_URL . '/common/lib/zui',
        
		'__NAME__'=>'MuuCmf T5开源开发框架',
        '__COMPANY__'=>'北京火木科技有限公司',
        '__WEBSITE__'=>'www.muucmf.cn',
        '__COMPANY_WEBSITE__'=>'www.hoomuu.cn'
	],
];