<?php

return array_merge(include_once('../Config/config.php'), array(

	'TMPL_ACTION_ERROR'     =>  './Tpl/Global/jump.html',

    'TMPL_ACTION_SUCCESS'   => './Tpl/Global/jump.html',

    'TMPL_EXCEPTION_FILE'   => './Tpl/Global/exception.html',

	'TMPL_PARSE_STRING' => array(

		'__JS__' =>  __ROOT__ . '/' . APP_NAME . '/Public/js',

		'__CSS__' => __ROOT__ . '/' . APP_NAME . '/Public/css',

		'__IMAGES__' => __ROOT__ . '/' . APP_NAME . '/Public/images'

	)	

));