<?php
/**
 * Printemps Framework Requirement Initialize File
 * Printemps Framework 初始化文件
 * (c)2015 Printemps Framework DevTeam
 */

$frameWork = APP_CORE_PATH.'Printemps/';
$frameworkDir = dir($frameWork);

/**
 * 加载Printemps Framework 文件夹下所有后缀为 .pri.php 的文件 
 * .pri.php 是 Printemps Framework 的核心文件
 */
while($file = $frameworkDir->read()){
	if(!is_dir("$frameWork/$file")){
		if(preg_match("/(.*?)(\.pri\.php)$/",$file) || preg_match("/(.*?)(\.ext\.php)$/", $file))
			require $frameWork.$file;
	}
}
/**
 * 通过spl_autoload_register 方法来引用未遇到的类文件
 * 需要PHP 5.0 以上版本 :) 
 * 例如PrintempsFramework遇到未知类 indexController，将会自动引用本文件目录下的 /Controller/index.class.php
 */
spl_autoload_register(function($class){
	$name = $class.'.php';
	$className = str_replace("Controller.php",".class.php",$name);
	require APP_CORE_PATH.'Controller/'.$className;
});