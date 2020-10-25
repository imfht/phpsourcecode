<?php
/**
 * TXTCMS 项目配置
 * @copyright			(C) 2013-2014 TXTCMS
 * @license				http://www.txtcms.com
 * @lastmodify			2014-8-8
 */
defined('INI_TXTCMS') or exit();
return array(
	'TMPL_ACTION_ERROR'     => APP_ROOT.'static/tips/_jump.html', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   => APP_ROOT.'static/tips/_jump.html', // 默认成功跳转对应的模板文件
	'URL_404_REDIRECT'=>__ROOT__.'/404.htm',  //错误页
);