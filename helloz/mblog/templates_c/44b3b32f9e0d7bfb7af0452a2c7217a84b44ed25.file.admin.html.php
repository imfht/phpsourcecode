<?php /* Smarty version Smarty-3.1.21-dev, created on 2015-01-02 10:27:55
         compiled from "D:\wwwroot\blog\templates\admin.html" */ ?>
<?php /*%%SmartyHeaderCode:1541554a13d25ad5181-19722730%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '44b3b32f9e0d7bfb7af0452a2c7217a84b44ed25' => 
    array (
      0 => 'D:\\wwwroot\\blog\\templates\\admin.html',
      1 => 1420190868,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1541554a13d25ad5181-19722730',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_54a13d25b00111_92454736',
  'variables' => 
  array (
    'nickname' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54a13d25b00111_92454736')) {function content_54a13d25b00111_92454736($_smarty_tpl) {?><!DOCTYPE html>
<html>
<head>
<meta charset = "UTF-8" />
<title>M-Blog后台管理</title>
<link href="../templates/css/style.css" rel="stylesheet" type="text/css" >

<?php echo '<script'; ?>
 type="text/JavaScript"> 
var $=function(id) {
   return document.getElementById(id);
}

function show_menu(num){
for(i=0;i<100;i++){
	if($('li0'+i)){
	$('li0'+i).style.display='none';
	$('f0'+i).className='';
	 }
}
	  $('li0'+num).style.display='block';//触发以后信息块
	  $('f0'+num).className='left02down01_xia_li';//触发以后TAG样式
}

function show_menuB(numB){
	for(j=0;j<100;j++){
		 if(j!=numB){
			if($('Bli0'+j)){
		  $('Bli0'+j).style.display='none';
		  $('Bf0'+j).style.background='url(images/01.gif)';
		}
		 }
	}
	if($('Bli0'+numB)){   
		if($('Bli0'+numB).style.display=='block'){
		  $('Bli0'+numB).style.display='none';
		 $('Bf0'+numB).style.background='url(images/01.gif)';
		}else {
		  $('Bli0'+numB).style.display='block';
		  $('Bf0'+numB).style.background='url(images/02.gif)';
		}
	}
}


var temp=0;
function show_menuC(){
		if (temp==0){
		 document.getElementById('LeftBox').style.display='none';
	  	 document.getElementById('RightBox').style.marginLeft='0';
		 document.getElementById('Mobile').style.background='url(images/center.gif)';

		 temp=1;
		}else{
		document.getElementById('RightBox').style.marginLeft='222px';
	   	document.getElementById('LeftBox').style.display='block';
		document.getElementById('Mobile').style.background='url(images/center0.gif)';

	   temp=0;
			}
	 }
<?php echo '</script'; ?>
>

</head>

<body>
<div class="header">
	<div class="header03"></div>
	<a href = "../index.php" rel = "nofollow"><div class="header01"></div></a>
	<div class="header02">M-Blog后台管理系统</div>
</div>
<div class="left" id="LeftBox">
	<div class="left01">
		<div class="left01_right"></div>
		<div class="left01_left"></div>
		<div class="left01_c">欢迎回来：<?php echo $_smarty_tpl->tpl_vars['nickname']->value;?>
</div>
	</div>
	<div class="left02">
		<div class="left02top">
			<div class="left02top_right"></div>
			<div class="left02top_left"></div>
			<div class="left02top_c">信息管理</div>
		</div>
	  <div class="left02down">
			<div class="left02down01"><a  onclick="show_menuB(80)" href="javascript:;"><div id="Bf080" class="left02down01_img"></div>用户信息查询</a></div>
			<div class="left02down01_xia noneBox" id="Bli080">
				<ul>
					<li onmousemove="show_menu(10)" id="f010"><a href="http://www.xiaoz.me" target = "show">&middot;精确查询</a></li>
					<li onmousemove="show_menu(11)" id="f011"><a href="#">&middot;组合条件查询</a></li>
				</ul>
			</div>
		    <div class="left02down01"><a onclick="show_menuB(81)" href="javascript:;">
		      <div id="Bf081" class="left02down01_img"></div>
		      用户密码管理</a></div>
			<div class="left02down01_xia noneBox" id="Bli081">
				<ul>
					<li onmousemove="show_menu(12)" id="f012"><a href="#">&middot;找回密码</a></li>
					<li onmousemove="show_menu(13)" id="f013"><a href="./changepw.php" rel = "nofollow" target = "show">&middot;更改密码</a></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="left02">
		<div class="left02top">
			<div class="left02top_right"></div>
			<div class="left02top_left"></div>
			<div class="left02top_c">基本设置</div>
		</div>
		<div class="left02down">
			<div class="left02down01"><a href="./seo.php" target = "show" rel = "nofollow"><div class="left02down01_img"></div>SEO设置</a></div>
			<div class="left02down01"><a href="#"><div class="left02down01_img"></div>关于页面</a></div>
			<div class="left02down01"><a href="http://helloz.duoshuo.com/admin/" target = "show"><div class="left02down01_img"></div>评论管理</a></div>
		</div>
	</div>
	<div class="left02">
		<div class="left02top">
			<div class="left02top_right"></div>
			<div class="left02top_left"></div>
			<div class="left02top_c">文章管理</div>
		</div>
		<div class="left02down">
			<div class="left02down01"><a href="./publish.php" target = "show" rel = "nofollow"><div class="left02down01_img"></div>发表文章</a></div>
			<div class="left02down01"><a href="./list.php" target = "show"><div class="left02down01_img"></div>文章列表</a></div>
			<div class="left02down01"><a href="#"><div class="left02down01_img"></div>文章检索</a></div>
		</div>
	</div>
	<div class="left01">
		<div class="left03_right"></div>
		<div class="left01_left"></div>
		<div class="left03_c"><a href = "?id=out" target="_parent">安全退出</a></div>
	</div>
</div>
<div class="rrcc" id="RightBox">
	<iframe name = "show" style = "width:96%;height:94%;margin:10px;border:0px solid #868686;"></iframe>
</div>
</body>
</html>
<?php }} ?>
