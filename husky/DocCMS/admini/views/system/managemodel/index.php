<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a> → <a href="./index.php?m=system&s=managemodel">模块管理</a></div>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
  <tr class="adtbtitle">
    <td><h3>模块管理</h3><a href="http://www.doccms.net/forum-58-1.html" class="creatbt">获取最新模块</a></td>
    <td width="91">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">
	  <?php echo $model_list->render() ?>	
	  </td>
  </tr>
</table>

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
  <tr class="adtbtitle">
    <td><h3>安装模块</h3></td>
  </tr>  
  <tr>
    <td bgcolor="#FFFFFF">
    <form id="form1" name="form1" enctype="multipart/form-data" method="post" action="?m=system&s=managemodel&a=upload_model">
    <table width="90%" border="0" align="left" cellpadding="4" cellspacing="0">
	  <tr>
	    <td width="10%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;安装模块</td>
	    <td width="90%"><input name="upfile" type="file" id="upfile" style="width:300px" /> 
	    	<input type="submit" name="Submit" value="上传并且安装" />(zip格式)  </td>
	  </tr>
	  <tr>
	    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;相关资源</td>
	    <td><a href="http://www.doccms.net/forum-58-1.html" target="_blank">下载最新模块</a> | <a href="http://www.doccms.net/forum-42-1.html" target="_blank">模快制作教程</a></td>
	  </tr>
    </table>
    </form>
    </td>
  </tr>
</table>
<iframe id="noname" src="../inc/models/index.php" width="0" height="0" frameborder="0" scrolling="no"></iframe>
<br />