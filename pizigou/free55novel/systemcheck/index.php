<?php

$requirements=array(
	array(
		t('yii','PHP version'),
		true,
		version_compare(PHP_VERSION,"5.3.0",">="),
		'<a href="http://www.free55.net">飞舞小说系统</a>',
		t('yii','PHP 5.3.0 or higher is required')),
	array(
		t('yii','$_SERVER variable'),
		true,
		($message=checkServerVar()) === '',
		'<a href="http://www.free55.net">飞舞小说系统</a>',
		'系统核心'),
	array(
		t('yii','Reflection extension'),
		true,
		class_exists('Reflection',false),
		'<a href="http://www.free55.net">飞舞小说系统</a>',
		'系统核心'),
	array(
		t('yii','PCRE extension'),
		true,
		extension_loaded("pcre"),
		'<a href="http://www.free55.net">飞舞小说系统</a>',
		'系统核心'),
	array(
		t('yii','SPL extension'),
		true,
		extension_loaded("SPL"),
		'<a href="http://www.free55.net">飞舞小说系统</a>',
		'系统核心'),
	array(
		t('yii','GD extension'),
		true,
		($message=checkGD()) === '',
		'<a href="http://www.free55.net">飞舞小说系统</a>',
		'用于图像处理'),
	array(
		t('yii','cUrl extension'),
		true,
		extension_loaded("curl"),
		'<a href="http://www.free55.net">飞舞小说系统</a>',
		'用于加载远程数据'),
	array(
		t('yii','PDO extension'),
		true,
		extension_loaded('pdo'),
		'<a href="http://www.free55.net">飞舞小说系统</a>',
		t('yii','用于访问MySQL数据库')),
	array(
		t('yii','PDO MySQL extension'),
		true,
		extension_loaded('pdo_mysql'),
		'<a href="http://www.free55.net">飞舞小说系统</a>',
		t('yii','用于访问MySQL数据库')),
	array(
		t('yii','Url rewrite is open'),
		true,
		checkRewrite(),
		'<a href="http://www.free55.net">飞舞小说系统</a>',
		t('yii','用于生成有利于SEO的URL')),
);

function checkServerVar()
{
	$vars=array('HTTP_HOST','SERVER_NAME','SERVER_PORT','SCRIPT_NAME','SCRIPT_FILENAME','PHP_SELF','HTTP_ACCEPT','HTTP_USER_AGENT');
	$missing=array();
	foreach($vars as $var)
	{
		if(!isset($_SERVER[$var]))
			$missing[]=$var;
	}
	if(!empty($missing))
		return t('yii','$_SERVER does not have {vars}.',array('{vars}'=>implode(', ',$missing)));

	if(realpath($_SERVER["SCRIPT_FILENAME"]) !== realpath(__FILE__))
		return t('yii','$_SERVER["SCRIPT_FILENAME"] must be the same as the entry script file path.');

	if(!isset($_SERVER["REQUEST_URI"]) && isset($_SERVER["QUERY_STRING"]))
		return t('yii','Either $_SERVER["REQUEST_URI"] or $_SERVER["QUERY_STRING"] must exist.');

	if(!isset($_SERVER["PATH_INFO"]) && strpos($_SERVER["PHP_SELF"],$_SERVER["SCRIPT_NAME"]) !== 0)
		return t('yii','Unable to determine URL path info. Please make sure $_SERVER["PATH_INFO"] (or $_SERVER["PHP_SELF"] and $_SERVER["SCRIPT_NAME"]) contains proper value.');

	return '';
}

function checkGD()
{
	if(extension_loaded('gd'))
	{
		$gdinfo=gd_info();
		if($gdinfo['FreeType Support'])
			return '';
		return t('yii','GD installed<br />FreeType support not installed');
	}
	return t('yii','GD not installed');
}

function checkRewrite()
{
  if (extension_loaded('curl')) {
    $url = 'http://'.$_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];

    $t = explode("systemcheck", $url);

    $url = $t[0] . "install/index";

    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_HEADER, TRUE); 
    curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    $head = curl_exec($ch); 
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
    curl_close($ch); 
    
    if($httpCode < 400) 
    {
      return TRUE; 
    }
  }
  return FALSE;
}


function getYiiVersion()
{
	$coreFile=dirname(__FILE__).'/../version.php';
	if(is_file($coreFile))
	{
    require_once $coreFile;

    return FWXSVersion;
	}
	return '1.2.x';
}

/**
 * Returns a localized message according to user preferred language.
 * @param string message category
 * @param string message to be translated
 * @param array parameters to be applied to the translated message
 * @return string translated message
 */
function t($category,$message,$params=array())
{
	static $messages;

	if($messages === null)
	{
		$messages=array();
		if(($lang=getPreferredLanguage()) !== false)
		{
			$file=dirname(__FILE__)."/messages/$lang/yii.php";
			if(is_file($file))
				$messages=include($file);
		}
	}

	if(empty($message))
		return $message;

	if(isset($messages[$message]) && $messages[$message] !== '')
		$message=$messages[$message];

	return $params !== array() ? strtr($message,$params) : $message;
}

function getPreferredLanguage()
{
	return "zh_cn";
}

function getServerInfo()
{
	$info[]=isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '';
	$info[]='<a href="http://www.free55.net/">飞舞小说系统</a>/'.getYiiVersion();
	$info[]=@strftime('%Y-%m-%d %H:%M',time());

	return implode(' ',$info);
}

function renderFile($_file_,$_params_=array())
{
	extract($_params_);
	require($_file_);
}

$result=1;  // 1: all pass, 0: fail, -1: pass with warnings

foreach($requirements as $i=>$requirement)
{
	if($requirement[1] && !$requirement[2])
		$result=0;
	else if($result > 0 && !$requirement[1] && !$requirement[2])
		$result=-1;
	if($requirement[4] === '')
		$requirements[$i][4]='&nbsp;';
}

//if (1 == $result) {
//  header("location: ../index.php/install/index");
//  exit;
//}

$lang=getPreferredLanguage();
$viewFile=dirname(__FILE__)."/views/$lang/index.php";
if(!is_file($viewFile))
	$viewFile=dirname(__FILE__).'/views/index.php';

renderFile($viewFile,array(
	'requirements'=>$requirements,
	'result'=>$result,
	'serverInfo'=>getServerInfo()));

