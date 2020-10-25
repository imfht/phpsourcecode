<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
header('Content-Type: text/html; charset=utf-8');
define('THISISADMINI',true);
$dirName=dirname(__FILE__);
$docConfig=$dirName.'/../config/doc-config.php';
require_once($docConfig);
function_exists('date_default_timezone_set') && @date_default_timezone_set('Etc/GMT-'.TIMEZONENAME);
require_once(ABSPATH.'/inc/class.database.php');
require_once(ABSPATH.'/inc/function.php');
$_REQUEST = cleanArrayForMysql($_REQUEST);
$_GET     = cleanArrayForMysql($_GET);
$_POST    = cleanArrayForMysql($_POST);
$request  = $_REQUEST;
$menu_arr=array();
//写路由语句
$pfileName = $request['f'];
if(!empty($pfileName) && $pfileName != 'search')
	$request['p'] = getIdByMenuName($pfileName);
elseif($pfileName == 'search')
	$request['m'] = 'search';

$params['id']		=	$request['p']		=isset($request['p'])?intval($request['p']):0;
$params['cid']		=	$request['c']		=isset($request['c'])?intval($request['c']):0;
$params['args']		=	$request['r']		=isset($request['r'])?intval($request['r']):0;
$params['i']		=	$request['i']		=isset($request['i'])?intval($request['i']):0;
						$request['comment']	=isset($request['comment'])?intval($request['comment']):0;
						$request['mdtp']	=isset($request['mdtp'])?intval($request['mdtp']):0;					
$menu_arr=get_model_type($params['id']);

$request['m']		=	!isset($request['m'])?'':$request['m'];
$params['model']	=	empty($request['m'])?$menu_arr['type']:$request['m'];
$request['a']		=	!isset($request['a'])?'':$request['a'];
$params['action']	=	empty($request['a'])?'index':$request['a'];
$params['related_common'] =	empty($menu_arr['related_common'])?$params['model']:$menu_arr['related_common'];

$request['a']=='phpinfo'?exit:'';

//引入程序主文件
$loadFile=array(
	0=>'/inc/common.php',
	1=>'/inc/class.pager.php',
	2=>'/content/common/common.php',
	3=>'/inc/class.seo.php',
	4=>'/config/doc-global.php',
	5=>'/content/index/__sys.php',
);
foreach($loadFile as $k=>$v){
	require_once(ABSPATH.$v);
}
unset($loadFile);

//权限判断
if($params['model']!='user' || $params['model']!='index')
{
	$model_arr=array( 'article' , 'guestbook' , 'jobs' , 'order' , 'webmap' , 'poll' ,  'mapshow');
	if(intval($_SESSION[TB_PREFIX.'user_roleId'])<intval($menu_arr['level'])&&($params['action']=='view' ||$params['action']=='download' || in_array($menu_arr['type'],$model_arr)))
	{
		redirect(sys_href(0,'user'));exit();
	}
}

//加载 模块主程序
$controller=ABSPATH.'/content/'.$params['model'].'/index.php';
if(is_file($controller))
{
	require_once($controller);	
	require_once(ABSPATH.'/content/comment/index.php');

	//执行 Action	
	empty($params['action'])?index():(function_exists($params['action'])?$params['action']():exit("无此Action #".RemoveXSS($params['action'])." 或栏目类型已被修改"));
}

//加载 模板风格文件
$part_path=ABSPATH.'/skins/'.STYLENAME.'/';
$part_common_path=ABSPATH.'/skins/'.STYLENAME.'/common/';
$loadSkinIndex=$part_path.'index.php';
$loadSkinCommon=$part_path.'common.php';
$loadSkinUserCommon=$part_common_path.'common_user.php';
$loadSkinSearchCommon=$part_common_path.'common_search.php';
$loadSkinOtherCommon=$part_common_path.$params['related_common'];

switch ($params['related_common'])
{
	case 'index':
		if(is_file($loadSkinIndex))$include=$loadSkinIndex;
		break;
	case 'user':
		if(is_file($loadSkinUserCommon))
			$include=$loadSkinUserCommon;
		else
			$include=$loadSkinCommon;
		break;
	case 'search':
		if(is_file($loadSkinSearchCommon))
			$include=$loadSkinSearchCommon;
		else
			$include=$loadSkinCommon;
		break;
		
	default:
		if(is_file($loadSkinOtherCommon)) 
			$include=$loadSkinOtherCommon;
		elseif(is_file($loadSkinCommon))
			$include=$loadSkinCommon;
		else
			exit ('<span style="color:RED"><strong>pager error!</strong></span>');
		break;	
}

require_once($include);
//内页parts 主体函数
function sys_parts($style=0)
{
	global $request,$params,$tag,$path,$data;
	$part_path=ABSPATH.'/skins/'.STYLENAME.'/parts/'.$params['model'].'/'.$params['model'].'_'.$params['action'].'_'.$style.'.php';
	if(is_file($part_path))
		require_once($part_path);
	else 
		echo '<span style="color:RED"><strong>加载 /skins/'.STYLENAME.'/parts/'.$params['model'].'/'.$params['model'].'_'.$params['action'].'_'.$style.'.php 样式资源文件失败，程序意外终止。</strong></span>';
		
    //评论模块开启	
	isComments();
}
function get_model_type($id)
{
	global $db;
	if($id==0)
	return array('type'=>'index','level'=>0);
	else
	return $db->get_row("SELECT * FROM ".TB_PREFIX."menu WHERE id=$id",ARRAY_A);
}
function getIdByMenuName($file)
{
	global $db;
	$sql="SELECT id FROM ".TB_PREFIX."menu WHERE menuName='$file'";
	return $db->get_var($sql);
}
unset($request);
unset($params);
unset($tag);
unset($path);
unset($data);