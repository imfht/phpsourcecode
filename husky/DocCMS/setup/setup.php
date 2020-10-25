<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>安装程序-DocCms X 1.0 [音译：稻壳Cms]免费开源企业建站系统</title>
<link href="setup.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-1.3.2.min.js"></script>
</head>
<body>
<div id="header">
	<div id="head">
    	<a href="http://www.doccms.com" target="_blank"><img src="images/logo.png" width="186" height="51" align="left" /></a><a href="http://www.doccms.com" target="_blank" class="doctitle">DocCms 2013正式版 X1.0 PHP</a>
		<div id="link">
		  <ul>
				<li><a href="http://www.doccms.com" target="_blank"><img src="images/index_icon.gif" width="30" height="30" alt="官方网站" /></a></li>
				<li class="nobg"><a href="http://www.doccms.net" target="_blank"><img src="images/bbs_icon.gif" width="30" height="30" alt="官方论坛" /></a></li>
			</ul>
		</div>
	</div>
</div>
<?php
@error_reporting(E_ALL ^ E_NOTICE);

if (is_file(dirname(__FILE__).'/../config/doccms.lock') && filesize(dirname(__FILE__).'/../config/doccms.lock')==0)
    die('系统检测到已经安装，若要重新安装，请删除/config/doccms.lock文件再进行操作。');
if (!is_file('db-config-sample.php'))
    die('对不起，我需要 db-config-sample.php 这个文件，可是你的目录中没有，你可以重新下载一个试试。');
