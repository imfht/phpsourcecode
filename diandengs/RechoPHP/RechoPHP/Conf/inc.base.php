<?php
// +----------------------------------------------------------------------
// | RechoPHP [ WE CAN DO IT JUST Better ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010-2014 http://recho.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: recho <diandengs@gmail.com>
// +----------------------------------------------------------------------

@session_start();
define('MODEL_NAME', empty($_REQUEST['mod']) ? 'Index':ucfirst($_REQUEST['mod']));
define('ACTION_NAME', empty($_REQUEST['act']) ? 'Index':$_REQUEST['act']);
$confDir0 = RC_PATH_CFG;$o = opendir($confDir0);
while($file = readdir($o)){
	if( is_file($file=$confDir0.$file)){
		$f = require_once($file);
		if( is_array($f)) C($f);
	}
}
closedir($o);
$confDir1 = WWWROOT.'Conf/';$o = opendir($confDir1);
while($file = readdir($o)){
	if( is_file($file=$confDir1.$file)){
		$f = require_once($file);
		if( is_array($f)) C($f);
	}
}
closedir($o);