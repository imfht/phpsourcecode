<?php

// 后台配置，与前台配置基本一致，主要为模板相关
// model_path 可以为数组，会按照顺序搜索目录
return  array (
	'app_id' => 'bbsadmin',
	'app_url' => $bbsconf['app_url'].'admin/',
	'control_path' => array(BBS_PATH.'admin/control/'),
	'static_url' => $bbsconf['static_url'].'admin/',
	'view_path' => array(BBS_PATH.'admin/view/'),
);

?>