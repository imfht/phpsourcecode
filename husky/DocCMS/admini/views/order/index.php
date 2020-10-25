<script language="javascript" type="text/javascript" src="../inc/js/window_custom.js"></script>
<script> 
function send(ev)
 {
    var objPos = mousePosition(ev);
    messContent = "<div class='setbox'><form action='index.php?p=<?php echo $request['p'] ?>&a=setModels' method='post'><div class='setleftod'><div id='setfield'><?php sys_push('',"<label>属性名称：</label><input name='fields[]' value='{name}' type='text' class='txttc'>")?></div><p class='addline'><a href='javascript:createField();'>添加自定义属性</a> </p></div><p class='saveline'><input type='submit' value=' 保存设置 '  class='savest'></p></form></div>";
    showMessageBox('表单自定义字段设置', messContent, objPos, 350);

}

</script>
<style type="text/css">
<!--
html, body { font-size:12px; margin:0px; height:100%; }
.mesWindow { border:#666 1px solid; background:#fff; }
.mesWindowTop { border-bottom:#eee 1px solid; margin-left:4px; padding:3px; font-weight:bold; text-align:left; font-size:12px; }
.mesWindowContent { margin:4px; font-size:12px; }
.mesWindow .close { height:15px; width:28px; border:none; cursor:pointer; text-decoration:underline; background:#fff }
.setright { width:260px; float:left}
.setboxod{ padding:20px 0 20px 0;}
.setboxod li { list-style:none; }
.wrapL { width:19.8%; float:left; }
.wrapR { width:80%; float:left; }
.menuText a { text-decoration:none; }
.menuText a:hover { text-decoration:none; }
#setfield{ padding:10px 30px 30px 30px;}
#settab{ padding:10px 25px 25px 25px;}
#setfield,#settab .txt{ clear:right;}
.txttc{width:83%; height:20px; padding:3px 0 3px 10px; border:1px solid #ddd; color:#666;}
#setfield,#settab label{ clear:left;}
-->
</style>
<h2 class="title"><?php echo $pageInfo['submenuName'] ?></h2>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
  <tr class="adtbtitle">
    <td><h3>表单管理：</h3><a href="javascript:send(0)" class="button orange">设置表单自定义字段</a></td>
    <td width="91">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">
	  <?php echo $order->render() ?>	
	  </td>
  </tr>
</table>