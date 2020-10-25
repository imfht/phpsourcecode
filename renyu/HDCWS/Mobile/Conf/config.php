<?php

return array_merge(include_once('../Config/config.php'), include_once('../Config/config.cache.php'), array(

	'TMPL_ACTION_ERROR'     => APP . '/Tpl/Public/jump.html',

    'TMPL_ACTION_SUCCESS'   => APP . '/Tpl/Public/jump.html',

    'TMPL_EXCEPTION_FILE'   => APP . '/Tpl/Public/exception.html',	

	'TMPL_PARSE_STRING' => array(

		'__JS__' =>  __ROOT__. '/' . APP_NAME . '/Public/js',

		'__CSS__' => __ROOT__. '/' . APP_NAME . '/Public/css',

		'__IMAGES__' => __ROOT__. '/' . APP_NAME . '/Public/images'

	)

));