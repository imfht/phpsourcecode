<?php include('header.php'); ?>
<style>
#Bucket_list input.input_text {
	width: 292px;
}
#STable td.object_list {
	background:url("View/images/Listbj01.gif") repeat-x scroll left top white;
	padding:20px;
}
</style>
<script>
// Object列表
var get_Object_list = function (id)
{
	var object_list = G('object_list_' + id);
	var object_list_td = G('object_list_td_' + id);
	object_list.style.display = '';
	object_list_td.innerHTML = '<img src="View/images/loading.gif" /> Loading...';
	Ajax.get('/index.php?c=ALiOSS&a=Object_list&remote_id=' + id + '&tag=' + Math.random(),function (msg){
		object_list_td.innerHTML = msg;
		if(!amh_backup_list_ing) amh_backup_list();
	}, false, true);

	return false;
}
// Object关闭
var Object_close = function (id)
{
	var object_list = G('object_list_' + id);
	object_list.style.display = 'none';
}
// Object删除
var Object_delete = function (obj, notice, id)
{
	if(!confirm(notice))
		return false;

	obj.innerHTML = '<span class="cross icon"></span>删除中...';
	Ajax.get(obj.href + '&tag=' + Math.random(),function (msg){
		get_Object_list(id);
	}, false, true);
	return false;
}
// Object下载到备份目录
var Object_download_local = function (obj)
{
	var title = '下载中...';
	if(obj.title == title) return false;
	obj.title = title;
	Ajax.get(obj.href + '&tag=' + Math.random(),function (msg){
		obj.innerHTML = '<span class="downarrow icon"></span>下载中...(0.00%)';
		if(!amh_backup_list_ing) amh_backup_list();
	}, false, true);
	return false;
}

// 获取amh备份列表文件(实时进度检查)
var amh_backup_list_ing = false;
var amh_backup_list = function ()
{
	amh_backup_list_ing = true;
	Ajax.get('/index.php?c=ALiOSS&a=Object_list&amh_backup_list=y&tag=' + Math.random(),function (msg){
		var amh_list = eval('(' + msg + ')');
		for (var k in amh_list)
		{
			if(G(amh_list[k].md5))
			{
				var _obj = G(amh_list[k].md5);
				var sum_size = _obj.name;
				var local_amh_percent = (amh_list[k].size / sum_size * 100).toFixed(2);
				if(local_amh_percent < 99)
					_obj.innerHTML = '<span class="downarrow icon"></span>下载中...(' + (amh_list[k].size / sum_size * 100).toFixed(2) + '%)';
				else
				{
					_obj.innerHTML = '<span class="downarrow icon"></span>下载到备份目录';
					_obj.title = '';
				}
			}
		}
		setTimeout(function ()
		{
			amh_backup_list();
		}, 5000)
	}, false, true);
}
/*
// Object目录打开
var Object_dir = function (obj, id)
{
	var object_list_td = G('object_list_td_' + id);
	object_list_td.innerHTML = '<img src="View/images/loading.gif" /> Loading...';
	Ajax.get(obj.href + '&tag=' + Math.random(),function (msg){
		object_list_td.innerHTML = msg;
	}, false, true);
	return false;
}
*/
</script>
<div id="body">
<?php include('ALiOSS_category.php'); ?>

<?php
	if (!empty($top_notice)) echo '<div style="margin:5px 2px;width:500px;"><p id="' . $status . '">' . $top_notice . '</p></div>';
?>
<div id="Bucket_list">
<p>ALiOSS - Bucket 存储空间列表:</p>
<table border="0" cellspacing="1"  id="STable" style="width:980px;">
	<tr>
	<th width="18">ID</th>
	<th>所属Key-ID</th>
	<th>Bucket 名称</th>
	<th>读写权限</th>
	<th>添加时间</th>
	<th>访问地址</th>
	<th>操作</th>
	</tr>
	<?php 
	if(!is_array($Bucket_list_data) || count($Bucket_list_data) < 1)
	{
	?>
		<tr><td colspan="7" style="padding:10px;">ALiOSS 暂无Bucket存储空间</td></tr>
	<?php	
	}
	else
	{
		$i=0;
		foreach ($Bucket_list_data as $key=>$val)
		{
	?>
			<tr>
			<th class="i"><?php echo ++$i;?></th>
			<td><?php echo $val['remote_user'];?></td>
			<td><b><?php echo $val['Bucket_name'];?></b></td>
			<td><?php echo $val['acl'];?></td>
			<td><?php echo $val['Bucket_time'];?></td>
			<td><a href="http://<?php echo $val['Bucket_name'];?>.oss.aliyuncs.com" target="_blank"><?php echo $val['Bucket_name'];?>.oss.aliyuncs.com</a></td>
			<td>
			<a href="index.php?c=ALiOSS&a=Object_list&remote_id=<?php echo $val['remote_id'];?>" class="button" onclick="return get_Object_list(<?php echo $val['remote_id'];?>)"><span class="tag icon"></span>Object 列表</a>
			<a href="index.php?c=ALiOSS&a=Bucket_list&delete=<?php echo $val['remote_id'];?>&Bucket_name=<?php echo $val['Bucket_name'];?>" class="button" onclick="return confirm('所属<?php echo $val['Bucket_name'];?>的Object列表所有文件都将删除。\n确认删除ALiOSS Bucket存储空间：<?php echo $val['Bucket_name'];?>?');"><span class="cross icon"></span>删除</a>
			</td>
			</tr>
			<tr id="object_list_<?php echo $val['remote_id'];?>" style="display:none;">
			<td colspan="7" class="object_list" id="object_list_td_<?php echo $val['remote_id'];?>">
			</td>
			</tr>
	<?php
		}
	}
	?>
</table>
</div>

<div id="notice_message" style="width:660px;">
<h3>» Bucket 存储空间</h3>
1) amh类型的备份文件，可以在线下载到服务器。AMH面板备份目录/home/backup，备份列表可查看。<br />
1) amh文件“下载到备份目录”是提交到AMH的后台运行，离开当前网页不影响下载进度。<br />
2) 有公共读权限的Bucket空间，所有访客都可直接下载文件。(访问地址+文件名)<br />

</div>

</div>
<?php include('footer.php'); ?>


