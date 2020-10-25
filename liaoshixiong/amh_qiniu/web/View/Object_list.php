<?php !defined('_Amysql') && exit; ?>
<style>
#STable td.object_name, #STable th.object_name {
	text-align:left;
	padding-left:22px;
}
#STable .Object_list {
	width:930px;
	background:none;
	border:1px solid #E7E7E7;
}
#STable .Object_list td {
border-bottom:1px solid #E0E0E0;
}
</style>

<table border="0" cellspacing="0"  id="STable" class="Object_list">
	<tr>
	<th class="object_name">文件名</th>
	<th>大小</th>
	<th>类型</th>
	<th>创建时间</th>
	<th>操作</th>
	</tr>
	<?php 
	if(!is_array($Object_list_data) || count($Object_list_data) < 1)
	{
	?>
		<tr><td colspan="6" style="padding:10px;">没有Object文件数据</td></tr>
	<?php	
	}
	else
	{
		$i=0;
		foreach ($Object_list_data as $key=>$val)
		{
	?>
			<tr>
			<td class="object_name" width="430">
			<img src="/View/images/ALiOSS/<?php echo $val['object_type'];?>.png" style="margin-bottom: -2px;"/> <?php echo $val['path'];?>
				<!-- <img src="/View/images/ALiOSS/dir.png" style="margin-bottom: -2px;"/> 
				<a href="index.php?c=ALiOSS&a=Object_list&remote_id=<?php echo $_GET['remote_id'];?>&path=<?php echo $val['basename'];?>&amh_token=<?php echo $_SESSION['amh_token'];?>" onclick="return Object_dir(this, <?php echo $_GET['remote_id'];?>)"><?php echo $val['basename'];?></a>
				--> 
			</td>
			<td><?php echo $val['size'];?></td>
			<td><?php echo $val['extension'];?></td>
			<td><?php echo $val['time'];?></td>
			<td style="text-align:left;">
			<?php if($val['object_type'] == 'file') {?>
				<?php if($val['extension'] == 'amh') {?>
					<a href="index.php?c=ALiOSS&a=Object_list&remote_id=<?php echo $_GET['remote_id'];?>&download_local=<?php echo urlencode($val['path']);?>&amh_token=<?php echo $_SESSION['amh_token'];?>" class="button" target="_blank" onclick="return Object_download_local(this);" id="<?php echo md5($val['basename']);?>" name="<?php echo $val['size_kb'];?>"><span class="downarrow icon"></span>下载到备份目录</a>
				<?php } ?>
				<a href="index.php?c=ALiOSS&a=Object_list&remote_id=<?php echo $_GET['remote_id'];?>&download=<?php echo urlencode($val['path']);?>&amh_token=<?php echo $_SESSION['amh_token'];?>" class="button" target="_blank"><span class="downarrow icon"></span>下载</a>
			<?php } ?>
			<a href="index.php?c=ALiOSS&a=Object_list&remote_id=<?php echo $_GET['remote_id'];?>&delete=<?php echo urlencode($val['path']);?>&amh_token=<?php echo $_SESSION['amh_token'];?>" class="button" onclick="return Object_delete(this, '确认删除ALiOSS<?php echo $val['object_type'] == 'file' ? '文件' : '文件夹';?>：<?php echo $val['basename'];?>?', <?php echo $_GET['remote_id'];?>);"><span class="cross icon"></span>删除</a>
			</td>
			</tr>
	<?php
		}
	}
	?>
</table>
<div style="text-align:right;margin:5px 15px;">
<input type="button" value="关闭" onclick="Object_close(<?php echo $_GET['remote_id'];?>)"/>
</div>