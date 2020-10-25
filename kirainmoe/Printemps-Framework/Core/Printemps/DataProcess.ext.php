<?php
/**
 * Prinemps Framework 数据处理类
 * (C)2015 Printemps Framework All rights reserved.
 */
class DataProcess{
	/**
	 * 快速引入 css/js/图片
	 * @param  string/array $filename 文件名
	 * @param string $class 样式名称
	 * @return none/boolean
	 */
	public function import($filename, $class=''){
		$link = Printemps::parseURL();
		$assetPath = str_replace(APP_ROOT_PATH.'/',"",APP_STATIC_PATH);
		if(!is_array($filename)){
			$this->importChildren($link,$assetPath,$filename,$class);
		}
		else{
			foreach($filename as $value){
				$this->importChildren($link,$assetPath,$value,$class);
			}
		}
	}
	/**
	 * $this->import 子函数
	 * @param  string $link  网址
	 * @param  string $assetPath 静态文件目录
	 * @param  string $filename  文件名
	 * @param string $class 样式名称
	 * @return none/string
	 */
	private function importChildren($link,$assetPath,$filename,$class = ''){
		if(preg_match("/(.*?)(\.css)$/",$filename))
			echo '<link rel="stylesheet" type="text/css" href="'."{$link}{$assetPath}css/{$filename}".'">'."\n";
		elseif(preg_match("/(.*?)(\.js)$/", $filename))
			echo '<script src="'."{$link}{$assetPath}js/{$filename}".'"></script>'."\n";
		elseif(preg_match("/(.*?)(\.(png|jpg|jpeg|bmp|ico|tiff|gif))$/",$filename))
			echo '<img class="'.$class.'" src="'."{$link}{$assetPath}img/{$filename}".'">'."\n";
		else
			return false;
	}
	/**
	 * markdown 将markdown解析还原为HTML
	 * @param  string $string 要转换的字符串
	 * @return string         转换后的字符串
	 */
	public function markdown($string){
		$content = Markdown::convert($string);
		return $content;
	}
}