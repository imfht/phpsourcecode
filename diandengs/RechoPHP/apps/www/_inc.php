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

session_start();
header("X-Powered-By:Recho-2.0");
define( 'IS_IN', true);
define('WWWROOT', dirname(__FILE__).'/');
define( 'RECHO_PHP', WWWROOT.'../../RechoPHP/');
require_once RECHO_PHP .'RechoPHP.php';

//-- 共用smarty数据 --
rc::smarty()->assign(array(
	'redirectURL' => urlencode($_REQUEST['redirectURL']),
	'siteInfo' => array('siteName'=>C('SITENAME'),'title'=>C('TITLE')),
	'siteName' => C('SITENAME'),//网站名称
	'WWWROOT' => C('WWWBASEURL/'),
	'WWWROOT1' => C('WWWBASEURL'),
));