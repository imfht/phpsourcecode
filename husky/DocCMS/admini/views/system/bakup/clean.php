<style>
body { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; background-color:#FFFFFF; }
td, input, button { font-size: 12px; }
a { text-decoration: none; color: #000000; }
a:hover { color: #009286; text-decoration: underline; }
.px10 { font-size:10px; }
.f_red { color:red; }
th { font-weight: bold; font-size: 12px; background: #2B5CC5; color: white; height: 20px; }
td.tablerow { padding-right: 3px; background: #f1f3f5; line-height: 150%; }
td.tablerowhighlight { font-weight: bold; padding: 3px; background: #E4EDF9; line-height: 150%; }
.tableborder { margin:auto; border: 1px solid #2B5CC5; width: 700px; background: #ffffff; }
</style>
<script type="text/javascript">
function checkall(form) {
	for(var i = 0;i < form.elements.length; i++) {
		var e = form.elements[i];
		if (e.name != 'chkall' && e.disabled != true) {
			e.checked = form.chkall.checked;
		}
	}
}
</script>

<?php require(ABSPATH.'/admini/views/system/bakup/header.php') ?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#FFFFF0" align="center">
  <tr height="24" bgcolor="#E4EDF9">
    <td > 友情提示：（数清理据前，请先备份数据库，以防不时之需!）</td>
    <td ><a href="./index.php?m=system&s=bakup&a=aclean">自动清理</a></td>
  </tr>
</table>
<?php 
 	$arr=array(TB_PREFIX.'models_reg',TB_PREFIX.'models_set',TB_PREFIX.'user');//禁止清空的表
  	global $db;
 	$size = $bktables = $bkresults = $results= array();
	$k = 0;
	$totalsize = 0;
	$query = mysql_query("SHOW TABLES FROM ".DB_DBNAME);
	if($query)
	{
		while($r = mysql_fetch_row($query))
		{
			$tables[$k] = $r[0];
			$count1 = mysql_query("SELECT count(*) as number FROM $r[0] WHERE 1");
			$count = mysql_fetch_array($count1);
			$results[$k] = $count['number'];
			$bktables[$k] = $r[0];
			$bkresults[$k] = $count['number'];
			$q = mysql_query("SHOW TABLE STATUS FROM `".DB_DBNAME."` LIKE '".$r[0]."'");
			$s = mysql_fetch_array($q);
			$tabletype[$k]=$s['Engine']?'1':'0';
			$size[$k] = round($s['Data_length']/1024, 2);
			$totalsize += $size[$k];
			$k++;
		}
	}else
	{
		echo '空记录。';
	}
?>
<form method="post" name="myform" action="./index.php?m=system&s=bakup&a=uclean">
  <table width="100%">
    <tr bgcolor="#FFFFFF">
      <td width="20%" class="tablerowhighlight" align="center"><input name='chkall' type='checkbox' id='chkall' onclick='checkall(this.form)' value='check' >
        全选/反选 </td>
      <td width="40%" class="tablerowhighlight">数据库表</td>
      <td width="20%" class="tablerowhighlight">记录条数</td>
      <td width="20%" class="tablerowhighlight">大小 [共
        <?=$totalsize?>
        k]</td>
    </tr>
    <?php 
	if(is_array($bktables)){
		foreach($bktables as $k => $tablename){
?>
    <tr>
      <td class="tablerow"  align="center"><?php if(!in_array($tablename,$arr)){ ?>
        <input type="checkbox" name="tables[]" value="<?php echo $tablename?>" >
        <?php } ?>
        <input type="hidden" name="tabletype[]" value="<?php echo $tabletype[$k]?>" ></td>
      <td class="tablerow"><?php echo $tablename?></td>
      <td class="tablerow">&nbsp;<?php echo $bkresults[$k]?></td>
      <td class="tablerow">&nbsp;<?php echo $size[$k]?> K</td>
    </tr>
    <?php 
		}
	}
?>
    <tr>
      <td class="tablerow"  align="center" colspan="4"><input type="submit" name="dosubmit" value=" 开始清理 "></td>
    </tr>
  </table>
</form>
