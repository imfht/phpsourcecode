<style>
body { font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; background-color:#FFFFFF; }
td, input, button { font-size: 12px; }
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
<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system">操作员后台</a> → <a href="./index.php?m=system&s=bakup">数据库管理</a></div>
<table width="100%" border="0" cellpadding="4" cellspacing="1" class="admintb">
  <tr class="adtbtitle">
    <td width="50%"><h3>数据库管理</h3></td>
    <td width="40%"><a href="?m=system&s=bakup&a=export" class="creatbt"><strong><span>数据库备份</span></strong></a> <a href="?m=system&s=bakup&a=import" class="creatbt"><strong><span>数据库恢复</span></strong></a> <a href="?m=system&s=bakup&a=repair" class="creatbt"><strong>数据库管理</strong></a> <a href="?m=system&s=bakup&a=clean" class="creatbt"><strong><span>垃圾数据清理</span></strong></a></td>
    <td align="right">&nbsp;</td>
  </tr>
</table>
