<style type="text/css">
<!--
.change{ padding-left:4%;}
dl{margin:0 0 10px 0; padding:10px 0;}
dd{margin:0; padding:0;}
dd img{ border:1px solid #f1f1f1; padding:0; clear:both; }
.u_box {
	float: left;
	width: 230px;
	margin-right:10px;
	padding-left:10px;
	background-color:#FFFFCC;
	border:1px solid #ff0;
}

.skinbox{width: 230px; margin-right:10px; padding-left:10px; border:1px solid #CCC; background-color:#F9F9F9; float:left;}
.skinbox dd,.u_box dd{ line-height:24px;}
.skinbox dd a,.u_box dd a{ line-height:12px; margin:10px 15px 0 0;}
/*img { border:none;}*/
-->
</style>
<script type="text/javascript">
function openiframe(i){
	if($(".labelmanager").length>0){
		$("#labeliframe").removeClass("labelmanager");
		i.innerHTML="点击关闭标签管理&nbsp;&nbsp;︽";
	}
	else
	{
		$("#labeliframe").addClass("labelmanager");
		i.innerHTML="点击打开标签管理&nbsp;&nbsp;︾";
	}
}
</script>
<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a> → <a href="./index.php?m=system&s=changeskin">模板管理</a></div>
<a name="tagEdit"></a>
<table width="100%" border="0" cellpadding="2" cellspacing="0" class='tableBorder'>
  <tr>
    <td class='tableHeading'>标签可视化管理</td>
  </tr>
</table>
<div id="laberdiv"> <a href="#tagEdit" style=" position:absolute; top:10px; right:15px; z-index:1000;" class="clkopen" onclick="openiframe(this)">点击展开标签可视化管理&nbsp;&nbsp;︾</a>
  <div class="labelmanager" id="labeliframe">
    <iframe src="../inc/lable/lable.php" width="100%" height="800" id="doc_frames"></iframe>
  </div>
</div>
<div class="clear"></div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class='tableBorder'>
  <tr>
    <td class='tableHeading'>选择并管理模板</td>
  </tr>
</table>
<div class="box change">
  <table width="100%" border="0"  cellpadding="0" cellspacing="0">
    <tr>
      <td><?php get_skins_info(get_directory("../skins/")); ?>
      </td>
    </tr>
  </table>
</div>
<table width="100%" border="0" cellpadding="2" cellspacing="0" class='tableBorder'>
  <tr>
    <td class='tableHeading'>在线安装模板包</td>
  </tr>
</table>
<div class="box">
  <form id="form1" name="form1" enctype="multipart/form-data" method="post" action="?m=system&s=changeskin&a=upload_template">
    <table width="100%" border="0" align="center" cellpadding="4" cellspacing="0">
      <tr>
        <td width="10%">上传模板</td>
        <td width="90%"><input name="upfile" type="file" id="upfile" style="width:300px" />
          <input type="submit" name="Submit" value="上传并且安装" />
          (zip格式) </td>
      </tr>
      <tr>
        <td>相关资源</td>
        <td><a href="http://www.doccms.net/forum.php?gid=46" target="_blank">下载最新模板</a> | <a href="http://www.doccms.net/forum-49-1.html" target="_blank">模板制作教程</a></td>
      </tr>
    </table>
  </form>
</div>