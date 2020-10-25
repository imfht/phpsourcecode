<?php
@session_start();
@error_reporting(E_ALL ^ E_NOTICE);
header('Content-Type: text/html; charset=utf-8');
define('THISISADMINI',true);
define('SITELANGUAGE','cn');

if(isset($_GET['l'])){
    $_SESSION[TB_PREFIX.'doclang']=$_GET['l'];  
}
else{
	$_SESSION[TB_PREFIX.'doclang']=SITELANGUAGE;
}
$dirName=dirname(__FILE__);
$docConfig=$dirName.'/../config/doc-config-'.$_SESSION[TB_PREFIX.'doclang'].'.php';
if(!is_file($docConfig)||filesize($docConfig)==0||filesize($docConfig)==3){require_once($dirName.'/../inc/nosetup/setup.html');exit;}else{require_once($docConfig);}
require(ABSPATH.'/admini/config/qd-config.php');
function_exists('date_default_timezone_set') && @date_default_timezone_set('Etc/GMT-'.TIMEZONENAME);
require(ABSPATH.'/inc/function.php');
if(is_file(ABSPATH.'/inc/common.php'))require_once(ABSPATH.'/inc/common.php');
require(ABSPATH.'/inc/class.database.php');
require(ABSPATH.'/inc/class.pager.php');
require(ABSPATH.'/inc/class.ui.php');
if(empty($_SESSION[TB_PREFIX.'admin_name']) or $_SESSION[TB_PREFIX.'admin_roleId']<7){
	redirect('login.php');
}
//IP登录限制
if(defined('LOGINIP') && LOGINIP!='')
{
	$ip = explode(';',LOGINIP);
	!in_array(long2ip(getip()),$ip)?exit:'';
}

$_REQUEST = cleanArrayForMysql($_REQUEST);
$_GET 	  = cleanArrayForMysql($_GET);
$_POST 	  = cleanArrayForMysql($_POST);
$request  = $_REQUEST;

if(isset($request['l'])){
	$_SESSION[TB_PREFIX.'doclang']=$request['l'];  
}
elseif(isset($_SESSION[TB_PREFIX.'doclang'])){
	$request['l']= $_SESSION[TB_PREFIX.'doclang'];    
}
else{
	$request['l']= SITELANGUAGE;    
	$_SESSION[TB_PREFIX.'doclang']=SITELANGUAGE;
}

$request['p']=intval($request['p']);
$request['c']=intval($request['c']);
$request['n']=intval($request['n']);
$request['i']=intval($request['i']);
$request['cid']=intval($request['cid']);
$request['mdtp']=intval($request['mdtp']);
$request['comment']=intval($request['comment']);

$pageInfo=array();
$pageInfo['display']=true;
$pageInfo['header']=ABSPATH."/admini/views/header.php";
$pageInfo['footer']=ABSPATH."/admini/views/footer.php";
require_once(ABSPATH.'/admini/global.php');
switch($request['m'])
{
	case 'system':
		$module_name = empty($request['s'])?'index':$request['s'];
		$pageInfo['header']=ABSPATH."/admini/views/system_header.php";

		$controller = ABSPATH.'/admini/controllers/system/'.$module_name.'.php';
		if(is_file($controller))
		{
			require_once($controller);
			empty($request['a'])?index():(function_exists($request['a'])?$request['a']():die("无此Action #$request[a]"));
		}else{
			die('尚未安装'.$module_name.'模块。');
		}

		$view = empty($request['a'])?ABSPATH.'/admini/views/system/'.$module_name.'/index.php':ABSPATH.'admini/views/system/'.$module_name.'/'.$request['a'].'.php';
		break;
	default:
		$module_name = empty($request['p'])?'index':get_model_type($request['p']);
		$controller = ABSPATH.'/admini/controllers/'.$module_name.'.php';
		if(is_file($controller))
		{
			require_once($controller);
			require_once(ABSPATH.'/admini/controllers/comment.php');

			$view = empty($request['a'])?ABSPATH.'/admini/views/'.$module_name.'/index.php':ABSPATH.'/admini/views/'.$module_name.'/'.$request['a'].'.php';
			empty($request['a'])?index():(function_exists($request['a'])?$request['a']():die("无此Action #$request[a]"));
		}
		else die('尚未安装'.$module_name.'模块。');
}
if($pageInfo['display'])require_once($pageInfo['header']);
if(is_file($view)) 	require_once($view);
isComment();
if($pageInfo['display'])require_once($pageInfo['footer']);

function __autoload($class_name)
{
	$model = ABSPATH.'/admini/models/'.$class_name.'.php';
	if(is_file($model))require_once($model);
}

function get_model_type($id)
{
	global $db;
	return $db->get_var("SELECT `type` FROM `".TB_PREFIX."menu` WHERE id=$id");
}
function isComment()
{
	global $params,$db,$request;
	$view_path=ABSPATH.'/admini/views/comment/comment_index.php';
	if(menuIsComment())
	{
		if(is_file($view_path))
		require_once($view_path);
	}
}
function menuIsComment()
{
	global $db,$request;
	$sql = "SELECT * FROM ".TB_PREFIX."menu WHERE id=".$request['p'];
	$result = $db->get_row($sql);
	if(intval($result->isComment) == 1 && $result->type != 'guestbook' && $result->type != 'jobs' && $result->type != 'webmap' && $result->type != 'user' && $result->type != 'linkers' && $result->type != 'order' && $result->type != 'rss')
	{
		if((($result->type == 'article' || $result->type == 'mapshow') && $requset['n']==0) || ($result->type != 'article' && $request['n']>0))
		return true;
	}else{
		return false;
	}
}