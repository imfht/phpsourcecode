<?php
/**
 * Created by PhpStorm.
 * Author: William
 * Date: 2016/9/13
 * Time: 13:57
 */

/**
 * 生成访问路径
 * @param $uri string 访问路径
 * @param $ext string 拓展名 .php .html
 */
if (!function_exists('base_url')) {
	function base_url($uri, $ext = '' ,$protocol = '')
	{
		if(!$protocol){
			$protocol = 'http://';
		}
		$url = BASEURL . $uri . $ext;
		$url = str_replace('//', '/', $url);
		return $protocol . $url;
	}
}


