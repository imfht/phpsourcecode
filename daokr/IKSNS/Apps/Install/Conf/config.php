<?php
// +----------------------------------------------------------------------
// | IKPHP.COM [ I can do all the things that you can imagine ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2050 http://www.ikphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 小麦 <810578553@qq.com> <http://www.ikcms.cn>
// +----------------------------------------------------------------------

/**
 * 安装程序配置文件
 */
return array (

		'URL_MODEL' => '0',
		'OUTPUT_ENCODE' => false,
		'APP_DEBUG' => false,
		'DB_FIELD_CACHE' => false,
		'HTML_CACHE_ON' => false,
		'URL_CASE_INSENSITIVE' => false,

	    /* 模板相关配置 */
	    'TMPL_PARSE_STRING' => array(
	        '__TMPL_STATIC__' => APP_PATH . 'Install/View/Public',
	    ),
);
?>