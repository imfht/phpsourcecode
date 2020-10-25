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
function send(ev)
 {
    var objPos = mousePosition(ev);
    messContent = "<div class='setbox'><form action='index.php?p=<?php echo $request['p'] ?>&a=setModels' method='post'><div class='setleft'><div id='setfield'><?php sys_push('',"<label>属性名称：</label><input name='fields[]' value='{name}' type='text' class='txttc'>")?></div><p class='saveline'><a href='javascript:createField();'>添加产品新属性</a> </p></div><div class='setright'><div id='settab'><?php sys_push('',"<label>选项卡名称：</label><input name='tabs[]' value='{name}' type='text' class='txttc'>",1)?></div><p class='saveline'><a href='javascript:createTab();'>添加产品详情新选项卡</a></p></div><p class='saveline'><input type='submit' value=' 保存设置 ' class='savest'></p></form></div>";
    showMessageBox('产品自定义字段设置', messContent, objPos, 350);
}
</script>
<script language="javascript" type="text/javascript" src="../inc/js/window_custom.js"></script>
<style type="text/css">
.txttc{width:90%; height:20px; padding:3px 0 3px 10px; border:1px solid #ddd; color:#666;}
</style>


<span id="test"></span>
<form name="form1" id="form1" action="./index.php?p=<?php echo $request['p'] ?>" method="post">
  <input type="hidden" name="keyword" id="keyword">
</form>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
  <form name="orderForm" method="POST" action="./index.php?a=ordering&p=<?php echo $request['p'] ?>">
    <tr class="adtbtitle">
      <td><h3>产品管理：</h3><a href="./index.php?a=manageorders&p=<?php echo $request['p'] ?>" class="creatbt">订单管理</a><a href="./index.php?a=create&p=<?php echo $request['p'] ?>" class="creatbt">添加产品</a>
        <input type="text" id="keyword1" class="prokeyword">
        <input type="button" value=" 搜 索 " onclick="go_search();" class="creatsb">
        <a href="javascript:send(0)" class="button orange">设置产品自定义字段</a>
        </td>
      <td align="right"><span id="msg" style="color:#FF0000"></span>
        <input type="submit" name="submit" value="保存排序" class="savebt" />
        &nbsp; </td>
    </tr>
    <tr>
      <td colspan="3" bgcolor="#FFFFFF"><div class="wrapL">
          <table width="100%" border="0">
            <?php	  
		 	 index_category(getMenu_info('id',true,$request['p']));
	        ?>
          </table>
        </div>
        <div class="wrapR"> <?php echo $product->render() ?> </div></td>
    </tr>
  </form>
</table>
