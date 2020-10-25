<?php
/*
*	PHPCrazy 安装程序文件
*	
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

session_start();

define('ANONYMOU',	-1);
define('USER', 0);
define('MASTER', 1);
define('ADMIN', 2);
define('ROOT_PATH', '../');

require_once 'func#install.php';

@include '../#config.php';

$lang_path = isset($_POST['lang']) ? $_POST['lang'] : 'Simplified-Chinese';

if (file_exists('../includes/lang/' . $lang_path . '/Translation.php')) {

	$_SESSION['install_config']['lang'] = $lang_path;
} else {

	$_SESSION['install_config']['lang'] = 'Simplified-Chinese';
}

$dir = @opendir('../includes/lang/' . $_SESSION['install_config']['lang']);

while( $file = @readdir($dir) ) {

	if( preg_match("/^lang-.*?\.php$/", $file) ) {
	
		include '../includes/lang/' . $_SESSION['install_config']['lang'] . '/' . $file;
	}
}

if (defined('INSTALL_FINISH')) {

	installError(L('安装完成 提示'));

}

$setup = isset($_GET['setup']) ? $_GET['setup'] : '';

$db_list = array(
	'mysql' => 'MySQL',
	'sqlite' => 'SQLite'
);
		
switch ($setup) {
	
	case 'db':

		installDB();

		break;

	case 'admin':
		
		installAdmin();

		break;

	case 'finish':
		
		require_once '../includes/lib/sql.func.php';

		installFinish();

		break;
	
	default:

		include ROOT_PATH .'includes/lib/misc.func.php';

		$nav = '<nav><ul class="nav nav-pills pull-right"><li><form action="install.php" method="post">';
			
		$nav .= LangSel($_SESSION['install_config']['lang'], 'lang');
     
        $nav .= ' <input type="submit" value="' . L('安装 选择') . '" class="btn btn-default btn-xs"></form></li></ul></nav>';

		installWelcome();

		break;
}

?>