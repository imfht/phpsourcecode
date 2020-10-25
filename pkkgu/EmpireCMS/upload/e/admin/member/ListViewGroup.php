<?php
define('EmpireCMSAdmin','1');
require("../../class/connect.php");
require("../../class/db_sql.php");
require("../../class/functions.php");
require "../".LoadLang("pub/fun.php");
$link=db_connect();
$empire=new mysqlquery();
$editor=1;
//验证用户
$lur=is_login();
$logininid=$lur['userid'];
$loginin=$lur['username'];
$loginrnd=$lur['rnd'];
$loginlevel=$lur['groupid'];
$loginadminstyleid=$lur['adminstyleid'];
//ehash
$ecms_hashur=hReturnEcmsHashStrAll();
//验证权限
CheckLevel($logininid,$loginin,$classid,"viewgroup");

//返回会员组
function ReturnAddViewMemberGroup($membergroup){
	$count=count($membergroup);
	if($count==0)
	{
		return '';
	}
	$mg='';
	for($i=0;$i<$count;$i++)
	{
		$mg.=intval($membergroup[$i]).',';
	}
	if($mg)
	{
		$mg=','.$mg;
	}
	return $mg;
}

//增加会员访问组
function AddViewGroup($add,$userid,$username){
	global $empire,$dbtbpre;
	if(empty($add['gname']))
	{
		printerror('EmptyViewGroup','history.go(-1)');
	}
	$gname=hRepPostStr($add['gname'],1);
	$gids=ReturnAddViewMemberGroup($add['membergroup']);
	$ingids=ReturnAddViewMemberGroup($add['ingroup']);
	$agids=ReturnAddViewMemberGroup($add['madmingroup']);
	$sql=$empire->query("insert into {$dbtbpre}enewsvg(gname,gids,ingids,agids,mlist) values('$gname','$gids','$ingids','$agids',0);");
	if($sql)
	{
		$vgid=$empire->lastid();
		insert_dolog("vgid=$vgid&gname=$gname");//操作日志
		printerror("AddViewGroupSuccess","AddViewGroup.php?enews=AddViewGroup".hReturnEcmsHashStrHref2(0));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}

//修改会员访问组
function EditViewGroup($add,$userid,$username){
	global $empire,$dbtbpre;
	$vgid=intval($add['vgid']);
	if(empty($add['gname'])||!$vgid)
	{
		printerror('EmptyViewGroup','history.go(-1)');
	}
	$gname=hRepPostStr($add['gname'],1);
	$gids=ReturnAddViewMemberGroup($add['membergroup']);
	$ingids=ReturnAddViewMemberGroup($add['ingroup']);
	$agids=ReturnAddViewMemberGroup($add['madmingroup']);
	//会员白名单
	$membernum=$empire->gettotal("select count(*) as total from {$dbtbpre}enewsvglist where vgid='$vgid'");
	$mlist=$membernum?1:0;
	$sql=$empire->query("update {$dbtbpre}enewsvg set gname='$gname',gids='$gids',ingids='$ingids',agids='$agids',mlist='$mlist' where vgid='$vgid'");
	if($sql)
	{
		insert_dolog("vgid=$vgid&gname=$gname");//操作日志
		printerror("EditViewGroupSuccess","ListViewGroup.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}

//删除会员访问组
function DelViewGroup($add,$userid,$username){
	global $empire,$dbtbpre;
	$vgid=intval($add['vgid']);
	if(!$vgid)
	{
		printerror('EmptyViewGroupid','history.go(-1)');
	}
	$r=$empire->fetch1("select vgid,gname from {$dbtbpre}enewsvg where vgid='$vgid'");
	if(!$r['vgid'])
	{
		printerror('EmptyViewGroupid','history.go(-1)');
	}
	$sql=$empire->query("delete from {$dbtbpre}enewsvg where vgid='$vgid'");
	$msql=$empire->query("delete from {$dbtbpre}enewsvglist where vgid='$vgid'");
	if($sql)
	{
		insert_dolog("vgid=$vgid&gname=$r[gname]");//操作日志
		printerror("DelViewGroupSuccess","ListViewGroup.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}

$enews=$_POST['enews'];
if(empty($enews))
{$enews=$_GET['enews'];}
if($enews)
{
	hCheckEcmsRHash();
}
if($enews=="AddViewGroup")
{
	AddViewGroup($_POST,$logininid,$loginin);
}
elseif($enews=="EditViewGroup")
{
	EditViewGroup($_POST,$logininid,$loginin);
}
elseif($enews=="DelViewGroup")
{
	DelViewGroup($_GET,$logininid,$loginin);
}


$search=$ecms_hashur['ehref'];
$page=(int)$_GET['page'];
$page=RepPIntvar($page);
$start=0;
$line=16;//每页显示条数
$page_line=25;//每页显示链接数
$offset=$page*$line;//总偏移量
$query="select * from {$dbtbpre}enewsvg";
$totalquery="select count(*) as total from {$dbtbpre}enewsvg";
$num=$empire->gettotal($totalquery);//取得总条数
$query=$query." order by vgid desc limit $offset,$line";
$sql=$empire->query($query);
$returnpage=page2($num,$line,$page_line,$start,$page,$search);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../adminstyle/<?=$loginadminstyleid?>/adminstyle.css" rel="stylesheet" type="text/css">
<title>会员访问组</title>
</head>

<body>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
  <tr> 
    <td width="50%" height="25">位置：<a href="ListViewGroup.php<?=$ecms_hashur['whehref']?>">管理会员访问组</a></td>
    <td><div align="right" class="emenubutton">
        <input type="button" name="Submit5" value="增加会员访问组" onclick="self.location.href='AddViewGroup.php?enews=AddViewGroup<?=$ecms_hashur['ehref']?>';">
      </div></td>
  </tr>
</table>

<br>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <tr class="header"> 
    <td width="6%" height="25"> <div align="center">ID</div></td>
    <td width="39%" height="25"> <div align="center">组名称</div></td>
    <td width="38%"><div align="center">允许会员白名单</div></td>
    <td width="17%" height="25"> <div align="center">操作</div></td>
  </tr>
  <?php
  while($r=$empire->fetch($sql))
  {
  	$color="#ffffff";
	$movejs=' onmouseout="this.style.backgroundColor=\'#ffffff\'" onmouseover="this.style.backgroundColor=\'#C3EFFF\'"';
	$membernum=$empire->gettotal("select count(*) as total from {$dbtbpre}enewsvglist where vgid='$r[vgid]'");
  ?>
  <tr bgcolor="<?=$color?>"<?=$movejs?>> 
    <td height="25"> <div align="center"> 
        <?=$r['vgid']?>
      </div></td>
    <td height="25"> <div align="center"> 
        <?=$r['gname']?>
      </div></td>
    <td><div align="center"><a href="ListVgMember.php?vgid=<?=$r['vgid']?><?=$ecms_hashur['ehref']?>" target="_blank">管理会员白名单(数量：<strong><?=$membernum?></strong>)</a></div></td>
    <td height="25"> <div align="center"> [<a href="AddViewGroup.php?enews=EditViewGroup&vgid=<?=$r['vgid']?><?=$ecms_hashur['ehref']?>">修改</a>]&nbsp;[<a href="ListViewGroup.php?enews=DelViewGroup&vgid=<?=$r['vgid']?><?=$ecms_hashur['href']?>" onclick="return confirm('确认要删除？');">删除</a>]</div></td>
  </tr>
  <?
  }
  ?>
  <tr bgcolor="#FFFFFF"> 
    <td height="25" colspan="4">&nbsp;&nbsp;&nbsp; 
      <?=$returnpage?>    </td>
  </tr>
</table>
</body>
</html>
<?
db_close();
$empire=null;
?>
