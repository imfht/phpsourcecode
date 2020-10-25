<?php
if ( strnatcasecmp(phpversion(),'5.3') <= 0 )
{
	exit(" Must use php 5.3+");
}

date_default_timezone_set("Asia/Shanghai");

error_reporting(E_ALL & ~E_NOTICE);

define('ZEUS_TIME',microtime(true));
define('Author', 'zeus @ nathena');
define('VER', 'v1.0.0');
define("DS",DIRECTORY_SEPARATOR);
define('ZEUS', dirname(__FILE__));
define("ROOT", dirname(__DIR__));

//can configure

defined("DEBUG") || define("DEBUG",true);
defined("LOGLEVEL") || define("LOGLEVEL",0);////0 = DEBUG, 1 = info, 2 = warn, 3 = error
defined("LOG") || define("LOG",ROOT.DS."log");
defined("TEMPLATE") || define("TEMPLATE",ROOT.DS."template");
defined("THEME") || define("THEME","");
defined("INCLUDE_PATH") || define("INCLUDE_PATH",ROOT.DS."application");

//configure end
ini_set("display_errors", DEBUG ? "on" : "Off");

set_include_path('.'.PATH_SEPARATOR.ROOT);

$paths = explode(",",INCLUDE_PATH);
if( !empty($paths) )
{
	foreach($paths as $path )
	{
		set_include_path(get_include_path().PATH_SEPARATOR.$path);
	}
}

/**
 * 获取当前客户端IP
 * @return Ambigous <string, unknown>
 */
function ip()
{
	static $_ip;
	if( !isset($_ip) )
	{
		$cip = getenv('HTTP_CLIENT_IP');
		$xip = getenv('HTTP_X_FORWARDED_FOR');
		$rip = getenv('REMOTE_ADDR');
		$srip = $_SERVER['REMOTE_ADDR'];
		if($cip && strcasecmp($cip, 'unknown'))
		{
			$_ip = $cip;
		}
		elseif($xip && strcasecmp($xip, 'unknown'))
		{
			$_ip = $xip;
		}
		elseif($rip && strcasecmp($rip, 'unknown'))
		{
			$_ip = $rip;
		}
		elseif($srip && strcasecmp($srip, 'unknown'))
		{
			$_ip = $srip;
		}
		else
		{
			$_ip = "";
		}
	}
	return $_ip;
}

/**
 * 重定向
 * @param String $url
 * @param String $message
 * @param String $delay
 */
function redirect($url, $message = '', $delay = 1)
{
	ob_end_clean();
	if (!empty($message) && $delay > 0)
	{
		echo <<<HTML
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta http-equiv="refresh" content="{$delay};URL={$url}" />
</head>
<body>
<div style="width:800px;border:1px solid #FF0000; position:absolute; left:50%; top:50%;
margin-left:-400px; margin-top:-15px; z-index:1; background-color:#FFF2E9; text-align:center; padding:8px;
font:12px Verdana, Lucida, Helvetica, Arial, sans-serif;">{$message}</div>
</body>
</html>
HTML;
	}
	else if (!headers_sent())
	{
		header('Location:' . $url);
	}
	else
	{
		echo <<<HTML
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta http-equiv="refresh" content="0;URL={$url}" />
</head>
<body>
</body>
</html>
HTML;
	}
	exit;
}

function tpl( $tpl )
{
	$_tpl = realpath(TEMPLATE.DS.THEME.DS.$tpl);
	if(!$_tpl)
	{
		$_tpl = realpath(TEMPLATE.DS.$tpl);
	}
	return $_tpl;
}


/**
 * 输出Debug信息
 * @author nathena
 */
function zDebug()
{
	echo '<br />'.PHP_EOL;
	echo 'Used Time ' . ( microtime(true) - ZEUS_TIME ) . '<br />' . PHP_EOL;
	echo 'Used memory ' . memory_get_usage() . '<br />' . PHP_EOL;

	$files = get_included_files();

	foreach( $files as $file )
	{
		echo $file . '<br />' . PHP_EOL;
	}
}

/**
 *
 * @param String $path
 * @return mixed
 */
function zRealpath($path)
{
	return str_replace(array('/', '\\', '//', '\\\\'), DS, $path);
}

/**
 * mkdir新型
 * @param String $dir
 * @param int $mode
 * @return String
 */
function zMkdir($dir, $mode = 0777)
{
	if(!file_exists($dir))
	{
		$old = umask(0);
		mkdir($dir, $mode,true);
		umask($old);
	}
	return $dir;
}

/**
 * 是否空文件夹
 * @param Stirng $dir
 */
function zEmptyDir($dir)
{
	$handle = opendir($dir);
	while (false !== ($file = readdir($handle)))
	{
		if ($file != '.' && $file != '..')
		{
			closedir($handle);
			return false;
		}
	}
	closedir($handle);
	return true;
}

/**
 * 删除目录
 * @param String $dir
 * @return boolean
 */
function zUnlink($dir)
{
	$handle = opendir($dir);
	while (false !== ($file = readdir($handle)))
	{
		if ($file == '.' || $file == '..') continue;
		$filename = $dir . DS . $file;
		if (filetype($filename) == 'dir')
		{
			zUnlink($filename);
		}
		else
		{
			unlink($filename);
		}
	}
	closedir($handle);
	if(emptyDir($dir))
	{
		rmdir($dir);
	}
	return true;
}

/**
 * 复制目录
 * @param String $source
 * @param String $dest
 * @return boolean
 */
function zCopyDir($source, $dest)
{
	$source = rtrim($source, '\\/') . DS;
	$dest = rtrim($dest, '\\/') . DS;
	zMkdir($dest, 0755);
	$handle = opendir($source);
	while (false !== ($filename = readdir($handle)))
	{
		$file = $source . $filename;
		if (is_file($file))
		{
			copy($file, $dest . $filename);
		}
	}
	closedir($handle);
	return true;
}

/**
 * 文件后缀
 * @param String $file
 * @return String
 */
function zFileExt($file)
{
	return end(explode(".", $file));
}

function generateUUID($length=16)
{
	$noncestr = str_shuffle("abcdefghigklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890");

	return substr($noncestr,0,max(4,$length));
}

function generateNumberUUID($length=4)
{
	$noncestr = str_shuffle("1234567890");

	return substr($noncestr,0,$length);
}


include_once "__loader.php";