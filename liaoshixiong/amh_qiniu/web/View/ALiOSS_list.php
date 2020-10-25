<?php include('header.php'); ?>
<style>
#AMProxy_list input.input_text {
	width: 292px;
}
</style>
<script>

if(!WindowLocation)
{
	var WindowLocation = function (url)
	{
		window.location = url;
	}
	var WindowOpen = function (url)
	{
		window.open(url);
	}
}
var remote_ecs_change = function ()
{
	var remote_ecs_dom = G('remote_ecs_dom');
	var remote_ip = G('remote_ip');
//	remote_ip.value = (remote_ecs_dom.value == '1' ) ? 'oss-internal.aliyuncs.com' : 'oss.aliyuncs.com';
}
</script>
<script src="View/js/backup_remote.js"></script>

<div id="body">
<?php include('ALiOSS_category.php'); ?>

<?php
	if (!empty($top_notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $top_notice . '</p></div>';
?>
<div id="ALiOSS_list">
<p>ALiOSS 远程备份服务:</p>
<table border="0" cellspacing="1"  id="STable" style="width:1080px;">
	<tr>
	<th>ID</th>
	<th>类型</th>
	<th>状态</th>
	<th>远程IP域名 <br /> Host</th>
	<th>保存位置 <br /> Bucket</th>
	<th>账号 <br /> Key-ID</th>
	<th>账号验证</th>
	<th>密码 / 密匙<br /> Key-Secret</th>
	<th>说明备注</th>
	<th>添加时间</th>
	<th>操作</th>
	</tr>
	<?php 
	if(!is_array($ALiOSS_list_data) || count($ALiOSS_list_data) < 1)
	{
	?>
		<tr><td colspan="11" style="padding:10px;">ALiOSS 暂无远程备份设置</td></tr>
	<?php	
	}
	else
	{
		$remote_pass_type_arr = array(
			'1'	=> '密码',
			'2' => '<font color="green">密匙</font>',
			'3' => '<i>无</i>'
		);
		foreach ($ALiOSS_list_data as $key=>$val)
		{
	?>
			<tr>
			<th class="i"><?php echo $val['remote_id'];?></th>
			<td><?php echo $val['remote_type'];?></td>
			<td><?php echo $val['remote_status'] == '1' ? '<font color="green">已开启</font>' : '<font color="red">已关闭</font>';?></td>
			<td><?php echo $val['remote_ip'];?></td>
			<td><?php echo !empty($val['remote_path']) ? $val['remote_path'] : '<i>无</i>';?></td>
			<td><?php echo !empty($val['remote_user']) ? $val['remote_user'] : '<i>无</i>';?></td>
			<td><?php echo $remote_pass_type_arr[$val['remote_pass_type']];?></td>
			<td><?php echo $val['remote_pass_type'] != '3' ? '******' : '<i>无</i>';?></td>
			<td><?php echo !empty($val['remote_comment']) ? $val['remote_comment'] : '<i>无</i>';?></td>
			<td><?php echo $val['remote_time'];?></td>
			<td>
			<a href="index.php?c=ALiOSS&check=<?php echo $val['remote_id'];?>" class="button" onclick="return connect_check(this);"><span class="loop icon"></span>连接测试</a>
			<a href="index.php?c=ALiOSS&edit=<?php echo $val['remote_id'];?>" class="button"><span class="pen icon"></span>编辑</a>
			<a href="index.php?c=ALiOSS&del=<?php echo $val['remote_id'];?>" class="button" onclick="return confirm('确认删除ALiOSS远程备份设置ID:<?php echo $val['remote_id'];?>?');"><span class="cross icon"></span>删除</a>
			</td>
			</tr>
	<?php
		}
	}
	?>
</table>
<button type="button" class="primary button" onclick="WindowLocation('/index.php?c=ALiOSS')"><span class="home icon"></span> 返回列表</button>
<button type="button" class="primary button" onclick="WindowOpen('/index.php?c=backup&a=backup_list&category=backup_remote')">查看所有远程设置</button>

<br /><br />

<?php
	if (!empty($notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $notice . '</p></div>';
?>

<p>
<?php echo isset($edit_remote) ? '编辑' : '新增';?>ALiOSS远程备份设置:<?php echo isset($edit_remote) ? 'ID' . $_POST['remote_id'] : '';?>
</p>
<form action="index.php?c=ALiOSS" method="POST"  id="remote_edit" />
<table border="0" cellspacing="1"  id="STable" style="width:700px;">
	<tr>
	<th> &nbsp; </th>
	<th>值</th>
	<th>说明 </th>
	</tr>

	<tr><td>是否启用	</td>
	<td>
	<select id="remote_status_dom" name="remote_status">
	<option value="1">开启</option>
	<option value="2">关闭</option>
	</select>
	<?php if(isset($_POST['remote_status'])) {?>
	<script>G('remote_status_dom').value = '<?php echo $_POST['remote_status'];?>';</script>
	<?php }?>
	</td>
	<td><p> &nbsp; <font class="red">*</font> 是否启用</p></td>
	</tr>

	<tr><td>Host</td>
	<td><input type="text" id="remote_ip" name="remote_ip" class="input_text" /></td>
	<td><p> &nbsp; <font class="red">*</font> ALiOSS请求Host地址</p></td>
	</tr>

	<tr><td>Bucket</td>
	<td><input type="text" name="remote_path" class="input_text" value="<?php echo $_POST['remote_path'];?>" /></td>
	<td><p> &nbsp; <font class="red">*</font> ALiOSS Bucket存储空间</p></td>
	</tr>

	<tr><td>Key-ID</td>
	<td><input type="text" name="remote_user" class="input_text" value="<?php echo $_POST['remote_user'];?>" /></td>
	<td><p> &nbsp; <font class="red">*</font> 填写Access Key ID</p></td>
	</tr>

	<tr><td>Key-Secret</td>
	<td>
		<input id="remote_password" type="password" class="input_text" name="remote_password"  />
	</td>
	<td>
		<?php if (!isset($edit_remote)) { ?>
			<p> &nbsp; <font class="red">*</font> 填写Access Key Secret</p> 
		<?php } else {?> 
			<p> &nbsp; Access Key Secret留空将不做更改</p>
		 <?php }?>
	</td>
	</tr>

	<tr><td>说明备注	</td>
	<td><input type="text" name="remote_comment" class="input_text" value="<?php echo $_POST['remote_comment'];?>" /></td>
	<td><p> &nbsp;  添加说明备注</p></td>
	</tr>
	
</table>

<?php if (isset($edit_remote)) { ?>
	<input type="hidden" name="save_edit" value="<?php echo $_POST['remote_id'];?>" />
<?php } else { ?>
	<input type="hidden" name="save" value="y" />
<?php }?>

<button type="submit" class="primary button" name="submit"><span class="check icon"></span>保存</button> 
</form>

<div id="notice_message" style="width:850px;">
<h3>» ALiOSS 远程备份</h3>
1) 阿里云ECS服务器，Host地址使用oss-internal.aliyuncs.com即可在内网数据传输(快速与免流量费用)，非阿里云服务器请使用oss.aliyuncs.com。<br />
2) ALiOSS账号Key-ID、Key-Secret验证数据请登录 http://i.aliyun.com/access_key/ 取得。<br />
3) 如果多个Bucket存储空间都为同一阿里云账号，尽量使用同一Access Key，有效减少API请求次数，提高响应速度。<br />
4) 建议您建立一独立私有读写权限的Bucket存储空间，为AMH远程备份专用，数据访问权限安全与方便管理维护。<br />
5) 设置完成点击连接测试进行检测，可测试配置是否能正常连接。<br />
6) 系统www用户主目录/home/www不可删除，删除将会影响数据传输。<br />

<h3>» SSH ALiOSS远程备份</h3>
<ul>
<li>查看所有Bucket列表: amh module ALiOSS-1.1 admin gs,[ID] </li>
<li>查看Bucket读写权限: amh module ALiOSS-1.1 admin gs_acl,[ID] </li>
<li>查看Bucket存储对象列表: amh module ALiOSS-1.1 admin ls,[ID]</li>
<li>删除Bucket存储空间: amh module ALiOSS-1.1 admin rm-all,[ID]</li>
<li>获得Object下载地址: amh module ALiOSS-1.1 admin url,[ID],[ALiOSS-File]</li>
<li>下载Object到备份目录: amh module ALiOSS-1.1 admin get,[ID],[ALiOSS-File]</li>
<li>删除Object文件: amh module ALiOSS-1.1 admin rm,[ID],[ALiOSS-File]</li>
<li>ALiOSS所有远程设置连接数据传输: amh module ALiOSS-1.1 admin post,AMH-BackupFile-name  </li>
</ul>

</div>

</div>

</div>
<?php include('footer.php'); ?>


