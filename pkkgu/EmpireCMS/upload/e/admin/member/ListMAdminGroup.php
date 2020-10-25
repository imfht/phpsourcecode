<?php
define('EmpireCMSAdmin','1');
require("../../class/connect.php");
require("../../class/db_sql.php");
require("../../class/functions.php");
require("../../member/class/user.php");
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
CheckLevel($logininid,$loginin,$classid,"madmingroup");

//增加会员管理组
function AddMAdminGroup($add,$userid,$username){
	global $empire,$dbtbpre;
	if(empty($add['agname']))
	{
		printerror('EmptyMAdminGroup','history.go(-1)');
	}
	$add['agname']=hRepPostStr($add['agname'],1);
	$add['isadmin']=(int)$add['isadmin'];
	$sql=$empire->query("insert into {$dbtbpre}enewsag(agname,isadmin,auids) values('$add[agname]','$add[isadmin]','');");
	if($sql)
	{
		$agid=$empire->lastid();
		//更新缓存
		GetConfig();
		GetMemberLevel();
		insert_dolog("agid=$agid&agname=$add[agname]");//操作日志
		printerror("AddMAdminGroupSuccess","AddMAdminGroup.php?enews=AddMAdminGroup".hReturnEcmsHashStrHref2(0));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}

//修改会员管理组
function EditMAdminGroup($add,$userid,$username){
	global $empire,$dbtbpre;
	$agid=intval($add['agid']);
	if(empty($add['agname'])||!$agid)
	{
		printerror('EmptyMAdminGroup','history.go(-1)');
	}
	$add['agname']=hRepPostStr($add['agname'],1);
	$add['isadmin']=(int)$add['isadmin'];
	$addupdate='';
	if($agid==1||$agid==2)
	{
		$addupdate='';
	}
	else
	{
		$addupdate=",isadmin='$add[isadmin]'";
	}
	$sql=$empire->query("update {$dbtbpre}enewsag set agname='$add[agname]'".$addupdate." where agid='$agid'");
	//更新缓存
	GetConfig();
	GetMemberLevel();
	if($sql)
	{
		insert_dolog("agid=$agid&agname=$add[agname]");//操作日志
		printerror("EditMAdminGroupSuccess","ListMAdminGroup.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}

//删除会员管理组
function DelMAdminGroup($add,$userid,$username){
	global $empire,$dbtbpre;
	$agid=intval($add['agid']);
	if(!$agid)
	{
		printerror('EmptyMAdminGroupid','history.go(-1)');
	}
	$r=$empire->fetch1("select agid,agname from {$dbtbpre}enewsag where agid='$agid'");
	if(!$r['agid'])
	{
		printerror('EmptyMAdminGroupid','history.go(-1)');
	}
	if($agid==1||$agid==2)
	{
		printerror('NotDelSysMAdminGroupid','history.go(-1)');
	}
	$sql=$empire->query("delete from {$dbtbpre}enewsag where agid='$agid'");
	$empire->query("update ".eReturnMemberTable()." set ".egetmf('agid')."=0 where ".egetmf('agid')."='$agid'");
	//更新缓存
	GetConfig();
	GetMemberLevel();
	if($sql)
	{
		insert_dolog("agid=$gid&agname=$r[agname]");//操作日志
		printerror("DelMAdminGroupSuccess","ListMAdminGroup.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}

//增加会员管理员
function AddMAgUser($add,$userid,$username){
	global $empire,$dbtbpre;
	$agid=(int)$add['agid'];
	$adduserid=(int)$add['adduserid'];
	$addusername=RepPostVar($add['addusername']);
	if(!$agid||!$adduserid||!$addusername)
	{
		printerror("EmptyMAgUser","history.go(-1)");
	}
	$magr=$empire->fetch1("select * from {$dbtbpre}enewsag where agid='$agid'");
	if(!$magr['agid'])
	{
		printerror("EmptyMAgUser","history.go(-1)");
	}
	$mr=$empire->fetch1("select ".eReturnSelectMemberF('userid')." from ".eReturnMemberTable()." where ".egetmf('userid')."='$adduserid' and ".egetmf('username')."='$addusername' limit 1");
	if(!$mr['userid'])
	{
		printerror("ErrorMAgUser","history.go(-1)");
	}
	//是否存在
	if(strstr($magr['auids'],','.$adduserid.','))
	{
		printerror("HaveMAgUser","history.go(-1)");
	}
	$num=$empire->gettotal("select count(*) as total from {$dbtbpre}enewsag where auids like '%,".$adduserid.",%' limit 1");
	if($num)
	{
		printerror("HaveMAgUser","history.go(-1)");
	}
	if($magr['auids'])
	{
		$new_auids=$magr['auids'].$adduserid.',';
	}
	else
	{
		$new_auids=','.$adduserid.',';
	}
	$sql=$empire->query("update {$dbtbpre}enewsag set auids='$new_auids' where agid='$agid' limit 1");
	$empire->query("update ".eReturnMemberTable()." set ".egetmf('agid')."='$agid' where ".egetmf('userid')."='$adduserid'");
	//更新缓存
	GetConfig();
	GetMemberLevel();
	if($sql)
	{
		//操作日志
		insert_dolog("agid=".$agid."<br>userid=".$adduserid."&username=".$addusername);
		printerror("AddMAgUserSuccess","ListMAgUser.php?agid=$agid".hReturnEcmsHashStrHref2(0));
	}
	else
	{printerror("DbError","history.go(-1)");}
}

//删除会员管理员
function DelMAgUser($add,$userid,$username){
	global $empire,$dbtbpre;
	$agid=(int)$add['agid'];
	$adduserid=(int)$add['adduserid'];
	if(!$agid||!$adduserid)
	{
		printerror("EmptyMAgUser","history.go(-1)");
	}
	$magr=$empire->fetch1("select * from {$dbtbpre}enewsag where agid='$agid'");
	if(!$magr['agid'])
	{
		printerror("EmptyMAgUser","history.go(-1)");
	}
	$new_auids=str_replace(",".$adduserid.",",",",$magr['auids']);
	if($new_auids==',')
	{
		$new_auids='';
	}
	$sql=$empire->query("update {$dbtbpre}enewsag set auids='$new_auids' where agid='$agid' limit 1");
	$empire->query("update ".eReturnMemberTable()." set ".egetmf('agid')."=0 where ".egetmf('userid')."='$adduserid'");
	//更新缓存
	GetConfig();
	GetMemberLevel();
	if($sql)
	{
		//操作日志
		insert_dolog("agid=".$agid."<br>userid=".$adduserid);
		printerror("DelMAgUserSuccess","ListMAgUser.php?agid=$agid".hReturnEcmsHashStrHref2(0));
	}
	else
	{printerror("DbError","history.go(-1)");}
}


$enews=$_POST['enews'];
if(empty($enews))
{$enews=$_GET['enews'];}
if($enews)
{
	hCheckEcmsRHash();
}
if($enews=="AddMAdminGroup")
{
	AddMAdminGroup($_POST,$logininid,$loginin);
}
elseif($enews=="EditMAdminGroup")
{
	EditMAdminGroup($_POST,$logininid,$loginin);
}
elseif($enews=="DelMAdminGroup")
{
	DelMAdminGroup($_GET,$logininid,$loginin);
}
elseif($enews=="AddMAgUser")
{
	AddMAgUser($_POST,$logininid,$loginin);
}
elseif($enews=="DelMAgUser")
{
	DelMAgUser($_GET,$logininid,$loginin);
}


$search=$ecms_hashur['ehref'];
$page=(int)$_GET['page'];
$page=RepPIntvar($page);
$start=0;
$line=50;//每页显示条数
$page_line=25;//每页显示链接数
$offset=$page*$line;//总偏移量
$query="select * from {$dbtbpre}enewsag";
$totalquery="select count(*) as total from {$dbtbpre}enewsag";
$num=$empire->gettotal($totalquery);//取得总条数
$query=$query." order by agid desc limit $offset,$line";
$sql=$empire->query($query);
$returnpage=page2($num,$line,$page_line,$start,$page,$search);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../adminstyle/<?=$loginadminstyleid?>/adminstyle.css" rel="stylesheet" type="text/css">
<title>会员管理组</title>
</head>

<body>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
  <tr> 
    <td width="50%" height="25">位置：<a href="ListMAdminGroup.php<?=$ecms_hashur['whehref']?>">管理会员管理组</a></td>
    <td><div align="right" class="emenubutton">
        <input type="button" name="Submit5" value="增加会员管理组" onclick="self.location.href='AddMAdminGroup.php?enews=AddMAdminGroup<?=$ecms_hashur['ehref']?>';">
      </div></td>
  </tr>
</table>

<br>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <tr class="header"> 
    <td width="5%" height="25"> <div align="center">ID</div></td>
    <td width="32%" height="25"> <div align="center">组名称</div></td>
    <td width="26%"><div align="center">管理级别</div></td>
    <td width="21%"><div align="center">成员列表</div></td>
    <td width="16%" height="25"> <div align="center">操作</div></td>
  </tr>
  <?php
  while($r=$empire->fetch($sql))
  {
  	$color="#ffffff";
	$movejs=' onmouseout="this.style.backgroundColor=\'#ffffff\'" onmouseover="this.style.backgroundColor=\'#C3EFFF\'"';
	$membernum=0;
	if($r['auids'])
	{
		$mr=explode(",",$r['auids']);
		$membernum=count($mr)-2;
	}
	if($r['isadmin']==9)
	{
		$isadminname='管理员';
	}
	elseif($r['isadmin']==5)
	{
		$isadminname='版主';
	}
	elseif($r['isadmin']==1)
	{
		$isadminname='实习版主';
	}
	else
	{
		$isadminname='';
	}
  ?>
  <tr bgcolor="<?=$color?>"<?=$movejs?>> 
    <td height="25"> <div align="center"> 
        <?=$r[agid]?>
      </div></td>
    <td height="25"> <div align="center"> 
        <?=$r[agname]?>
      </div></td>
    <td><div align="center"><?=$isadminname?></div></td>
    <td><div align="center"><a href="ListMAgUser.php?agid=<?=$r['agid']?><?=$ecms_hashur['ehref']?>" target="_blank">管理成员列表 (<strong><?=$membernum?></strong>)</a></div></td>
    <td height="25"> <div align="center"> [<a href="AddMAdminGroup.php?enews=EditMAdminGroup&agid=<?=$r[agid]?><?=$ecms_hashur['ehref']?>">修改</a>]&nbsp;[<a href="ListMAdminGroup.php?enews=DelMAdminGroup&agid=<?=$r[agid]?><?=$ecms_hashur['href']?>" onclick="return confirm('确认要删除？');">删除</a>]</div></td>
  </tr>
  <?
  }
  ?>
  <tr bgcolor="#FFFFFF"> 
    <td height="25" colspan="5">&nbsp;&nbsp;&nbsp; 
      <?=$returnpage?>    </td>
  </tr>
</table>
</body>
</html>
<?
db_close();
$empire=null;
?>
