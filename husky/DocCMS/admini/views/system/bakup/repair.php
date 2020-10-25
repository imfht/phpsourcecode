<?php require(ABSPATH.'/admini/views/system/bakup/header.php') ?>
<script type="text/javascript">
<!--
function check(){
	if(document.getElementById('deleteTip').checked)
	{
		if(confirm('您确认要删除此数据库备份?一旦删除，将不可恢复。'))
			return true;
		else
			return false;
	}
}
</script>
<form method="post" name="myform" action="?m=system&amp;s=bakup&a=repair" onSubmit="return check()">
<table width="100%">
<tr align="center">
<td width="20%" class="tablerowhighlight">选中</td>
<td width="80%" class="tablerowhighlight">数据表名</td>
</tr>
<?php 
	global $db;
	$tables = array();
	$query = mysql_query("SHOW TABLES FROM ".DB_DBNAME);
	if($query)
	{
		while($r = mysql_fetch_row($query))
		{
			$tables[] = $r[0];
		}
	}
	if(is_array($tables)&&count($tables)>0){
		foreach($tables as $id => $table){
?>
  <tr>
    <td class="tablerow" align="center"><input type="checkbox" name="tables[]" value="<?php echo $table?>"></td>
    <td class="tablerow"><?php echo $table?></td>
</tr>
<?php 
	}
}
else
echo '<tr><td colspan="2" align="center" style=" color:#FF0000;">数据库中暂时无表</td></tr>';
?>
  <tr>
    <td class="tablerow" align="center"><input name='chkall' type='checkbox' id='chkall' onclick='checkall(this.form)' value='check'>全选/反选</td>
      <td class="tablerow">
      <input type="radio" name="operation" value="repair">修复表&nbsp;&nbsp;
      <input type="radio" name="operation" value="optimize" checked>优化表&nbsp;&nbsp;
	  <input type="radio" name="operation" value="drop" id="deleteTip">删除表&nbsp;&nbsp;
	  <input type="submit" name="dosubmit" value=" 提 交 "></td>
  </tr>
</table>
</form>