if(!intval($_REQUEST['step']))
{
?>
<div id="content">
	<div id="cright">
		<div id="install">
			<h2>DocCms X 免费开源软件使用许可协议</h2><hr />
			<div id="crs">
				<textarea name="summary" cols="70" rows="10" class="txtArea" id="summary">
  DocCms X 1.0 [音译：稻壳Cms] Php Cms系统免费开源协议声明：

一、版权所有 (c) 2006-2012, Doccms.com,Shlcms.com,Doooc.com Dev Team 保留所有权力.

二、DocCms 由 稻壳网 Dev Team 独立开发,全部核心技术归属 稻壳网 Dev Team（DocCms,ShlCms 在中国国家版权局著作权登记号为:2010SR008829）
官方网站为 http://www.doccms.com ；官方论坛为 http://www.doccms.net ；DocCms for doooc地址为 http://www.doooc.com ；
本授权协议适用于 DocCms 任何版本，稻壳网 Dev Team 拥有对本授权协议的最终解释权和修改权。

三、DocCms企业信息化建站系统
     1 、DocCms,ShlCms 著作权已在中华人民共和国国家版权局注册，著作权登记号为: 2010SR008829 并受到法律和国际公约保护。如果您需要采用DocCms系统的部分程序构架其他程序系统，请务必取得我们的同意和授权，否则我们将追究责任并索赔相关损失！修改后的代码，未经书面许可，严禁公开发布，更不得利用其从事盈利业务；
     2 、所有用户均可查看DocCms 的全部源代码,也可以根据自己的需要对其进行修改!但无论如何，既无论用途如何、是否经过修改或美化、修改程度如何，只要您使用 DocCms 的任何整体或部分程序算法，都必须保留程序后台部分页脚处的 DocCms 名称和 http://www.doccms.com 和 http://www.doooc.com 的链接地址；
     3 、未经商业授权，不得以除稻壳Cms和DocCms以外其它任何品牌将本软件用于商业用途(企业网站或以盈利为目的经营性网站)，否则我们将保留追究法律责任的权力。有关 DocCms 授权包含的服务范围，技术支持等，请参看 http://www.doccms.com/BusinessService/；
     注：对于违反以上条款，或以任何目的复制或发行 DocCms 的组织或个人，我们将依法追究其法律责任。

四、本授权协议适用于稻壳Cms所有版本，稻壳Cms开发团队拥有对本授权协议的最终解释权，以下为协议许可的权利和约束： 
  I 协议许可的权利 
    1. 您可以在完全遵守本最终用户授权协议的基础上，按官方免费开源协议将本软件合法的应用于商业用途，而不必支付软件版权授权费用； 
    2. 您可以在协议规定的约束和限制范围内修改稻壳Cms源代码(如果被提供的话)或界面风格以适应您的网站要求，但必须将后台界面和源代码信息里保留稻壳Cms官方版权；
    3. 您拥有使用本软件构建的网站中全部网站资料、文章及相关信息的所有权，并独立承担与网站内容的相关法律义务； 
    4. 提交商业应用真实资料备案后，您可以将本软件应用于相应商业用途，关于商业应用备案详情请参考http://www.doccms.com/Supporting/。

  II 协议规定的约束和限制 
    1. 未提交商业应用真实资料备案之前，不得以除稻壳Cms和DocCms以外其它任何品牌将本软件用于商业用途（包括但不限于以赚取佣金为目的的网站制作、风格模板定制、功能定制等）。商业应用备案相关说明请登陆http://www.doccms.com/Supporting/查阅； 
    3. 无论如何，即无论用途如何、是否经过修改或美化、修改程度如何，只要使用稻壳Cms的整体或任何部分，未经书面许可，网站管理后台页面的相应版权信息及http://www.doccms.com或其他相应官方网址链接都必须保留，而不能清除或修改，一旦发现，责任必纠； 
    4. 禁止在稻壳Cms的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发，一旦发现，官方必追纠相关方法律责任； 
    5. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。 

  III 有限担保和免责声明 
    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的； 
    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在未购买相应产品技术服务之前，我们不承诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任； 
    3. 稻壳Cms开发团队不对使用本软件构建的网站中的文章或信息承担责任。
    
五、有关稻壳Cms最终用户授权协议、商业授权与技术服务的详细内容，均由稻壳Cms官方网站独家提供。稻壳Cms开发团队拥有在不事先通知的情况下，修改授权协议和备案规则的权力，修改后的协议或备案规则对自改变之日起的新授权用户生效。 

六、电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始安装稻壳Cms，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。

七、免责声明:
    1 、利用本软件构建的网站的任何信息内容以及导致的任何版权纠纷和法律争议及后果，官方不承担任何责任。
    2 、损坏包括程序的使用(或无法再使用)中所有一般化,特殊化,偶然性的或必然性的损坏(包括但不限于数据的丢失,自己或第三方所维护数据的不正确修改,和其他程序协作过程中程序的崩溃等),官方不承担任何责任。 
				</textarea>
				<a href="setup.php?step=1" class="button orange step1next">下一步</a>
			</div>
		</div>
	</div>
</div>
<?php
}
elseif(intval($_GET['step'])==1)
{
?>
<div id="content">
	<div id="ctop">
    	<h1>1.安装须知</h1>
		<div id="steps">
			<ul>
				<li id="selected"><a href="">1</a><span>安装须知</span></li>
				<li><a href="">2</a><span>运行环境检测</span></li>
				<li><a href="">3</a><span>文件权限设置</span></li>
				<li><a href="">4</a><span>帐号设置</span></li>
				<li class="over"><a href="">5</a><span>安装完成</span></li>
			</ul>
		</div>
	</div>
	<div id="cright">
		<div id="install">
			<div id="crs">
				<h3>（一）运行环境需求</h3>
				<p>* 可用的 httpd 服务器（如 Apache，IIS，Nginx 等）</p>
				<p>* PHP 5.0 及以上 </p>
				<p>* Mysql 使用5.0以上(请使用4.3以下版本的用户，先升级您的数据库到5.x版本)</p>
				<p>&nbsp;</p>
				<h3>（二）程序安装步骤</h3>
				<p>* 第一步：使用ftp工具中的"二进制模式"将本软件包 doccms 目录内容上传至服务器根目录。</p>
				<p>* 第二步：访问 http://yourwebsite/setup/setup.php 进入安装程序，根据安装向导提示完成安装。</p>
                <a href="setup.php" class="orange button back">上一步</a>
				<a href="setup.php?step=2" class="orange button next">下一步</a>
			</div>
		</div>
	</div>
</div>
<?php }
elseif(intval($_GET['step'])==2)
{
$PHP_GD = '';
if(extension_loaded('gd'))
{
	if(function_exists('imagepng')) $PHP_GD .= '.png';
	if(function_exists('imagejpeg')) $PHP_GD .= ' .jpg';
	if(function_exists('imagegif')) $PHP_GD .= ' .gif';
}
?>
<div id="content">
	<div id="ctop">
    	<h1>2.运行环境检测</h1>
		<div id="steps">
			<ul>
				<li><a href="">1</a><span>安装须知</span></li>
				<li id="selected"><a href="">2</a><span>运行环境检测</span></li>
				<li><a href="">3</a><span>文件权限设置</span></li>
				<li><a href="">4</a><span>帐号设置</span></li>
				<li class="over"><a href="">5</a><span>安装完成</span></li>
			</ul>
		</div>
	</div>
	<div id="cright">
		<div id="install">
			<div id="crs">
				<table width="100%" cellpadding="0" cellspacing="0" class="table_list">
                  <tr>
                    <th width="97">检查项目</th>
                    <th width="306">当前环境</th>
                    <th width="194">建议环境</th>
                    <th width="68">功能影响</th>
                  </tr>
                  <tr>
                    <td>操作系统</td>
                    <td><?php echo php_uname();?></td>
                    <td>Windows_NT/Linux/Freebsd</td>
                    <td align="center"><span class="yellow">√</span></td>
                  </tr>
                  <tr>
                    <td>web 服务器</td>
                    <td><?php echo $_SERVER['SERVER_SOFTWARE'];?></td>
                    <td>Apache/Nginx/IIS</td>
                    <td align="center"><span class="yellow">√</span></td>
                  </tr>
                  <tr>
                    <td>php 版本</td>
                    <td><?php echo phpversion();?></td>
                    <td>php 5.0 及以上</td>
                    <td align="center"><?php if(phpversion() >= '5.0.0'){ ?><span class="yellow">√<?php }else{ ?><span class="red">无法安装</span><?php }?></td>
                  </tr>
                  <tr>
                    <td>mysql 扩展</td>
                    <td><?php if(extension_loaded('mysql')){ ?>√<?php }else{ ?>×<?php }?></td>
                    <td>建议开启</td>
                    <td align="center"><?php if(extension_loaded('mysql')){ ?><span class="yellow">√</span><?php }else{ ?><span class="red">无法安装</span><?php }?></td>
                  </tr>
                  <tr>
                    <td>gd 扩展</td>
                    <td><?php if($PHP_GD){ ?>√ （支持 <?php echo $PHP_GD;?>）<?php }else{ ?>×<?php }?></td>
                    <td>建议开启</td>
                    <td align="center"><?php if($PHP_GD){ ?><span class="yellow">√</span><?php }else{ ?><span class="red">不支持缩略图和水印</span><?php }?></td>
                  </tr>
				  <tr>
                    <td>ob_start 缓存</td>
                    <td><?php if(function_exists(ob_start)){ ?>√ （支持网站静态化）<?php }else{ ?><span class="red">×</span><?php }?></td>
                    <td>建议开启</td>
                    <td align="center"><?php if(function_exists(ob_start)){ ?><span class="yellow">√</span><?php }else{ ?><span class="red">不支持网站静态化</span><?php }?></td>
                  </tr>
                </table>
				<a href="setup.php?step=1" class="orange button back">上一步</a>
				<a href="setup.php?step=3" class="orange button next">下一步</a>
			</div>
		</div>
	</div>
</div>
<?php
}
elseif(intval($_GET['step'])==3)
{
?>
<div id="content">
	<div id="ctop">
    	<h1>3.文件权限设置</h1>
		<div id="steps">
			<ul>
				<li><a href="">1</a><span>安装须知</span></li>
				<li><a href="">2</a><span>运行环境检测</span></li>
				<li id="selected"><a href="">3</a><span>文件权限设置</span></li>
				<li><a href="">4</a><span>帐号设置</span></li>
				<li class="over"><a href="">5</a><span>安装完成</span></li>
			</ul>
		</div>
	</div>
	<div id="cright">
		<div id="install">
			<div id="crs">
				<table class="tb"
				<tr><th align="left">目录文件权限检测[需 766 或 777 权限]</th><th align="left">所需状态</th><th align="left">当前状态</th></tr>
				<tr><td>/setup/empty5.sql</td><td class="right">可写</td><?php echo file_mode_info('../setup/empty5.sql')?'<td class="right">可写</td>':'<td class="wrong">不可写</td>';?></tr>
				<tr><td>/setup/demo5.sql</td><td class="right">可写</td><?php echo file_mode_info('../setup/demo5.sql')?'<td class="right">可写</td>':'<td class="wrong">不可写</td>';?>
				<tr><td>[文件]/admini/nav.php</td><td class="right">可写</td><?php echo file_mode_info('../admini/nav.php')?'<td class="right">可写</td>':'<td class="wrong">不可写</td>';?></tr>
				<tr><td>[目录]/config/</td><td class="right">可写</td><?php echo file_mode_info('../config/')?'<td class="right">可写</td>':'<td class="wrong">不可写</td>';?></tr>
				<tr><td>[文件]/config/doc-config.php</td><td class="right">可写</td><?php echo file_mode_info('../config/doc-config.php')?'<td class="right">可写</td>':'<td class="wrong">不可写</td>';?></tr>
				<tr><td>[目录]/html/</td><td class="right">可写</td><?php echo file_mode_info('../html/')?'<td class="right">可写</td>':'<td class="wrong">不可写</td>';?></tr>
				<tr><td>[目录]/upload/</td><td class="right">可写</td><?php echo file_mode_info('../upload/')?'<td class="right">可写</td>':'<td class="wrong">不可写</td>';?></tr>
				<tr><td>[目录]/skins/【如无需后台上传官方标准打包模板，可为"不可写"】</td><td class="right">可写</td><?php echo file_mode_info('../skins/')?'<td class="right">可写</td>':'<td class="wrong">不可写</td>';?></tr>
				<tr><td>[目录]/temp/</td><td class="right">可写</td><?php echo file_mode_info('../temp/')?'<td class="right">可写</td>':'<td class="wrong">不可写</td>';?></tr>
				<tr><td>[目录]/temp/data/</td><td class="right">可写</td><?php echo file_mode_info('../temp/data/')?'<td class="right">可写</td>':'<td class="wrong">不可写</td>';?></tr>
				<tr><td>[目录]/admini/controllers/system/userinfo/config/</td><td class="right">可写</td><?php echo file_mode_info('../admini/controllers/system/userinfo/config/')?'<td class="right">可写</td>':'<td class="wrong">不可写</td>';?></tr>
				</table>
				<p class="orword">*【linux系统 务必 /config目录设可写权限766或777；】</p>
				<p class="orword">*【如果您需要在后台上传模板或备份数据库以及将网站生成纯静态HTML文件同时也需要对<br />&nbsp;&nbsp;/upload、/skins、/temp、/html四个目录以及 &nbsp;/temp/data目录设可写权限666或777；】</p>
				<p class="orword">*【linux系统 务必 /controllers/system/userinfo/config目录设可写权限666或777；】</p>
				<p class="orword">*【注：强烈建议您在程序安装后将setup目录删除或移走到虚拟主机以外的目录】</p>
				<a href="setup.php?step=2" class="orange button back">上一步</a>
				<a href="setup.php?step=4" class="orange button next">下一步</a>
			</div>
		</div>
	</div>
</div>
<?php
}
elseif(intval($_GET['step'])==4)
{
$rpath=str_replace('/setup/setup.php','',$_SERVER['SCRIPT_NAME']);
?>
<script>
var dbflag=0;
$(document).ready(function(){
	checkdb();
	$('#dbhost').bind('blur',function(){
		checkdb();
	});
	$('#uname').bind('blur',function(){
		checkdb();
	});
	$('#pwd').bind('blur',function(){
		checkdb();
	});
	$('#dbname').bind('blur',function(){
		checkdb();
	});
});
var istrue=function(){
		if(!$("#dbhost").val()){
			alert("\u4E3B\u673A\u540D\u79F0\u4E0D\u80FD\u4E3A\u7A7A\uFF01");return false;
		}
		if(!$("#uname").val()){
			alert("\u7528\u6237\u540D\u4E0D\u80FD\u4E3A\u7A7A\uFF01");return false;
		}
		if(!$("#pwd").val()){
			//alert("\u5BC6\u7801\u4E0D\u80FD\u4E3A\u7A7A\uFF01");return false;
		}
		if(!$("#dbname").val()){
			alert("\u6570\u636E\u5E93\u540D\u79F0\u4E0D\u80FD\u4E3A\u7A7A\uFF01");return false;
		}
}
var checkdb=function(){
		istrue();
		$.ajax({
		   type: "POST",
		   url: "checkdb.php?action=chkdb",
		   data: "dbhost="+$("#dbhost").val()+"&uname="+$("#uname").val()+"&pwd="+$("#pwd").val()+"&dbname="+$("#dbname").val(),
		   success: function(msg){
			   dbflag=msg;
			 if(msg=='1'){
				$("#idoc").show();
				$("#idoc").attr("class","green");
				$("#idoc").html("\u6570\u636E\u5E93\u5B58\u5728\uFF01");
			 }else if(msg=='-1'){
				$("#idoc").show();
				$("#idoc").attr("class","red");
				$("#idoc").html("\u6570\u636E\u5E93\u8FDE\u63A5\u5931\u8D25");
			 }else if(msg=='0'){
				//strhtml="<input type='button' name='crtdb' id='crtdb' value='\u521B\u5EFA\u6570\u636E\u5E93' />";
				$("#idoc").show();
				$("#idoc").attr("class","yelo");
				$("#idoc").html("\u6307\u5B9A\u6570\u636E\u5E93\u4E0D\u5B58\u5728\uFF0C\u9700\u8981\u521B\u5EFA");
			 }else{
				alert('Error');return false;
			}
		   }
		});
}
var createdb=function(){
		istrue();
		$.ajax({
		   type: "POST",
		   url: "checkdb.php?action=creatdb",
		   data: "dbhost="+$("#dbhost").val()+"&uname="+$("#uname").val()+"&pwd="+$("#pwd").val()+"&dbname="+$("#dbname").val(),
		   success: function(msg){
			 //dbflag=msg;
			 if(msg=='1'){
				 return true;
			 }else if(msg=='-1'){
				$("#idoc").show();
				$("#idoc").attr("class","red");
				$("#idoc").html("\u6570\u636E\u5E93\u8FDE\u63A5\u5931\u8D25\uFF0C\u8BF7\u6838\u5B9E\u60A8\u7684\u4FE1\u606F\uFF01");
				return false;
			 }else if(msg=='0'){
				$("#idoc").show();
				$("#idoc").attr("class","yelo");				
				$("#idoc").html("\u6570\u636E\u5E93\u540D\u79F0\u4E0D\u80FD\u4E3A\u7A7A\uFF01");
				return false;
			 }else{
				alert('Err:'+msg);
				return false;
			}
		   }
		});
}
var next=function(){
	if (dbflag==-1){
		alert("\u6570\u636E\u5E93\u8FDE\u63A5\u5931\u8D25");return false;
	}else if(dbflag==0){
		if(confirm("确定要创建名为"+$("#dbname").val()+"的数据库吗？")){
			createdb();
		}else{
			return false;
		}
	}
}
</script>
<div id="content">
	<div id="ctop">
    	<h1>4.帐号设置</h1>
		<div id="steps">
			<ul>
				<li><a href="">1</a><span>安装须知</span></li>
				<li><a href="">2</a><span>运行环境检测</span></li>
				<li><a href="">3</a><span>文件权限设置</span></li>
				<li id="selected"><a href="">4</a><span>帐号设置</span></li>
				<li class="over"><a href="">5</a><span>安装完成</span></li>
			</ul>
		</div>
	</div>
	<div id="cright">
		<div id="install">
			<div id="crs">
			<form id="form1" name="form1" method="post" action="setup.php?step=5" onsubmit="return next();">
				<p class="tdtbtitle">1.设置数据库信息：</p>
				 <ul class="tdtb">
					<li><span>主机名称：</span>
                    <div class="tdtbrt"><input name="dbhost" type="text" id="dbhost" value="localhost" />(99%的情况下不需要修改)</div>
                    </li>
					<li><span>用户名：</span>
					  <div class="tdtbrt"><input name="uname" type="text" id="uname" value="root" />
					(空间商分配给你的数据库管理用户名)</div></li>
					<li><span>密&nbsp;&nbsp;&nbsp;&nbsp;码：</span>
					  <div class="tdtbrt"><input name="pwd" type="text" id="pwd" />
					(空间商分配给你的数据库管理密码)</div></li>
					<li><span>数据库名称 ：</span>
					  <div class="tdtbrt"><input name="dbname" type="text" id="dbname" value="doccms" /> 
					  (空间商分配给你的数据库名称)</div></li>
                    
					<li><span>数据表前缀：</span>
					  <div class="tdtbrt"><input name="pix" type="text" id="pix" value="doc_" /> 
					  (如果您在您的空间中安装多个稻壳系统，请更改此项.例如:中文版使用cn_ 英文版使用en_)</div></li>
						<li><span>系统根路径：</span>
							<div class="tdtbrt"><input name="rootpath" type="text" id="rootpath" value="<?php echo $rpath ?>" />(99%的情况下默认为空。如果本系统不在环境根目录，请修改。例如：你的网站在/cn 目录下，请填入 /cn)</div>
						</li>
						<li><span>是否安装测试数据 ：</span>
                        <div class="tdtbrt"><input name="testdate" type="checkbox" id="testdate" value="1" checked="checked" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(如果您是第一次使用本系统，建议您安装测试数据，以帮助您了解、熟悉和使用本系统)</div>
						</li>
						<li><span>Mysql 数据库版本 ：</span>
							<div class="tdtbrt">5.x<input name="mysqlver" type="radio" id="mysqlver" value="1" checked="checked" />&nbsp;&nbsp;&nbsp;&nbsp; (如果您的数据库版本是4.x版本,请升级您的数据库到5.x版本)</div>
						</li>
					</ul>
					<p class="tdtbtitle">2.设置站点信息：</p>
					<ul class="tdtb">
						<li><span>创始人昵称 ：</span>
							<div class="tdtbrt"><input name="adminnick" type="text" id="adminnick" value="创始人" />(用来在系统中显示你的名字，可以为中文。)</div>
						  </li>
						<li><span>创始人帐户 ：</span>
							<div class="tdtbrt"><input name="adminname" type="text" id="adminname" value="admin" /></div>
						 </li>
						<li><span>创始人邮箱 ：</span>
							<div class="tdtbrt"><input name="mail" type="text" id="mail" value="admin@localhost" /></div></li>
						<li><span>创始人密码 ：</span>
						 	<div class="tdtbrt"><input name="adminpwd" type="text" id="adminpwd" value="admin" /> (设置后台登陆的密码,请输入20位以内的字母或数字)</div>
						 </li>
					</ul>
                    <p id="idoc" class="dis"></p>
				<input type="button" name="button" onclick="history.back(1)" value="上一步" class="orange button back" />
				<input type="submit" name="Submit" value="下一步" class="orange button next" />
				</form>
			</div>
		</div>
	</div>
</div>
<?php
}
elseif(intval($_GET['step'])==5)
{	
	foreach($_REQUEST as $k=>$v){
		$$k=is_array($v)?$v:trim($v);
	}
function_exists('date_default_timezone_set') && date_default_timezone_set('Asia/Shanghai');
$testDb = @mysql_connect($dbhost, $uname, $pwd);
if (!$testDb) {
	die('不能够链接数据库:数据库服务器名、用户名或密码错误。 ');
}
mysql_select_db($dbname,$testDb) or die ($dbname.'数据表不存在。 ');
// 判断环境是否支持伪静态
$www = $_SERVER['SERVER_NAME']=='localhost'?'127.0.0.1:'.$_SERVER["SERVER_PORT"]:$_SERVER['HTTP_HOST'];
$url =  'http://'.$www.$rootpath.'/index.html';

if(availableUrl($url)){
	$urlwrite = 'true';
}
else
{
	$urlwrite = 'false';
}
//读写doccms.lock
if(!string2file('','../config/doccms.lock')){
	echo '/config/目录下创建 doccms.lock 文件失败。';exit;
}
//读写配置文件
$configFile = file('db-config-sample.php');
$handle = fopen('../config/doc-config-cn.php', 'w');
foreach ($configFile as $line_num => $line) {
	switch (substr($line,0,16)) {
		case "define('DB_HOSTN":
			fwrite($handle, str_replace("localhost", $dbhost, $line));
			break;
		case "define('DB_USER'":
			fwrite($handle, str_replace("'user'", "'$uname'", $line));
			break;
		case "define('DB_PASSW":
			fwrite($handle, str_replace("'pwd'", "'$pwd'", $line));
			break;
		case "define('DB_DBNAM":
			fwrite($handle, str_replace("doccms", $dbname, $line));
			break;
		case "define('TB_PREFI":
			fwrite($handle, str_replace("doc_", $pix, $line));
			break;
		case "define('doccmsbi":
			fwrite($handle, str_replace("now", date('Y-m-d'), $line));
			break;
		case "define('ROOTPATH":
			fwrite($handle, str_replace("root", $rootpath, $line));
			break;
		case "define('URLREWRI":
			fwrite($handle, str_replace("false", $urlwrite, $line));
			break;
		default:
			fwrite($handle, $line);
	}
}
fclose($handle);
@chmod('../config/doc-config-cn.php', 0666);

//测试数据库。
require("../config/doc-config-cn.php");	

if($testdate == '1' && $mysqlver == '1')
{
	$sqlFile='demo5.sql';
}
elseif($testdate != '1' && $mysqlver == '1')
{
	$sqlFile='empty5.sql';
}
$sql_setup=file2String($sqlFile);
$testDb = @mysql_connect(DB_HOSTNAME, DB_USER, DB_PASSWORD);
mysql_select_db(DB_DBNAME,$testDb);
/* 加密密码 start */
require("../inc/class.docencryption.php");
$docencryption = new docEncryption($adminpwd);
$encryptionadminpwd=$docencryption->to_string();
/* 加密密码  end */
if(empty($_SERVER['REMOTE_ADDR'])){
	$remote_addr="127.0.0.1";
}
$remote_addr=$_SERVER['REMOTE_ADDR'];

$sql_setup="SET NAMES UTF8;\n\n".$sql_setup;
$sql_setup.="INSERT INTO ##_user (`nickname` , `email` , `username` , `pwd` , `role` , `right` , `dtTime` , `auditing` , `ip`) VALUES ('".$adminnick."', '".$mail."', '".$adminname."', '".$encryptionadminpwd."', '10', 'webadmin', '".date('Y-m-d H:i:s')."', 1, '".$remote_addr."')";
$sql_setup.="--<br>--";
$sql_setup=str_replace("##_", TB_PREFIX, $sql_setup);
$sql_arr=explode('--<br>--',$sql_setup);

foreach ($sql_arr as $sql_o)
{
	mysql_query("SET NAMES UTF8;",$testDb);
	mysql_query($sql_o,$testDb);
}
@mysql_free_result(testDb);
@mysql_close(testDb);
?>
<div id="content">
	<div id="ctop">
    	<h1>5.安装完成</h1>
		<div id="steps">
			<ul>
				<li><a href="">1</a><span>安装须知</span></li>
				<li><a href="">2</a><span>运行环境检测</span></li>
				<li><a href="">3</a><span>文件权限设置</span></li>
				<li><a href="">4</a><span>帐号设置</span></li>
				<li id="selected" class="over"><a href="">5</a><span>安装完成</span></li>
			</ul>
		</div>
	</div>
	<div id="cright">
		<div id="install">
			<div id="crs">
				<ul class="tdtb">
					<li class="orword">* 注：强烈建议您在程序安装后将 /setup 目录删除或移走到虚拟主机以外的目录;</li>
					<li>&nbsp;&nbsp;&nbsp;您网站后台默认登陆用户名：<strong><?php echo $adminname?></strong></li>
					<li>&nbsp;&nbsp;&nbsp;您网站后台默认登陆密码：<strong><?php echo $_REQUEST['adminpwd']?></strong></li>
					<li>&nbsp;&nbsp;&nbsp;您现在已经可以浏览网站的首页：<a target="_blank" href='../'>点击进入网站首页</a></li>
					<li>&nbsp;&nbsp;&nbsp;也可以进入后台管理系统进行管理：<a target="_blank" href='../admini/index.php'>进入网站后台/admini/</a></li>
					<li>&nbsp;&nbsp;&nbsp;安全起见，请将 /setup/ 目录删除。并将根目录下的doc-config.php文件权限设置为 766 或 777。</li>
				</ul>
				<p>	<a href="../admini/index.php" class="button orange step1next">完成</a></p>
			</div>
		</div>
	</div>
</div>
<?php
}
function file2String($filePath)
{
	$fp = fopen($filePath,"r");
	$content_= fread($fp, filesize($filePath));
	fclose($fp);
	return $content_;

}
//生成新的文件($str为字符串,$filePath为生成时的文件路径包括文件名)
function string2file($str,$filePath)
{
	$fp=fopen($filePath,'w+');
	if(!$fp)return false;
	if(fwrite($fp,$str)=== false)return false;
	fclose($fp);
	return true;
}
function file_mode_info($file_path)
{
    /* 如果不存在，则不可读、不可写、不可改 */
    if (!file_exists($file_path))
    {
        return false;
    }
    $mark = 0;
    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
    {
        /* 测试文件 */
        $test_file = $file_path . '/cf_test.txt';
        /* 如果是目录 */
        if (is_dir($file_path))
        {
            /* 检查目录是否可读 */
            $dir = @opendir($file_path);
            if ($dir === false)
            {
                return $mark; //如果目录打开失败，直接返回目录不可修改、不可写、不可读
            }
            if (@readdir($dir) !== false)
            {
                $mark ^= 1; //目录可读 001，目录不可读 000
            }
            @closedir($dir);
            /* 检查目录是否可写 */
            $fp = @fopen($test_file, 'wb');
            if ($fp === false)
            {
                return $mark; //如果目录中的文件创建失败，返回不可写。
            }
            if (@fwrite($fp, 'directory access testing.') !== false)
            {
                $mark ^= 2; //目录可写可读011，目录可写不可读 010
            }
            @fclose($fp);
            @unlink($test_file);
            /* 检查目录是否可修改 */
            $fp = @fopen($test_file, 'ab+');
            if ($fp === false)
            {
                return $mark;
            }
            if (@fwrite($fp, "modify test.\r\n") !== false)
            {
                $mark ^= 4;
            }
            @fclose($fp);
            /* 检查目录下是否有执行rename()函数的权限 */
            if (@rename($test_file, $test_file) !== false)
            {
                $mark ^= 8;
            }
            @unlink($test_file);
        }
        /* 如果是文件 */
        elseif (is_file($file_path))
        {
            /* 以读方式打开 */
            $fp = @fopen($file_path, 'rb');
            if ($fp)
            {
                $mark ^= 1; //可读 001
            }
            @fclose($fp);
            /* 试着修改文件 */
            $fp = @fopen($file_path, 'ab+');
            if ($fp && @fwrite($fp, '') !== false)
            {
                $mark ^= 6; //可修改可写可读 111，不可修改可写可读011...
            }
            @fclose($fp);
            /* 检查目录下是否有执行rename()函数的权限 */
            if (@rename($test_file, $test_file) !== false)
            {
                $mark ^= 8;
            }
        }
    }
    else
    {        
        if (@is_writable($file_path))
        {
            $mark ^= 14;
        }
/*		if (@is_readable($file_path))
        {
            $mark ^= 1;
        }*/
    }
    return $mark;
}
/*判断URL 是否存在*/
function availableUrl($url) {
	// 避免请求超时超过了PHP的执行时间
	$executeTime = ini_get('max_execution_time');
	ini_set('max_execution_time', 0);
	$headers = @get_headers($url);
	ini_set('max_execution_time', $executeTime);
	if ($headers) {
		$head = explode(' ', $headers[0]);
		if (!empty($head[1]) && intval($head[1]) < 400)
			return true;
	}
}
?>
<div class="clear"></div>
<div id="foot">
	<div id="bottom">
    	<a href="http://www.doccms.com" target="_blank" class="btmlogo"><img src="images/bottom_logo.png" /></a>
        <span>© 2006-2013 <a href="http://www.doccms.com" target="_blank" >DocCms X</a> design. All rights reserved. </span>
        <div id="weibo">
		  <ul>
              <li><a href="http://weibo.com/doccms" target="_blank"><img src="images/sina.gif" alt="DocCms新浪微博" /></a></li>
              <li class="nobg"><a href="http://t.qq.com/doccms" target="_blank"><img src="images/qq.gif"  alt="DocCms腾讯微博" /></a></li>
          </ul>
		</div>
    </div>
