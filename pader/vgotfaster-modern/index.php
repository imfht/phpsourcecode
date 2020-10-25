<?php
/**
 * VgotFaster PHP Framework V2.0
 *
 * VgotFaster 入口文件
 *
 * @link   http://vgotfaster.googlecode.com
 *         http://www.vgot.net
 * @author yp2008cn@gmail.com
 *         ypnow@163.com
 *         QQ 270075658
 */
define('VGOTFASTER', __FILE__);

/**
 * 应用框架环境配置
 * 
 * 应用程序可根据此配置读取不同的配置等
 *
 * development 本地开发环境
 * testing 测试平台
 * production 线上正式环境（生产环境）
 */
define('ENVIRONMENT', 'development');

switch (ENVIRONMENT) {
	case 'production':
		error_reporting(0);
		break;
	
	case 'testing':
	case 'development':
		error_reporting(E_ALL);
		break;
}

//应用程序与核心所在目录配置
//VgotFaster Constant
define('APPLICATION_PATH', __DIR__.'/app');
define('SYSTEM_PATH', __DIR__.'/system');

//Load VgotFaster Core Running
require SYSTEM_PATH.'/VgotFaster.php';
?>