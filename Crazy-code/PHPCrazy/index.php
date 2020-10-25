<?php
/*
*	PHPCrazy 入口文件
*	
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

try {

	define('IN_PHPCRAZY', true);

	// 加载 PHPCrazy 核心文件
	require_once 'PHPCrazy.php';

	// 加载控制层
	include_once C(obtainController());

	// 结束程序
	AppEnd();

// 抛出PDO异常
} catch (PDOException $e) {

	DEBUG(L('SQL错误'), $e->getMessage(), $e->getLine(), $e->getFile());

// 抛出异常
} catch (Exception $e) {
	
	DEBUG(L('错误'), $e->getMessage(), $e->getLine(), $e->getFile());
	
}

?>