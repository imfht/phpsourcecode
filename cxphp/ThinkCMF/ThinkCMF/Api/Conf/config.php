<?php

$config = array(
	'TMPL_PARSE_STRING' => array(
		'__STATIC__' => __ROOT__ . '/static',
	)
);
$site_config = (Array) F('site_options', '', C('CMF_CONF_PATH'));
$sdk_config = (Array) F('sdk_options', '', C('CMF_CONF_PATH'));

return array_merge($config, $site_config, $sdk_config);
