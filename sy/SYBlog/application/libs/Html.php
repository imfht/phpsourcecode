<?php

/**
 * 经过扩展的HTML操作类
 * 
 * @author ShuangYa
 * @package Blog
 * @category Library
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=blog&type=license
 */

namespace blog\libs;
use \Sy;
use \sy\base\Router;
use \sy\lib\Html as YHtml;

class Html extends YHtml {
	/**
	 * 输出link标签
	 * @param array $set 自设定标签
	 * @return string
	 */
	public static function link($set = NULL) {
		$set = (array)$set;
		$out = [
			['rel' => 'EditURI', 'type' => Sy::getMimeType('rsd'), 'title' => 'RSD', 'href' => Router::createUrl('index/xmlrpc/rsd', 'xml')],
			['rel' => 'wlwmanifest', 'type' => 'application/wlwmanifest+xml', 'href' => Router::createUrl('index/xmlrpc/wlw', 'xml')],
			['rel' => 'alternate', 'type' => Sy::getMimeType('rss'), 'title' => 'RSS 2.0', 'href' => Router::createUrl('index/feed/rss', 'xml')],
			['rel' => 'alternate', 'type' => Sy::getMimeType('rdf'), 'title' => 'RSS 1.0', 'href' => Router::createUrl('index/feed/rdf', 'xml')],
			['rel' => 'alternate', 'type' => Sy::getMimeType('atom'), 'title' => 'atom 1.0', 'href' => Router::createUrl('index/feed/atom', 'xml')]
		];
		$out = array_merge($out, $set);
		$r = '';
		foreach ($out as $v) {
			$r .= '<link ';
			foreach ($v as $kk => $vv) {
				$r .= $kk . '="' . $vv . '" ';
			}
			$r .= '/>';
			$r .= "\n";
		}
		return $r;
	}
}
