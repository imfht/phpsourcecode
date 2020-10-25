<?php
/*!
 * lable_edit PHP 
 * http://www.doccms.com/
 * Date: 狗头巫师(grysoft) 2012/11/11
 * QQ:767912290
 */
session_start();
error_reporting(E_ALL ^ E_NOTICE);
header('Content-Type: text/html; charset=utf-8');
$dirName=dirname(__FILE__);
$shlConfig=$dirName.'/../../config/doc-config-'.$_SESSION[TB_PREFIX.'doclang'].'.php';
require_once($shlConfig);
if(empty($_SESSION[TB_PREFIX.'admin_name']) or $_SESSION[TB_PREFIX.'admin_roleId']<10)
{
	redirect($tag['path.root'] .'/404.html');exit;
}
$type		     = $_GET['m'];
$num 		     = intval($_GET['num']);

$labelId   		 = $_POST['labelId'];             //标签调用的栏目 Id
$labelNum        = intval($_POST['labelNum']);    //调用数量
$labelCountT     = intval($_POST['labelCountT']); //标题截取字数
$labelCountD	 = intval($_POST['labelCountD']); //描述截取字数
$labelCountC     = intval($_POST['labelCountC']); //内容截取字数
$labelStyle      = $_POST['labelStyle'];          //标签调用的样式文件
$labelIsellipsis = $_POST['labelIsellipsis']=='true'?'true':'false';     //截取字串是否加上省略号
$labelHastag     = $_POST['labelHastag']=='true'?'true':'false';         //截取内容是否去除html代码
$labelOrder      = in_array($_POST['labelOrder'],array('ordering','id','dtTime','counts'))?$_POST['labelOrder']:'';          //排序方式
$labelFrom       = intval($_POST['labelFrom']);   //从第几条数据开始调用
$labelType       = intval($_POST['labelType']);           //标签 调用的方式
switch($type)
{
	case 'article':$param="'$labelId',$labelNum,$labelStyle,$labelCountT,$labelCountD,$labelCountC,$labelIsellipsis,$labelHastag,'$labelOrder',$labelFrom";break;
	
	case 'focus':$param="'$labelId',$labelNum,$labelStyle,$labelCountT,$labelCountD,$labelIsellipsis,'$labelOrder',$labelFrom";break;
	case 'guestbook':$param="'$labelId',$labelNum,$labelStyle,$labelCountT,$labelCountD,$labelIsellipsis,'$labelOrder',$labelFrom";break;
	
	case 'linkers':$param="'$labelId',$labelNum,$labelStyle,$labelCountT,$labelCountD,$labelType,$labelIsellipsis,'$labelOrder',$labelFrom";break;
	
	case 'mapshow':$param="'$labelId',$labelStyle,$labelCountT,$labelCountC,$labelIsellipsis,$labelHastag";break;
	
	case 'nav_sub':$param="'$labelId',$labelStyle,$labelType,$labelNum";break;
	
	case 'poll':$param="'$labelId',$labelStyle";break;
	
	default:$param="'$labelId',$labelNum,$labelStyle,$labelCountT,$labelCountD,$labelCountC,$labelIsellipsis,$labelHastag,'$labelOrder',$labelFrom";break;
}

$styleContent = stripslashes($_POST['styleContent']);

if($type == 'nav_sub')
{
	$url= 'skins/'.STYLENAME.'/index/__nav/'.$type.'_'.$labelStyle .'.php';
	$type=$type;
}
else
{
	$url= 'skins/'.STYLENAME.'/index/'.$type.'/'.$type.'_'.$labelStyle .'.php';
	$type='doc_'.$type;
}

if($fp=fopen(ABSPATH.$url,'w'))
{	
	fputs($fp,$styleContent);
	fclose($fp);
}

$filename = ABSPATH.'skins/'.STYLENAME.'/index.php';
$contents = file_get_contents($filename);

$ary = '/\<\?php([^\<\?]*?)'.$type.'\((.*?)\)+(.*?)\?\>/';

$ary2 = '<?php '.$type.'('.$param.')?>';

preg_match_all($ary,$contents,$link_content);

$newAry = $link_content[0];
for($i=0;$i<count($link_content[0]);$i++)
{
	if($i==($num-1))
	{
		$newAry[$i] =$ary2 ;
	}
}

$content = preg_replace('/\<\?php([^\<\?]*?)'.$type.'\((.*?)\)+(.*?)\?\>/e', 'stripslashes(func("$0"))', $contents);

$content = preg_replace('/'.$num.'\<\?php([^\<\?]*?)'.$type.'\((.*?)\)+(.*?)\?\>'.$num.'/',$newAry[$num-1],$content);

$content = preg_replace('/\d{1,2}(\<\?php([^\<\?]*?)'.$type.'\((.*?)\)+(.*?)\?\>)\d{1,2}/','$1',$content);

if($fp=fopen(ABSPATH.'skins/'.STYLENAME.'/index.php','w'))
{	
	fputs($fp,$content);
	fclose($fp);
}

function func($value){
	static $n;
	$n = ++$n;
	return $n.$value.$n;
}
echo "<script>window.location.href='".ROOTPATH."/inc/lable/lable.php'</script>";
exit;
?>