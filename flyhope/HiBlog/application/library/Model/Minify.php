<?php

/**
 * JS/CSS压缩工具
 *
 * @package Model
 * @author  chengxuan <i@chengxuan.li>
 */
namespace Model;
class Minify extends Abs {
	
	/**
	 * 获取Minify 组设置
	 * 
	 * @return array
	 */
	static public function loadGroupConfig() {
		$config = include CONF_PATH . '/minify-groups.php';
		
		foreach($config as $path => $files) {
			$extension = pathinfo($path, PATHINFO_EXTENSION);
			foreach($files as $key => $file) {
				$file = '//' . \Comm\View::path("static/dev-{$extension}/{$file}.{$extension}");
				$config[$path][$key] = $file;
			}
		}
		return $config;
	}
} 