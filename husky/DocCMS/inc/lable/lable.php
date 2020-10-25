<?php 
/*!
 * Frame PHP 
 * http://www.doccms.com/
 * Date: 狗头巫师(grysoft) 2012/11/11
 * QQ:767912290
 */
session_start();
error_reporting(E_ALL ^ E_NOTICE);
header('Content-Type: text/html; charset=utf-8');
$dirName=dirname(__FILE__);
$shlConfig=$dirName.'/../../config/doc-config-'.$_SESSION[TB_PREFIX.'doclang'].'.php';
require($shlConfig);
$loadFile=array(
	0=>'/inc/function.php',
	1=>'/inc/class.seo.php',
	2=>'/config/doc-global.php',
	3=>'/inc/class.database.php',
	4=>'/inc/class.json.php'
);
foreach($loadFile as $k=>$v){
	require_once(ABSPATH.$v);
}
checkme(10,true);
$request  = $_REQUEST;
$action = $request['a'];
empty($action)?index():(function_exists($action)?$action():exit("非法Action #".$action." "));
function index(){
	global $tag;
	is_file(ABSPATH.'skins/'.STYLENAME.'/index.php')?'':exit('模板不存在！');
	
	$filename = ABSPATH.'skins/'.STYLENAME.'/index.php';	
	$content = file_get_contents($filename);
	
	$style = '<link href="doc_sendframe.css" rel="stylesheet" type="text/css" />
			  <script language="javascript" type="text/javascript" src="../js/jquery-1.6.2.min.js"></script>
			  <script language="javascript" type="text/javascript" src="doc_sendframe.js"></script>
			  <script language="javascript" type="text/javascript" src="../js/window_custom.js"></script>';
	
	$ary = array(  '/\<\?php([^\<\?]*?)echo(\s*)\$tag\[[\'|\"]path.root[\'|\"]\]+(.*?)\?\>/',
				   '/\<\?php([^\<\?]*?)echo(\s*)\$tag\[[\'|\"]path.skin[\'|\"]\]+(.*?)\?\>/',
				   '/\<\?php([^\<\?]*?)doc_article\((.*?)\)+(.*?)\?\>/e',
				   '/\<\?php([^\<\?]*?)doc_download\((.*?)\)+(.*?)\?\>/e',
				   '/\<\?php([^\<\?]*?)doc_focus\((.*?)\)+(.*?)\?\>/e',
				   '/\<\?php([^\<\?]*?)doc_guestbook\((.*?)\)+(.*?)\?\>/e',
				   '/\<\?php([^\<\?]*?)doc_jobs\((.*?)\)+(.*?)\?\>/e',
				   '/\<\?php([^\<\?]*?)doc_linkers\((.*?)\)+(.*?)\?\>/e',
				   '/\<\?php([^\<\?]*?)doc_list\((.*?)\)+(.*?)\?\>/e',
				   '/\<\?php([^\<\?]*?)doc_mapshow\((.*?)\)+(.*?)\?\>/e',
				   '/\<\?php([^\<\?]*?)doc_picture\((.*?)\)+(.*?)\?\>/e',
				   '/\<\?php([^\<\?]*?)doc_poll\((.*?)\)+(.*?)\?\>/e',
				   '/\<\?php([^\<\?]*?)doc_product\((.*?)\)+(.*?)\?\>/e',
				   '/\<\?php([^\<\?]*?)doc_video\((.*?)\)+(.*?)\?\>/e',
				   '/\<\?php([^\<\?]*?)nav_sub\((.*?)\)+(.*?)\?\>/e',
				   '/\<\/head\>/',		
				);
	$ary2 = array( $tag['path.root'],
				   $tag['path.skin'],
				   'func("article","$2")',
				   'func("download","$2")',
				   'func("focus","$2")',
				   'func("guestbook","$2")',
				   'func("jobs","$2")',
				   'func("linkers","$2")',
				   'func("list","$2")',
				   'func("mapshow","$2")',
				   'func("picture","$2")',
				   'func("poll","$2")',
				   'func("product","$2")',
				   'func("video","$2")',
				   'func("nav_sub","$2")',
				   $style.'</head>',	   
				);
	
	$content = preg_replace($ary, $ary2, $content);
	
	echo $content;
}
function showId()
{
	global $db,$request;
	
	is_alow($request['type']);
	if($request['type'] == 'nav_sub')
	$request['type'] = '0" or 1= "1';
	
	$tables = $request['type']=='focus'?'flash_group':'menu';
	if($request['value'])
	{
		$value = explode(',',stripslashes($request['value']));
		if($value[0])
		{
			$value[0]=explode('|',$value[0]);
			$value[0]=implode(',',$value[0]);
			$menu = $db->get_results('SELECT * FROM '.TB_PREFIX.$tables.' WHERE `type` = "'.$request['type'].'" AND id NOT IN('.$value[0].')',ARRAY_A);
			$menu2 = $db->get_results('SELECT * FROM '.TB_PREFIX.$tables.' WHERE `type` = "'.$request['type'].'" AND id IN('.$value[0].')',ARRAY_A);
		}
		else
		{
			$menu = $db->get_results('SELECT * FROM '.TB_PREFIX.$tables.' WHERE `type` = "'.$request['type'].'"',ARRAY_A);
		}
	}
	else
	{
		$menu = $db->get_results('SELECT * FROM '.TB_PREFIX.$tables.' WHERE `type` = "'.$request['type'].'"',ARRAY_A);
	}	
	
	$s = 0;
	for($i=0;$i<count($menu);$i++)
	{
		$s++;
		$tempId['n'][$s]['id']   = $menu[$i]['id'];
		$tempId['n'][$s]['title']= $menu[$i]['title'];
	}
	
	$s = 0;
	for($i=0;$i<count($menu2);$i++)
	{	
		$s++;
		$tempId['y'][$s]['id']   = $menu2[$i]['id'];
		$tempId['y'][$s]['title']= $menu2[$i]['title'];
	}
	$json = new Services_JSON();
	echo $json->encode($tempId);
	exit;
}
function showPollId()
{
	global $db,$request;
	
	is_alow($request['type']);
	
	if($request['value'])
	{
		$value = explode(',',stripslashes($request['value']));
		if($value[0])
		{
			$value[0]=explode('|',$value[0]);
			$value[0]=implode(',',$value[0]);
			$menu = $db->get_results('SELECT * FROM '.TB_PREFIX.'poll_category WHERE id NOT IN('.$value[0].')',ARRAY_A);
			$menu2 = $db->get_results('SELECT * FROM '.TB_PREFIX.'poll_category WHERE id IN('.$value[0].')',ARRAY_A);
		}
		else
		{
			$menu = $db->get_results('SELECT * FROM '.TB_PREFIX.'poll_category',ARRAY_A);
		}
	}
	else
	{
		$menu = $db->get_results('SELECT * FROM '.TB_PREFIX.'poll_category',ARRAY_A);
	}	
	$s = 0;
	for($i=0;$i<count($menu);$i++)
	{
		$s++;
		$tempId['n'][$s]['id']   = $menu[$i]['id'];
		$tempId['n'][$s]['title']= $menu[$i]['title'];
	}
	
	$s = 0;
	for($i=0;$i<count($menu2);$i++)
	{	
		$s++;
		$tempId['y'][$s]['id']   = $menu2[$i]['id'];
		$tempId['y'][$s]['title']= $menu2[$i]['title'];
	}
	$json = new Services_JSON();
	echo $json->encode($tempId);
	exit;
}
function showStyle()
{
	global $request;
	
	is_alow($request['type']);
	
	if($request['type'] == 'nav_sub')
	$part_common_path=ABSPATH.'/skins/'.STYLENAME.'/index/__nav/';
	else
	$part_common_path=ABSPATH.'/skins/'.STYLENAME.'/index/'.$request['type'].'/';
	
	$temp_arr=rec_listFiles($part_common_path);
	if($temp_arr)
	{
		foreach ($temp_arr as $v)
		{
			preg_match('/'.$request['type'].'_(.*?).php/',$v,$math);
			$tempStyle[] = $math[1].':'.$v;	
		}
	}
    $json = new Services_JSON();
	echo $json->encode($tempStyle);
	exit;
}
function showStyleTxt()
{
	global $request;
	
	is_alow($request['type']);
	$request['style']=$request['style']=='undefined'||$request['style']==''?0:$request['style'];
	
	if($request['type'] == 'nav_sub')
	$filename = ABSPATH.'skins/'.STYLENAME.'/index/__nav/'.$request['type'].'_'.$request['style'].'.php';
	else
	$filename = ABSPATH.'skins/'.STYLENAME.'/index/'.$request['type'].'/'.$request['type'].'_'.$request['style'].'.php';
	
	if(is_file($filename))
	{
		$content = file_get_contents($filename);
	}
	
	echo $content;
	exit;
}
function func($type,$value){
	static $n;
	static $c;
	if($c!=$type)
	{
		$n=0;
	}
	$c=$type;
	return '<input class="creatbt" type="button" onclick="doc_send_window(0,\''.$type.'\',\''.$value.'\',\''.++$n.'\')" value="'.$type.'">';
}
function is_alow($type)
{
	$noModule = array('article', 'download', 'focus', 'guestbook', 'jobs', 'linkers', 'list', 'mapshow', 'picture', 'poll', 'product', 'video', 'nav_sub');
	if(!in_array($type,$noModule))exit;
}
?>