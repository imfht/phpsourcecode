<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a> → <a href="./index.php?m=system&s=userinfo">用户管理</a></div>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb"> 
  <tr class="adtbtitle">
    <td><h3>管理者</h3><a href="?m=system&s=userinfo&a=create" class="creatbt">添加管理者 </a></td>
    <td width="91">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">
	  <?php echo $users->render() ?>
	  </td>
  </tr>
</table>