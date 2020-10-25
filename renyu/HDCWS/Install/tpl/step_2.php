<?php if (!defined('INSTALL_STATUS')) exit('Access Denied!')?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>HDCWS安装向导</title>

<link rel="stylesheet" href="css/global.css" type="text/css" />
</head>
<body>
	<?php require 'tpl/header.php';?>
	<div class="main">
		<div class="step">
			<ul>
				<li class="current"><em>1</em>检测环境</li>
				<li><em>2</em>创建数据</li>
				<li><em>3</em>完成安装</li>
			</ul>
		</div>
		<table class="table1">
			<tr>
				<th>环境检测</th>
				<th>推荐配置</th>
				<th>当前状态</th>
			</tr>
			<tr>
				<td>操作系统</td>
				<td>Linux&nbsp;/&nbsp;WNT</td>
				<td><?php echo $os_software;?></td>
			</tr>
			<tr>
				<td>PHP版本</td>
				<td>&gt;5.2.8</td>
				<td><?php echo $environment_phpversion;?></td>
			</tr>
			<tr>
				<td>Mysql版本</td>
				<td>&gt;5.1.0</td>
				<td><?php echo $environment_mysql;?></td>
			</tr>
			<tr>
				<td>附件上传</td>
				<td>&gt;2M</td>
				<td><?php echo $environment_upload;?></td>
			</tr>
			<tr>
				<td>SESSION</td>
				<td><?php echo $lang['mustopen'];?></td>
				<td><?php echo $environment_session;?></td>
			</tr>
			<tr>
				<td>ICONV</td>
                <td><?php echo $lang['mustopen'];?></td>
				<td><?php echo $environment_iconv;?></td>
			</tr>
			<tr>
				<td>GD扩展</td>
                <td><?php echo $lang['mustopen'];?></td>
				<td><?php echo $environment_gd;?></td>
			</tr>			
			<tr>
				<td>mbstring扩展</td>
                <td><?php echo $lang['mustopen'];?></td>
				<td><?php echo $environment_mb;?></td>
			</tr>
		</table>
		<table class="table1">
			<tr>
				<th>目录权限检测</th>
				<th>&nbsp;</th>
				<th>写入</th>
				<th>读取</th>
			</tr>
			<?php foreach ($file as $dirvalue) {?>
			<tr>
				<td colspan="2"><?php echo $dirvalue?></td>
				<td>
				<?php 
					$dirvalue = dirname(getcwd()).'/'.ltrim($dirvalue,'/');
					echo is_readable($dirvalue) ? '<span class="ok">&nbsp;</span>' : '<span class="no">&nbsp;</span>';
				?>
				</td>
				<td><?php echo is_writable($dirvalue) ? '<span class="ok">&nbsp;</span>' : '<span class="no">&nbsp;</span>';?></td>
			</tr>
			<?php }?>
		</table>
		<div class="action"><a href="javascript:history.go(-1);" class="btn_blue">上一步</a>&nbsp;&nbsp;&nbsp;<a href="index.php?step=3" class="btn_blue">下一步</a></div>
	</div>
</body>
</html>