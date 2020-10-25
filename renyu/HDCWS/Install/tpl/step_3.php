<?php if (!defined('INSTALL_STATUS')) exit('Access Denied!')?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>HDCWS安装向导</title>
<link rel="stylesheet" href="css/global.css" type="text/css" />
<script type="text/javascript" src="js/jquery.js"></script>
</head>
<body>
	<?php require 'tpl/header.php';?>
	<div class="main">
		<div class="step">
			<ul>
				<li class="ok"><em>1</em>检测环境</li>
				<li class="current"><em>2</em>创建数据</li>
				<li><em>3</em>完成安装</li>
			</ul>
		</div>
		<form action="index.php?step=3" method="post">
		<table class="table1">
			<tr>
				<th width="10%">数据库信息</th>
				<th>安装后,原数据库会被清空,请做好备份</th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;数据库服务器：</td>
				<td><input type="text" class="text" value="localhost" name="DB_HOST" /></td>
				<td>本地填写：localhost或127.0.0.1</td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;数据库端口：</td>
				<td><input type="text" class="text" value="3306" name="DB_PORT" /></td>
				<td>数据库端口一般为3306</td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;数据库用户名：</td>
				<td><input type="text" class="text" value="root" name="DB_USER" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>数据库密码：</td>
				<td><input type="text" class="text" value="" name="DB_PWD" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;数据库名：</td>
				<td><input type="text" class="text" value="hdcws" name="DB_NAME" /></td>
				<td>不存在则自动创建。</td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;数据库表前缀：</td>
				<td><input type="text" class="text" value="hd_" name="DB_PREFIX" /></td>
				<td>推荐使用默认。</td>
			</tr>
			<tr>
				<th>网站配置</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td>网站名称：</td>
				<td><input type="text" class="text" value="公司网站系统" name="HD_webname" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;网站网址：</td>
				<td><input type="text" class="text" value="<?php echo $weburl;?>" name="HD_weburl" /></td>
				<td>请以http://或https://开头</td>
			</tr>
			<tr>
				<th>网站超级管理员</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;用户名：</td>
				<td><input type="text" class="text" value="admin" name="username" readonly="true" /></td>
				<td>默认超级管理员用户名</td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;密&nbsp;&nbsp;&nbsp;码：</td>
				<td><input type="text" class="text" value="" name="password" /></td>
				<td>最少6位</td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;E-mail：</td>
				<td><input type="text" class="text" value="" name="HD_email" /></td>
				<td>&nbsp;</td>
			</tr>			
			<tr>
				<td>测试数据：</td>
				<td><label><input type="checkbox" value="1" name="add_test" checked="checked"/>添加默认数据！(适合新手第一次使用)</label></td>
				<td>&nbsp;</td>
			</tr>			
		</table>
		<div class="action"><a href="javascript:history.go(-1);" class="btn_blue">上一步</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="btn_blue" onclick="postData()">下一步</a></div>
		</form>
	</div>
<script type="text/javascript">
function postData() {
	var _postForm = $('form').serialize();
	$.post('index.php?step=3',_postForm,function(data){
		if(data.status == 'error') {
			alert(data.info);
			return false;
		} else {
			window.location.href = 'index.php?step=4';
		}
	},'json');
}
</script>
</body>
</html>