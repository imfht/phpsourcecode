<?php

$site_config = (Array) F('site_options', '', C('CMF_CONF_PATH'));
$sdk_config = (Array) F('sdk_options', '', C('CMF_CONF_PATH'));

$config = array(
	'DEFAULT_THEME'			 => 'default', // 默认模板主题名称
	'TMPL_TEMPLATE_SUFFIX'	 => '.html', // 默认模板文件后缀
	'VIEW_PATH'				 => CMF_ROOT . '/static/',
);

return array_merge($config, $site_config, $sdk_config);
