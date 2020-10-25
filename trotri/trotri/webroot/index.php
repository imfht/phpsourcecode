<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * 为true时表示测试环境，会打印Debug日志
 */
define('DEBUG', true);

/**
 * 设置时区
 */
date_default_timezone_set('PRC');

/**
 * 定义项目名
 */
define('APP_NAME', 'site');

require '../libraries/tfc/saf/Loader.php';

if (DEBUG) {
	if (!is_file(DIR_CFG_DB . DS . 'cluster.php') && is_file(DIR_WEBROOT . DS . 'install.php')) {
		header('location: install.php');
	}
}

$bootstrap = new Bootstrap();
$bootstrap->run();

tfc\mvc\Mvc::run();
