<script type="text/javascript">  
// 说明 ： 用 Javascript 实现只能输入数字的文本框 
// 整理 ： 孙阳阳 20070715 
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
function go_search()
{
	document.getElementById('keyword').value = document.getElementById('keyword1').value;
	document.form1.submit();
}
</script>
<span id="test"></span>
<form name="form1" id="form1" action="./index.php?p=<?php echo $request['p'] ?>" method="post">
	<input type="hidden" name="keyword" id="keyword">
</form>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
<form name="orderForm" method="post" action="./index.php?p=<?php echo $request['p'] ?>&a=ordering">
  <tr class="adtbtitle">
    <td><h3>图片管理</h3><a href="?p=<?php echo $request['p'] ?>&a=create" class="creatbt">添加图片</a>
	<input type="text" id="keyword1" class="prokeyword"> <input type="button" value=" 搜 索 " onclick="go_search();" class="creatsb">

    </td>
    <td align="right"><span id="msg" style="color:#FF0000"></span><input type="submit" name="submit" value="保存排序" class="savebt" />&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">
	  <?php echo $picture->render() ?>
	  </td>
  </tr>
</form>
</table>