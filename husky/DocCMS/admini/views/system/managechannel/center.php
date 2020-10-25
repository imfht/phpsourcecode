<script type="text/javascript">
function menu_move(to,action_to,id)
{
	if(to)
	{
		if(confirm("真的要移动吗?")) {
		 window.location.href=action_to+"&move_to="+to+"&id="+id; 		
		}
    }
}
</script>
<style type="text/css">
<!--
#menu { width:100%; height:auto; margin: 0 20px; }
#menu ul { margin:0; padding:0; width:98%; }
#menu ul li { list-style:none; line-height:40px; border-bottom:#F5F9FD 2px solid; padding-left:10px; clear:both }
#menu ul li:hover { background:#F5F9FD; }
#menu ul input { height:20px; background: #F9F9F9; border:solid 1px #aacfe4; border: 1px solid; color: #333; padding: 2px; resize: none; line-height:20px; margin-left:5px; }
#menu ul li b { float:left; display:block; color:#333; margin:0; }
#menu ul li font { color:#333; margin-right:25px; font-weight:600; font-size:12px; display:block; width:28px; float:left; }
#menu ul li select { width:160px; height:28px; color:#333; margin:10px 50px 0 0; float:right; padding-top:10px; border:solid 1px #aacfe4; padding: 4px 2px; }
#menu ul li select.msl { color:#333; margin-right:10px; float:right; margin-top:10px; }
#menu ul li .ms2 { color:#333; margin-right:90px; float:right; }
.order { width:40px; }
.c { background:#F5F9FD; }
.jt { background:url(../inc/img/webmap/arrow_r.gif) 0 2px no-repeat; color:#36f; font-weight:bold; padding-left:10px; }
.yl { background: url(./images/bg_repno.gif) no-repeat -240px -550px; color:#39f; padding: 0 0 0 50px; }
.el { background:url(../inc/img/webmap/tree_line1-1.gif) 20px no-repeat; color:#999; padding-left:43px; }
.ys { color:#999; }
-->
</style>
<span id="test"></span>
<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a> → <a href="./index.php?m=system&s=managechannel">导航及栏目设置</a></div>
<table width="100%" border="0" cellpadding="4" cellspacing="1" class="admintb">
  <form name="orderForm" method="post" action="./index.php?m=system&s=managechannel&a=update_menu">
    <tr class="adtbtitle">
      <td width="60%"><a href="./index.php?m=system&s=managechannel" class="creatbt">导航及栏目菜单管理</a>
        <h3>导航菜单控制中心</h3></td>
      <td width="30%"></td>
      <td align="right"><input TYPE="submit" name="submit" value=" 保 存 " class="savebt" />
        &nbsp;</td>
    </tr>
    <tr>
      <td colspan="3" bgcolor="#ffffff"><div id="menu">
          <ul>
            <li><b style="width:5%">ID</b><b style="width:10%">显示顺序</b><b style="width:40%">栏目名称</b><b style="width:15%">显示状态</b><b style="width:15%">栏目样式</b><b style="width:10%">操作(移动)</b></li>
            <?php menutree()?>
          </ul>
          <div class="clear"></div>
        </div></td>
    </tr>
  </form>
</table>