</div>
<script type="text/javascript" language="javascript">//Kill IE 6
var ietips='<div id=\"_ietips\" style=\"display:none;background:#000;height:40px;line-height:40px;left:0; opacity:0.80; -moz-opacity:0.80; filter:alpha(opacity=80); position:fixed;bottom:0;width:100%;z-index:999; text-align:center; color:#FFF; font-size:16px;_bottom:auto; _width: 100%; _position: absolute; _top:expression(eval(document.documentElement.scrollTop+document.documentElement.clientHeight-this.offsetHeight-(parseInt(this.currentStyle.marginTop,10)||0)-(parseInt(this.currentStyle.marginBottom,10)||0)))\">\u5F53\u524D\u6D4F\u89C8\u5668\u7248\u672C\u592A\u4F4E\uFF0C\u60A8\u5C06\u65E0\u6CD5\u5B8C\u7F8E\u4F53\u9A8C\u6211\u4EEC\u7CFB\u7EDF\uFF01<a href=\"http://www.doccms.com\" target=\"_blank\">\u7A3B\u58F3CMS<\/a>\u5C06\u5168\u9762\u4E0D\u8003\u8651\u517C\u5BB9IE6\u7684\u95EE\u9898\uFF0C\u5982\u4E0D\u80FD\u6EE1\u8DB3\u60A8\u7684\u8981\u6C42\uFF0C\u8BF7<a href=\"http://www.shlcms.com\" target=\"_blank\">\u4E0B\u8F7DSHLCMS4.2<\/a>\u6765\u89E3\u51B3\21</div>';
if($.browser.version=="6.0"){$("body").append(ietips);setTimeout('$("#_ietips").fadeIn(2000);',1000);}</script>
</body>
</html>