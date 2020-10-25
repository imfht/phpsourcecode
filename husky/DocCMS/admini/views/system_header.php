<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo SITENAME ?>-后台控制</title>
<link href="../inc/css/data_table.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript" src="../inc/js/jquery-1.6.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../inc/js/data_table.js"></script>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="dhtmltooltip"></div>
<script language="JavaScript" type="text/javascript" src="../inc/js/tips.js"></script> 
<script type="text/javascript">
document.onmousemove=positiontip;
function openTip(){
		showdiv = function(){
			if($(this).parent().next().css("display")=="none"){	
			$(".navin li div").hide(50);
			$(this).parent().next().show(400);
			var spanposition = $(this).position();
			$(".navin li div").css("left",spanposition.left);
			$(".navin li div").focus();
			}
		}
		$(".navin li a").bind('hover',showdiv);
		$(".navin li").mouseleave (
			function() {$(".navin li div").fadeOut(400);}
		);
		$(".navin li div b").click (
		function() {$(".navin li div").hide(40);}
		)
		$(".admin_help").show();
	}
function hideTip(){
		$(".navin li a").unbind("hover");
		$(".admin_help").hide();
	}
var cookie=getCookie('admini_help');
$(document).ready(function(){
	if(cookie==0)
	{
		openTip();
	}
});
</script>
<script type="text/javascript">
function changelanguage(url){
	if(url!=1){
		location.href=window.location.search+"&l="+url;
	}
}
</script>
<div id="wrapper">
<div class="header">
  <div class="hbtn">
  	<h2>
        <select onchange="changelanguage(this.value)" id="language">
        <?php 		
		$langList     = explode('@',QD_lang);
		$langNameList = explode('@',QD_lang_name);
		for($i=0;$i<count($langList)-1;$i++)
		{
			if(!empty($langList[$i])){?>
        <option value="<?=$langList[$i]?>" <?php echo $request['l']==$langList[$i]?'selected="selected"':'';?>><?=$langNameList[$i]?>网站</option>
        <?php }
		}?> 
        </select>
    </h2>
    <h2><a href="./../" class="preview" target="_blank">预览网站</a></h2>
    <?php if($_SESSION[TB_PREFIX.'admin_roleId']>8){ ?>
    <h2><a href="./index.php" class="build2">返回内容管理</a></h2>
    <?php } ?>
    <div> <a href="./index.php?m=system&s=userinfo&a=edit&cid=<?php echo $_SESSION[TB_PREFIX.'admin_userID'] ?>">个人资料</a> <a href="login.php?act=logout">退出</a> </div>
  </div>
  <a href="./"><div class="logo"><h1><?php echo SITENAME; ?> </h1></div></a>	
</div>
<div class="navin">
<?php if($_SESSION[TB_PREFIX.'admin_roleId']>8){ ?>
  <ul>
    <li><h3><a href='./index.php?m=system&s=managechannel' title='添加、修改网站导航栏目菜单和栏目属性' id='m1'>设置导航菜单</a></h3><div class="szdhcd"><h4><b></b></h4></div></li>
    <li><h3><a href='./index.php?m=system&s=options' title='添加、修改网站功能各项参数配制' id='m2'>站点设置</a></h3><div class="zdsz"><h4><b></b></h4></div></li>
    <li><h3><a href='./index.php?m=system&s=changeskin' title='添加、修改、配制网站模板，修管理首页标签' id='m3'>模板管理</a></h3><div class="mbgl"><h4><b></b></h4></div></li>
    <li><h3><a href='./index.php?m=system&s=lang' title='添加、修改、配制网站模板，修管理首页标签' id='m3'>语言管理</a></h3><div class="mbgl"><h4><b></b></h4></div></li>
    <li><h3><a href='./index.php?m=system&s=managemodel' title='查看模块属性，添加、删除模块儿' id='m4'>模块管理</a></h3><div class="mkgl"><h4><b></b></h4></div></li>
    <li><h3><a href='./index.php?m=system&s=userinfo' title='管理网站各类用户以及分配权限' id='m5'>用户管理</a></h3><div class="yhgl"><h4><b></b></h4></div></li>
    <li><h3><a href='./index.php?m=system&s=flashoptions' title='添加、修改、删除网站广告功能' id='m6'>广告管理</a></h3><div class="gggl"><h4><b></b></h4></div></li>
    <li><h3><a href='./index.php?m=system&s=bakup' title='数据库备份、优化、管理' id='m7'>数据库管理</a></h3><div class="sjkgl"><h4><b></b></h4></div></li>
    <li><h3><a href='./index.php?m=system&s=managehtml' title='网站页面生成静态HTML文件缓存配制和管理' id='m8'>静态化管理</a></h3><div class="jthgl"><h4><b></b></h4></div></li>
    <li><h3><a href='./index.php?m=system&s=manageresource' title='用户Upload上传目录资源管理' id='m9'>资源管理</a></h3><div class="zygl"><h4><b></b></h4></div></li>
    <li><h3><a href='./index.php?m=system&s=migration' title='将稻壳Cms所建网站迁移至稻壳网企业信息化全案建设电商云平台，降低企业建站成本，拓宽营销推广空间和渠道，提升网站品牌价值！' id='m10'>DocCms X <<->> DoooC.com</a></h3><div class="sjhq"><h4><b></b></h4></div></li>
  </ul>
<?php }
else if($_SESSION[TB_PREFIX.'admin_roleId']>6 && $_SESSION[TB_PREFIX.'admin_roleId']<9){
	$path=ABSPATH.'/admini/controllers/system/userinfo/config/'; //目录路径
	echo '<ul>';
	echo "<li><h3><a href='./index.php' title='前台内容数据管理' id='m1'><< 返回内容管理</a></h3></li>";
	echo "<li><h3><a href='./index.php?m=system&s=flashoptions' title='添加、修改、删除网站广告功能' id='m2'>系统广告管理</a></h3><div class='gggl'><h4><b></b></h4></div></li>";
	echo '</ul>';
 }?>
  <div id="admini_help"></div>
  <script type="text/javascript">
  var help1 = '<a href="javascript:;" onclick="SetCookie(\'admini_help\',1);cookie=1;hideTip();">【关闭新手提示】</a>';
  var help2 = '<a href="javascript:;" onclick="SetCookie(\'admini_help\',0);cookie=0;openTip();">【开启新手提示】</a>';
  if(cookie==0)
  {
    $('#admini_help').html(help1);
  }
  else
  {
    $('#admini_help').html(help2);
  }
  </script>
</div>
<div id="container">