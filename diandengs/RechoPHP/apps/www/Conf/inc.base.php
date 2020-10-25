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

return array(
	'BASEURL' => 'http://'.$_SERVER['HTTP_HOST']. '/',
	'WWWBASEURL' => 'http://'.$_SERVER['HTTP_HOST'].(($dir=substr( dirname($_SERVER['PHP_SELF']), 0))=='/' ? '':'dddd'),		//rc首页(没有画线)
	'WWWBASEURL/' => 'http://'.$_SERVER['HTTP_HOST'].'/'.(($dir=substr( dirname($_SERVER['PHP_SELF']), 1))=='' ? '':$dir.'/'),	//rc首页(有画线)
	'PATH_FOLDER' => '/',//网站根文件夹
	'URL_PATH_MOD' => 4,//URL模式
	'SITESEO' => 'www',//SEO数据
	'redirectURL' => urlencode($_REQUEST['redirectURL']),
);
