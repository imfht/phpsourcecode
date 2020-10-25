<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

define('IN_PHPCRAZY', true);

try {
	
	require_once dirname(dirname(__FILE__)).'/PHPCrazy.php';

	ob_start();
	
	require dirname(__FILE__).'/AdminInit.php';

	if (isset($_GET['action'])) {
		
		$action = $_GET['action'];

		$file = 'admin#' . $action .'.php';

		
		if( preg_match("/^admin\#[0-9a-z_\-]*?\.php$/", $file) ) {

			if (file_exists($file)) {

				include dirname(__FILE__).'/'.$file;
			
			}

		}

		AppEnd();

	}

	$dir = @opendir(dirname(__FILE__));

	$setModule = true;

	while( $file = @readdir($dir) ) {
		
		if( preg_match("/^admin\#[0-9a-z_\-]*?\.php$/", $file) ) {

			include($file);
		}
	}

	@closedir($dir);

	unset($setModules);

	$PageTitle = L('管理');

	include T('admin');

	AppEnd();

} catch (PDOException $e) {

	DEBUG(L('SQL错误'), $e->getMessage(), $e->getLine(), $e->getFile());

} catch (Exception $e) {
	
	DEBUG(L('错误'), $e->getMessage(), $e->getLine(), $e->getFile());
	
}

?>