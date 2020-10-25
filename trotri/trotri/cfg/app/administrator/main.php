<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

return array (
	'encoding' => 'utf-8', // 项目编码，不区分大小写
	'language' => 'zh-CN', // 输出的语言种类，区分大小写
	'fontfile' => DIR_DATA . DS . 'fonts' . DS . 'PilsenPlakat.ttf', // 字体名
	'picture_server' => '', // 图片服务器地址
	'view' => array (
		'skin_name' => 'bootstrap',     // 模板风格
		'charset' => 'utf-8',           // HTML编码
		'tpl_extension' => '.php',      // 模板后缀
		'version' => '1.0',             // Js、Css文件的版本号
		'skin_version' => '3.0.3',      // 模板风格文件的版本号
	),
	'paginator' => array (
		'page_var' => 'paged',      // 从$_GET或$_POST中获取当前页的键名，缺省：paged
		'list_rows_var' => 'limit', // 从$_GET或$_POST中获取每页展示的行数的键名，缺省：limit
		'list_rows' => 50,           // 每页展示的行数
		'list_pages' => 4,          // 每页展示的页码数
	),
	'account' => array (
		'key_name' => 'auth_administrator',      // 密钥配置名
		'domain' => '',                          // Cookie的有效域名，缺省：当前域名
		'path' => '/',                           // Cookie的有效服务器路径，缺省：/
		'secure' => false,                       // FALSE：HTTP和HTTPS协议都可传输；TRUE：只通过加密的HTTPS协议传输，缺省：FALSE
		'httponly' => true,                      // TRUE：只能通过HTTP协议访问；FALSE：HTTP协议和脚本语言都可访问，容易造成XSS攻击，缺省：TRUE
		'expiry' => WEEK_IN_SECONDS,             // 记住密码时间
		'cookie_name' => 'atrid',                // Cookie名
		'cookset_password' => false,             // Cookie中设置密码
		'cookset_rolenames' => true,             // Cookie中设置用户拥有的角色名
		'cookset_appnames' => true,              // Cookie中设置用户拥有权限的项目名
	),
	'cookie' => array (
		'key_name' => 'cookie',         // 密钥配置名
		'domain' => '',                 // Cookie的有效域名，缺省：当前域名
		'path' => '/',                  // Cookie的有效服务器路径，缺省：/
		'secure' => false,              // FALSE：HTTP和HTTPS协议都可传输；TRUE：只通过加密的HTTPS协议传输，缺省：FALSE
		'httponly' => true,             // TRUE：只能通过HTTP协议访问；FALSE：HTTP协议和脚本语言都可访问，容易造成XSS攻击，缺省：TRUE
	),
	'navbar' => require_once 'navbar.php',
	'upload' => require_once 'upload.php',
);
