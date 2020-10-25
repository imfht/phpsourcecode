<?php require(ABSPATH.'/admini/views/system/bakup/header.php') ?>
 <form method="post" name="myform" action="?m=system&amp;s=bakup&a=export">
 <table width="100%">
  <tr align="center">
<td width="8%" class="tablerowhighlight">选中</td>
<td width="8%" class="tablerowhighlight">ID</td>
<td width="30%" class="tablerowhighlight">文件名</td>
<td width="10%" class="tablerowhighlight">文件大小</td>
<td width="20%" class="tablerowhighlight">备份时间</td>
<td width="8%" class="tablerowhighlight">卷号</td>
<td width="20%" class="tablerowhighlight">操作</td>
</tr>
  <?php 
	$sqlfiles = glob('../temp/data/*.sql');
	 if(is_array($sqlfiles))
	 {
		 $prepre = '';
		 $info = $infos = array();
		 foreach($sqlfiles as $id=>$sqlfile)
		 {
			 preg_match("/([a-z0-9_]+_[0-9]{8}_[0-9a-z]{6}_)([0-9]+)\.sql/i",basename($sqlfile),$num);
			 $info['filename'] = basename($sqlfile);//返回路径中的文件名部分
			 $info['filesize'] = round(filesize($sqlfile)/(1024), 2);
			 $info['maketime'] = date('Y-m-d H:i:s', filemtime($sqlfile));//取得文件修改时间
			 $info['pre'] = $num[1];
			 $info['number'] = $num[2];
			 if(!$id) $prebgcolor = '#E4EDF9';
			 if($info['pre'] == $prepre)
			 {
				 $info['bgcolor'] = $prebgcolor;
			 }
			 else
			 {
				 $info['bgcolor'] = $prebgcolor == '#E4EDF9' ? '#F1F3F5' : '#E4EDF9';
			 }
			 $prebgcolor = $info['bgcolor'];
			 $prepre = $info['pre'];
			 $infos[] = $info;
		 }
	 }
	if(is_array($infos)){
		foreach($infos as $id => $info){
	$id++;
?>
  <tr bgcolor="<?php echo $info['bgcolor']?>"  align="center">
    <td><input type="checkbox" name="filenames[]" value="<?php echo $info['filename']?>"></td>
    <td><?=$id?></td>
    <td class="px10" align="left">&nbsp;<a href="../temp/data/<?php echo $info['filename']?>"><?php echo $info['filename']?></a></td>
    <td class="px10"><?php echo $info['filesize']?> K</td>
	<td class="px10"><?php echo $info['maketime']?></td>
    <td class="px10"><?php echo $info['number']?></td>
    <td>
	<a href="?m=system&amp;s=bakup&a=import&pre=<?php echo $info['pre']?>&dosubmit=1">导入</a> | 
	<a href="?m=system&amp;s=bakup&a=delete&filenames=<?php echo $info['filename']?>" onclick="return confirm('您确认要删除此数据库备份?一旦删除，将不可恢复。')">删除</a> | 
	<a href="?m=system&amp;s=bakup&a=download&filename=<?php echo $info['filename']?>">下载</a>
	</td>
</tr>
<?php 
	}
}
?>
  <tr>
	<td colspan=7 valign="top" class="tablerow">&nbsp;<span style="color:red">注意:</span>背景色相同的文件为同一次备份的文件,导入时只需要点导入任意一个文件,程序会自动导入剩余文件</td>
  </tr>
  <tr>
    <td class="tablerow" align="center"><input name='chkall' type='checkbox' id='chkall' onclick='checkall(this.form)' value='check'></td>
	<td colspan=6 valign="top" class="tablerow">全选/反选 <input type="submit" name="submit2" value=" 删除选中的备份 " onclick="document.myform.action='?m=system&amp;s=bakup&a=delete'"></td>
  </tr>
</table>
</form>
<P></P>
<form name="upload" method="post" action="?m=system&amp;s=bakup&a=uploadsql" enctype="multipart/form-data">
<table width="100%" border="0" cellpadding="4" cellspacing="1" bgcolor="#C5EAF5">
  <tr>
  <td><span style="color:#FF0000;">上传数据库备份文件</span></td>
   <td height="30" align="center">
	        上传SQL文件：
             <input name="uploadfile" type="file" size="25" value="">
             <input type="hidden" name="max_file_size" value="2097152">
             <input type="submit" name="dosubmit" value=" 上传 ">
	</td>
  </tr>
  <tr>
	<td colspan=2 valign="top" class="tablerow">&nbsp;<span style="color:red">注意:</span>上传文件格式须为SQL格式，且命名格式为：数据库名称+下划线_+8位备份日期+下划线_+6位随机数+数据表备份卷号，如doccms_20120701_247512_1.sql。</td>
  </tr>
</table>
</form>