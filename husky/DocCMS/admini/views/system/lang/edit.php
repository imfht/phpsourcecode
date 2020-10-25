<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php">操作员后台</a> → <a href="./index.php?m=system&s=options">语言设置</a></div>
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#C5EAF5"> 

<form name="form1" method="POST" action="./index.php?m=system&s=lang&a=edit&lang=<?=$_GET['lang']?>">
  <tr>
    <td width="892">语言设置|<a href="./index.php?m=system&s=lang">返回</a></td>
    <td width="72"><input name="saveme" type="button" onclick="javascript:location.href='./index.php?m=system&s=lang&a=createModel&lang=<?=$_GET['lang']?>'" value=" 生成模板 " /></td>
    <td width="72"><input name="saveme" type="button" onclick="javascript:confirm('您确认要删除该语言?一旦删除，将不可恢复。')?location.href='./index.php?m=system&s=lang&a=delete&lang=<?=$_GET['lang']?>':false;" value=" 删除此语言 " /></td>
    <td width="72"><input name="saveme" type="button" onclick="form1.submit()" value=" 保存设置 " /></td>
  </tr>
  <tr>
    <td colspan="4" bgcolor="#FFFFFF">
		<table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
		<td colspan="3"><hr></td>
		</tr>
		<tr> 
		<td width="200"></td> 
		<td colspan="2"></td> 
		</tr> 
	    <tr> 
		<td width="200">
        <?php get_lang_info()?>
		 语言名称：</td> 
		<td colspan="2"><input name="langName" type="text" class="txt" id="langName" value="<?php echo htmlspecialchars(stripslashes(get_lang_info(2))) ?>" size="41" /> <font color="#FF0000">(例如：中文、英文，此项必填)</font></td> 
		</tr> 
        <tr> 
		<td width="200">
		 语言英文简称：</td> 
		<td colspan="2"><input name="lang" type="text" class="txt" id="lang" value="<?php echo htmlspecialchars(stripslashes(get_lang_info(1))) ?>" size="41" /> <font color="#FF0000">(例如：cn、en ，此项必填，作为系统标识用)</font></td>
        
		</tr>
        <tr>
        <td width="200">
		 网站标题：</td> 
		<td colspan="2"><input name="langTitle" type="text" class="txt" id="langTitle" value="<?php echo htmlspecialchars(stripslashes(get_lang_info(3))) ?>" size="41" /> <font color="#FF0000"><a href="http://www.shenhoulong.com/seo/#biaoti" target="_blank"><img src="./images/help.gif" alt="点击了解如何优化网站标题？" border="0" /></font></td> 
		</tr>
        <tr>
        <td width="200">
		 站点摘要：</td> 
		<td colspan="2"><textarea name="langSummary" id="langSummary" class="txt" cols="27" rows="3"><?php echo htmlspecialchars(stripslashes(get_lang_info(4))) ?></textarea> <font color="#FF0000"><a href="http://www.shenhoulong.com/seo/#zhaiyao" target="_blank"><img src="./images/help.gif" alt="如何写站点摘要描述？" border="0" /></a></font></td> 
		</tr>       
		</table>
	</td>
  </tr>
</form>
</table>