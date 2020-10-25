<style type="text/css">
<!--
.wrapL{
width:19.8%; float:left;
}
.wrapR{
width:80%; float:left;
}
.menuText a{
text-decoration:none;
}
.menuText a:hover{
text-decoration:none;
}
-->
</style>
<script type="text/javascript">  
function checkNumber(e) 
{
	var key = window.event ? e.keyCode : e.which;
	var keychar = String.fromCharCode(key);
	var el = document.getElementById('test');
	var msg = document.getElementById('msg');
	reg = /\d/;
	var result = reg.test(keychar);
	if(!result)
	{         
		el.className = "warn";
		msg.innerHTML = "只能输入数字!&nbsp;&nbsp;";
		return false;
	}
	else 
	{         
		el.className = "";
		msg.innerHTML = ""; 
		return true;     
	} 
} 
</script>
<span id="test"></span>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb"> 
<form name="orderForm" method="post" action="./index.php?p=<?php echo $request['p'] ?>&a=<?php echo empty($request['c'])?"category_ordering":"ordering&c=".$request['c'];?>">
  <tr class="adtbtitle">
	<?php
	if(empty($request['c']))
	{
		?>
		<td><h3>投票管理：</h3><a href="?a=create_title&p=<?php echo $request['p'] ?>" class="creatbt">添加主题</a>
		
		</td>
		<?php
	}
	else
	{
		?>
		<td><h3>投票管理：</h3><a href="?a=create_title&p=<?php echo $request['p'] ?>" class="creatbt">添加主题</a><a href="?a=edit_title&p=<?php echo $request['p'] ?>&c=<?php echo $request['c'] ?>" class="creatbt">修改主题</a><a href="?a=destroy_title&p=<?php echo $request['p'] ?>&c=<?php echo $request['c'] ?>" class="creatbt" onclick="return confirm('您确认要删除该主题?一旦删除，将不可恢复。');">删除主题</a><a href="?a=create_choice&p=<?php echo $request['p'] ?>&c=<?php echo $request['c'] ?>" class="creatbt">添加选项</a>
		</td>
		<?php
	}
	?>
    <td align="right"><span id="msg" style="color:#FF0000"></span><input type="submit" name="submit" value="保存排序"  class="savebt"/>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" bgcolor="#FFFFFF">
	  <div class="wrapL">
		<table width="100%" border="0">
		<?php
		$adminCategoryTree = new adminCategoryTree($poll_category,$request['c']);
		?>
		</table>
		</div>	
		<div class="wrapR">
		<?php echo $poll->render() ?>
		</div>
	  </td>
  </tr>
</form>
</table>