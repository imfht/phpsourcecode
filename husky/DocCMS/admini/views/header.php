<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo SITENAME ?>-后台控制</title>
<link href="../inc/css/data_table.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript" src="../inc/js/data_table.js"></script>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="dhtmltooltip"></div>
<?php if(EDITORSTYLE=='kindeditor'){?>
<script charset="utf-8" src="../editor/keditor/kindeditor-min.js"></script>
<script charset="utf-8" src="../editor/keditor/lang/zh_CN.js"></script>
<?php }else{?>
<script type="text/javascript" charset="utf-8" src="../editor/ueditor/editor_config.js"></script>
<script type="text/javascript" charset="utf-8" src="../editor/ueditor/editor_api.js"></script>
<link rel="stylesheet" type="text/css" href="../editor/ueditor/themes/default/ueditor.css"/>
<?php }?>
<script language="JavaScript" type="text/javascript" src="../inc/js/jquery-1.6.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../inc/js/tips.js"></script>
<script type="text/javascript">

document.onmousemove=positiontip;

function dex(n){
var tli= $(".amli");
var mli= $(".amcon");
for(i=0;i<tli.length;i++){
   tli[i].className=i==n?"amli hover":"amli";
   mli[i].style.display=i==n?"block":"none";
}
}
$(document).ready(function(){
	$(".nav li").hover(
    function() { $(this).addClass("iehover"); },
    function() { $(this).removeClass("iehover");}
    );
	var funli = $(".func li");
	for(m=0;m<funli.length;m++){
		$(funli[m]).css('background-position','7px -'+(66*m)+'px');
	}
	$(".nav li li").hover(
		function(){
			var liwidth = $(this).parent().width();
			$(this).width(liwidth);
			var ulleft = $(this).position().left;
			$(this).children("ul").css("left",ulleft+liwidth);
		},
		function(){
			//$(this).children("ul").css("display","none");
		}
	);
});
</script>
<script type="text/javascript">
function changelanguage(url){
	if(url!=1){
	location.href="./index.php?l="+url;
	}
}
</script>
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
        <h2><a href="./index.php?m=system&s=managechannel" class="build">开始构建网站</a></h2>
        <?php } ?>
        <div>
            <a href="./index.php?m=system&s=userinfo&a=edit&cid=<?php echo $_SESSION[TB_PREFIX.'admin_userID'] ?>">个人资料</a>
            <a href="login.php?act=logout">退出</a>
        </div>
    </div>
	<div class="logo"><h1><?php echo SITENAME; ?> </h1></div>
</div>
	<div class="nav">
		<?php
			 if($_SESSION[TB_PREFIX.'admin_roleId']>8){
			 	echo '<ul>';
			 	require_once('./'.$request['l'].'_nav.php');
			 	echo '</ul>';
			 }else{
			 	$path=ABSPATH.'/admini/controllers/system/userinfo/config/'; //目录路径
			 	$filename=$path.'dt-RightsManagement-config-'.$_SESSION[TB_PREFIX.'admin_userID'].'.php';//配置文件全路径
				if(is_file($filename)){
					if(!$request['p']){
							echo '<ul>';
							require_once($path.'nav_'.$_SESSION[TB_PREFIX.'admin_userID'].'.php');// 主菜单文件全路径
							echo '</ul>';
					}else{
						include($filename);
						if(in_array($request['p'],unserialize(MUNE_ID_ARRAY))){
							echo '<ul>';
							require_once($path.'nav_'.$_SESSION[TB_PREFIX.'admin_userID'].'.php');
							echo '</ul>';
						}else{
							echo '<ul><li><a href="'.$_SERVER['PHP_SELF'].'">Access Forbidden!返回</a></li></ul>';
							exit();
						}
					}
				}else{
					exit('此用户还没有分配管理栏目，请先分配管理权限');
				}
			}
		 ?>
	</div>
<div class="location">当前位置:<?php echo getLocation() ?></div>
<div class="doccmsmain">