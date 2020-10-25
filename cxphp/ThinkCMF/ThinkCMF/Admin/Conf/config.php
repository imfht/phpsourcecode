<?php

$sdk_config = (Array) F('sdk_options', '', C('CMF_CONF_PATH'));
$site_config = (Array) F('site_options', '', C('CMF_CONF_PATH'));
$config = array(
	'DEFAULT_THEME'		 => '',
	'TMPL_PARSE_STRING'	 => array(
		'__STATIC__' => __ROOT__ . '/static',
	)
);

return array_merge($site_config, $config, $sdk_config);
