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

/**
 * 自定义URL模式
 * @param unknown_type $mod		模型
 * @param unknown_type $act		控制
 * @param unknown_type $vars	变量
 */
function diyU( $mod, $act, $vars, $domain=true, $redirect=false, $suffix=true){
	parse_str(html_entity_decode($vars));
	$suffix = $suffix ? ($suffix=($suffix = $suffix===true?C('URL_HTML_SUFFIX'):$suffix) ? '.'.ltrim($suffix,'.'):''):'';
	$vars = empty($vars) ? '':"?$vars";
	switch( $mod){
		case 'index':$url = "$act$suffix".$vars;break;
		case 'page':$url="page/$page$suffix";break;
		case ($mod=='category' && $act=='categoryDetail'):$url = "article/$aid$suffix";break;
		case ($mod=='extendForms'):$url = "form/$act$suffix".$vars;break;
		default: $url="$mod/$act$suffix".$vars;break;
	}
	if( $redirect) redirect( $domain.$url);
	return $domain.$url;
}