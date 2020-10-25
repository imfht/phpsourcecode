<?php if (!defined('SHANYU_INSTALL')) exit('Access Denied!')?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $lang['install_title'];?></title>

<link rel="stylesheet" href="css/global.css" type="text/css" />
<script type="text/javascript" src="css/jquery.min.js"></script>
</head>
<body>
	<?php require 'tpl/header.php';?>
	<div class="main">
		<div class="step">
			<ul>
				<li class="ok"><em>1</em><?php echo $lang['detection_environment']; ?></li>
				<li class="current"><em>2</em><?php echo $lang['data_create']; ?></li>
				<li><em>3</em><?php echo $lang['complete_installation']; ?></li>
			</ul>
		</div>
		<form action="index.php?step=3" method="post">
		<table class="table1">
			<tr>
				<th width="10%"><?php echo $lang['database_information']; ?></th>
				<th><?php echo $lang['database_information_tip']; ?></th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;<?php echo $lang['install_mysql_host']; ?>：</td>
				<td><input type="text" class="text" value="127.0.0.1" name="DB_HOST" /></td>
				<td><?php echo $lang['install_mysql_host_intro']; ?></td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;<?php echo $lang['install_mysql_port']; ?>：</td>
				<td><input type="text" class="text" value="3306" name="DB_PORT" /></td>
				<td><?php echo $lang['install_mysql_port_intro']; ?></td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;<?php echo $lang['install_mysql_username']; ?>：</td>
				<td><input type="text" class="text" value="root" name="DB_USER" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo $lang['install_mysql_password']; ?>：</td>
				<td><input type="text" class="text" value="" name="DB_PWD" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;<?php echo $lang['install_mysql_name']; ?>：</td>
				<td><input type="text" class="text" value="shanyucms" name="DB_NAME" /></td>
				<td><?php echo $lang['install_mysql_name_intro']; ?></td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;<?php echo $lang['install_mysql_prefix']; ?>：</td>
				<td><input type="text" class="text" value="sy_" name="DB_PREFIX" /></td>
				<td><?php echo $lang['install_mysql_prefix_intro']; ?></td>
			</tr>
			<tr>
				<th><?php echo $lang['site_configuration']; ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td><?php echo $lang['site_name']; ?>：</td>
				<td><input type="text" class="text" value="<?php echo $lang['site_name_default']; ?>" name="WEB_NAME" /></td>
				<td>&nbsp;</td>
			</tr>
<!-- 			<tr>
				<td><font class="red">*</font>&nbsp;<?php echo $lang['site_url']; ?>：</td>
				<td><input type="text" class="text" value="<?php echo $weburl;?>" name="WEB_URL" /></td>
				<td><?php echo $lang['site_url_intro']; ?></td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;<?php echo $lang['site_style']; ?>：</td>
				<td><input type="radio" name="WEB_STYLE" value="default" checked="checked"><?php echo $lang['site_style_c']; ?><input type="radio" name="WEB_STYLE" value="blog"><?php echo $lang['site_style_b']; ?></td>
				<td>&nbsp;</td>
			</tr> -->
			<tr>
				<th><?php echo $lang['website_administrator']; ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;<?php echo $lang['username']; ?>：</td>
				<td><input type="text" class="text" value="admin" name="username" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;<?php echo $lang['password']; ?>：</td>
				<td><input type="text" class="text" value="" name="password" /></td>
				<td><?php echo $lang['password_intro']; ?></td>
			</tr>
			<tr>
				<td><font class="red">*</font>&nbsp;E-mail：</td>
				<td><input type="text" class="text" value="" name="email" /></td>
				<td>&nbsp;</td>
			</tr>			
<!-- 			<tr>
				<td><?php echo $lang['test_data']; ?>：</td>
				<td><label><input type="checkbox" value="1" name="add_test" /><?php echo $lang['test_data_intro']; ?></label></td>
				<td>&nbsp;</td>
			</tr> -->			
		</table>
		<div class="action"><a href="javascript:history.go(-1);" class="btn_blue"><?php echo $lang['previous'];?></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="btn_blue" onclick="postData()"><?php echo $lang['next'];?></a></div>
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