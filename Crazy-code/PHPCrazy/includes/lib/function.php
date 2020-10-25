<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

/////////////////////////////// 系统常用 /////////////////////////////

/*
*	自动加载类
*	我们只需要把类文件按以下规则保存到 lib 目录下即可：
*		class.类名.php
*	需要使用的时候直接 new 类名() 即可
*	无需使用 (include/require/require_once/include_once) 加载
*/
function AutoloadClass($classname) {

	$classname = preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, ltrim($classname, '\\'));

    $filename = dirname(__FILE__).DIRECTORY_SEPARATOR.$classname.'.class.php';
    
    if (is_readable($filename)) {

        require_once $filename;
    }
}

/*
*	加载函数
*	LoadFunc(函数类型)
*	lib目录内的函数命名规则：
*		func.函数类型.php
*/
function LoadFunc($function_type) {

	$filename = dirname(__FILE__).DIRECTORY_SEPARATOR.$function_type.'.func.php';

    if (is_readable($filename)) {

        require_once $filename;
    }
}

/*
*	加载语言
*	Lang(语言类型)
*	备注：语言文件命名规范 lang-类型.php
*/
function Lang($LangType) {

	$langFilename = ROOT_PATH.DIRECTORY_SEPARATOR.
		'includes'.DIRECTORY_SEPARATOR.
		'lang'.DIRECTORY_SEPARATOR.
		$GLOBALS['C']['lang'].DIRECTORY_SEPARATOR.
		'lang-'.$LangType.'.php';

	if (file_exists($langFilename)) {
		
		return $langFilename;
	}

	AppEnd("Language Package $LangType Not exists!");

}

/*
* 	错误收集/DEBUG模式
*	
* 	DEBUG(DEBUG类型, 网页标题, 提示内容, 错误的行, 错误的文件, 执行的SQL)
*/
function DEBUG($title, $msg, $line, $file) {

	// 如果没有开启 DEBUG 模式则直接返回忽略
	if (!DEBUG) {

		return;
	}

	// 防止输出磁盘目录
	$file = basename($file);

	// 定义网页标题
	$GLOBALS['PageTitle'] = $title;

	// 清空缓冲区
	ob_end_clean();

	// 加载 DEBUG 主题模板
	include T('debug');

	// 结束程序
	AppEnd();

}

/*
*	返回一条网页提示信息
*	Message(报告级别, 网页标题, 提示内容)
*/
function Message($level, $page_title, $message) {

	// 清除缓冲
	ob_end_clean();

	// 提示页面的标题
	$GLOBALS['PageTitle'] = $page_title;

	// 加载提示页面的主题模板
	include T('message');

	// 终止程序
	AppEnd();
}

/*
*	获取控制层
*/
function obtainController() {

	if(preg_match('/([0-9a-zA-Z]{1,20}\:[0-9a-zA-Z]{1,20})/', $_SERVER["REQUEST_URI"], $match)) {

		return str_replace(':', DIRECTORY_SEPARATOR, $match[1]);
	}

	return 'main'.DIRECTORY_SEPARATOR.'home';
}

/*
*	返回要加载控制层
*	C(控制层名)
*/
function C($ControlName) {

	//$ControlName = str_replace('\\', DIRECTORY_SEPARATOR, $ControlName);

	$ControlFile = ROOT_PATH.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.$ControlName.'.php';

	if (file_exists($ControlFile)) {
		
		return $ControlFile;

	}

	ob_end_clean();

	$PDO = null;

	AppEnd("Unable to load control $ControlName !!");
}

/*
*	返回要加载的主题模板
*	T(模版名)
*/
function T($tpl_name) {

	global $PDO;

	// Linux 系统为 \ Windows \ 或 / 都可以
	$tpl_name = str_replace('\\', DIRECTORY_SEPARATOR, $tpl_name);

	// admin 的主题文件
	$admin_filename = ADMIN_ROOT_PATH.DIRECTORY_SEPARATOR.'theme'.DIRECTORY_SEPARATOR.$tpl_name.'.tpl.php';
	
	// 用户层的主题文件
	$main_filename = ThemePath(true).$tpl_name.'.tpl.php';

	// 取得对应的主题文件
	$filename = (defined('IN_ADMIN')) ? $admin_filename : $main_filename;

	// 先检查主题文件是否存在, 防止 include 出错
	if (file_exists($filename)) {

		return $filename;
	}

	// 清空缓冲
	ob_end_clean();

	// 断开数据库连接
	$PDO = null;

	// 返回一条出错信息
	AppEnd("<strong>{$GLOBALS['C']['theme']}::<span style=\"color: red;\">$tpl_name.tpl.php</span> Not exista!!</strong>");
}

/*
*	加载翻译的语言
*	L(键名)
*/
function L($lang_key) {

	// 检查键值是否存在, 如果存在则返回键值
	if (isset($GLOBALS['lang'][$lang_key])) {

		return $GLOBALS['lang'][$lang_key];
	}

	// 键值不存在, 直接键名
	return $lang_key;

}

/*
*	获取网站首页 URL
*	HomeUrl()
*/
function HomeUrl($append = '') {

	// HTTPS
	$http = ($GLOBALS['C']['http_secure']) ? 'https://' : 'http://';

	// 会的服务器名(域名)
	$server_name = $_SERVER['HTTP_HOST'];

	// 程序安装目录
	$app_path = $GLOBALS['C']['app_path'];

	// Url
	$home_url = $http . $server_name . $app_path . $append;

	return $home_url;
}

/**
 *	生成登录重定向URL
 *	
 *	$location 为登录重定向的URL
 */
function UrlL($location) {

	return HomeUrl('index.php/main:login/?location='.urlencode($location));
}

/*
*	返回从根目录到主题路径的路径或URL
*	ThemePath(值)
*	值为 true 则返回主题的路径
*	值为 false 则返回主题的Url
*/
function ThemePath($Method = false) {

	// 主题目录
	$ThemePath = 'themes'.DIRECTORY_SEPARATOR.$GLOBALS['C']['theme'].DIRECTORY_SEPARATOR;

	// 主题目录的Url
	$ThemeUrl = HomeUrl() . $ThemePath;

	// 主题目录的路径
	$ThemePath = ROOT_PATH . DIRECTORY_SEPARATOR . $ThemePath;

	// 值为 true 则返回主题的路径
	// 值为 false 则返回主题的Url
	return ($Method) ? $ThemePath : $ThemeUrl;

}

/*
*	更新指定的系统配置到数据库
*/
function UpConfig($config_name, $config_value) {

	global $PDO;

	$sql = 'UPDATE ' . CONFIG_TABLE . "
		SET config_value = :config_value 
		WHERE config_name = :config_name";

	$result = $PDO->prepare($sql);

	$result->execute(array(
		':config_value' => $config_value,
		':config_name' => $config_name)
	);


}

/*
*	初始化分页的$start变量
*/
function InitStart($per) {

	if ( isset($_POST['start1']) ) {

		$start1 = abs(intval($_POST['start1']));
		$start1 = ($start1 < 1) ? 1 : $start1;
		$start = (($start1 - 1) * $per);
	} else {
		$start = ( isset($_GET['start']) ) ? intval($_GET['start']) : 0;
		$start = ($start < 0) ? 0 : $start;
	}
	
	return $start;
}

/*
* 	终结程序
*	AppEnd()
*/
function AppEnd($Message = '') {
	
	global $PDO;

	// 断开数据库连接
	$PDO = null;

	// 终止脚本
	exit($Message);
}

?>