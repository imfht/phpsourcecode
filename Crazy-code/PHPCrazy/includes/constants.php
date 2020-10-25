<?php
/*	
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

define('ADMIN_PATH', 'admin');
define('ADMIN_FILE', 'admin.php');
define('ADMIN_ROOT_PATH', ROOT_PATH . '/' . ADMIN_PATH);

// DEBUG
// true为开启, false为关闭
define('DEBUG', true);

define('DEBUG_SQL', 1);
define('DEBUG_PHP', 2);

// 游客的ID为-1
define('ANONYMOUS', -1);

// 权限
define('ANONYMOU',	-1);
define('USER', 0);
define('MASTER', 1);
define('ADMIN', 2);


// 报告级别
// 成功, 信息, 警告, 危险
define('SUCCESS', 1);
define('INFO', 2);
define('WARNING', 3);
define('DANGER', 4);


// 表的名称
define('CONFIG_TABLE', TABLE_PREFIX . 'config');
define('USERS_TABLE', TABLE_PREFIX . 'users');

?>