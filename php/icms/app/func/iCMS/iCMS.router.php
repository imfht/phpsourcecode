<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
function iCMS_router($vars){
	if(empty($vars['url'])){
		echo 'javascript:;';
		return;
	}

	$print = isset($vars['print'])?$vars['print']:true;

	$as    = $vars['as'];
	$url   = $vars['url'];

	if(isset($vars['set'])){
		$GLOBALS['iCMS:router'] = $vars;
		return;
	}
	iView::assign('Router',null);
	if($url==$GLOBALS['iCMS:router']['url']){
		iView::assign('Router',$GLOBALS['iCMS:router']);
	}

	unset($vars['url'],$vars['as'],$vars['print'],$vars['get']);

	$url = iURL::router($url);

	$vars['query'] && $url = iURL::make($vars['query'],$url);

	if($url && !iFS::checkHttp($url) && $vars['host']){
		$url = rtrim(iCMS_URL,'/').'/'.ltrim($url, '/');;
	}
	empty($url) && $url = 'javascript:;';

	if($as) return $url;

	$print && print($url);
	return $url;
}
