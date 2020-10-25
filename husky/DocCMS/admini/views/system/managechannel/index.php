<script type="text/javascript">
$(document).ready(function(){
	$("#tree li").mouseover(function (){
		$(this).css({background:"#FFFFCC"});//efefef
	});
	$("#tree li").mouseout(function (){
		$(this).css({background:"none"});
	});
});
</script>
<style>
<!--
ul#tree li ul { display:none; }
.tree { width:33%; }
.tree .title { background:url(../inc/img/tree/tree_file.gif) no-repeat 0 50%; padding-left:16px; }
.menuid { width:4%; }
.menuname { width:18%; }
.type { width:9%; }
.moding { width:28%; }
.mod { width:4%; }
.del { width:4%; }
.create { width:11%; }
.comment { width:9%; }
.commentoff { width:9%; }
.commenton { width:9%; }
.commentoff a { color:#000; }
.ordering { width:8%; margin-top:3px; text-align:right; }
.ordering input { height:14px;-moz-border-bottom-colors: none; -moz-border-left-colors: none; -moz-border-right-colors: none; -moz-border-top-colors: none; background-color: white; border-color: #CCCCCC #E2E2E2 #E2E2E2 #CCCCCC; border-image: none; border-style: solid; border-width: 1px; box-shadow: 1px 2px 3px #F0F0F0 inset; color: #666666; overflow: hidden; vertical-align: middle;}
.pxtxt{ width:40px; border:1px solid #ddd; color:#666; padding:3px 0 3px 10px;}
.check { width:5%; margin-top:3px; }
.check input { height:12px; }
#submit { width:50px; margin-left:126px; }
-->
</style>
<span id="test"></span>
<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a> → <a href="./index.php?m=system&s=managechannel">导航及栏目设置</a></div>
<table width="100%" border="0" cellpadding="4" cellspacing="1" class="admintb">
  <form name="orderForm" method="post" action="./index.php?m=system&s=managechannel&a=ordering&pid=<?php echo $request['cid'] ?>">
    <tr class="adtbtitle">
      <td width="30%"><h3>导航及栏目菜单管理</h3>
        <a href="./index.php?m=system&s=managechannel&a=center" class="creatbt">导航菜单控制中心</a></td>
      <td width="60%"><a href="?m=system&s=managechannel&a=create" class="creatbt"><strong><span >添加主导航菜单</span></strong></a> <a href="?m=system&s=managechannel&a=create&pid=<?php echo $request['cid'] ?>&cid=<?php echo $request['cid'] ?>&deep=0" class="creatbt"><strong><span>添加子栏目菜单</span></strong></a> <a href="?m=system&s=managechannel&a=edit&pid=<?php echo $request['cid'] ?>&cid=<?php echo $request['cid'] ?>" class="creatbt"><strong>修改菜单属性</strong></a> <a href="?m=system&s=managechannel&a=destroy&pid=0&cid=<?php echo $request['cid'] ?>" class="creatbt" onclick="return confirm('您确认要删除本频道?一旦删除，此栏目将不可恢复。')"><strong>删除当前主菜单</strong></a></td>
      <td align="right"><input type="submit" name="submit" value="保存排序" class="savebt" />
        &nbsp;</td>
    </tr>
    <tr>
      <td colspan="3" class="navtitle"><?php main_menu() ?></td>
    </tr>
    <?php if(!empty($tempstr)){?>
    <tr>
      <td colspan="3" bgcolor="#FFFFFF"><ul id="tree">
          <li><span class="tree">子栏目菜单标题</span><span class="menuid">ID</span><span class="menuname">英文名</span><span class="type">模块类型</span><span  class="moding">编辑</span><span  class="ordering">排序[升序]</span></li>
          <?php echo $tempstr;?>
        </ul></td>
    </tr>
    <?php } ?>
  </form>
</table>
