<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php">操作员后台</a> → <a href="./index.php?m=system&s=options">语言设置</a></div>
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#C5EAF5"> 
<form name="form1" method="POST" action="./index.php?m=system&s=lang&a=editTags&tags=<?=$request['tags']?>">
  <tr>
    <td width="892">语言设置|<a href="./index.php?m=system&s=lang">返回</a></td>
    <td width="72"><input name="saveme" type="button" onclick="javascript:confirm('您确认要删除此标签?一旦删除，将不可恢复。')?location.href='./index.php?m=system&s=lang&a=deleteTags&tags=<?=$request['tags']?>':false;" value=" 删除此标签 " /></td>
    <td width="72"><input name="saveme" type="button" onclick="form1.submit()" value=" 保存设置 " /></td>
  </tr>
  <tr>
    <td colspan="3" bgcolor="#FFFFFF">
		<table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
		<td colspan="3"><hr></td>
		</tr>
		<tr height="50"> 
		<td colspan="3">将首页、上一页、下一页等常用词做成语言包标签,以方便模板调用。</td> 
		</tr> 
        <?php
		$langList     = explode('@',QD_lang);
		$langNameList = explode('@',QD_lang_name);
        $langTags     = explode('@',QD_lang_tags);	
		eval('$langTagser = explode(\'@\',QD_lang_tags_'.$request['tags'].');');
		?>
	    <tr> 
		<td width="200">
		 标签-中文标识：</td> 
		<td colspan="2"><input name="lang_cn" type="text" class="txt" id="lang_cn" value="<?php echo htmlspecialchars(stripslashes($langTags[$request['tags']])) ?>" size="41" /> <font color="#FF0000">(标识项必填)</font></td> 
		</tr>
        <tr> 
		<td width="200">
		 标签-英文标识：</td> 
		<td colspan="2"><input name="lang_en" type="text" class="txt" id="lang_en" value="<?php echo htmlspecialchars(stripslashes($langTagser['1'])) ?>" size="41" /> <font color="#FF0000">(标识项必填)</font></td> 
		</tr>  
        <?php
		for($i=0;$i<count($langList)-1;$i++)
		{
			if($langList[$i]!='cn' && $langList[$i]!='en' && !empty($langList[$i]))
			{?>
        <tr> 
		<td width="200">
		 标签-<?=$langNameList[$i]?>翻译：</td> 
		<td colspan="2"><input name="lang_<?=$langList[$i]?>" type="text" class="txt" id="lang_<?=$langList[$i]?>" value="<?php echo htmlspecialchars(stripslashes($langTagser[$i])) ?>" size="41" /> <font color="#FF0000">(标签的<?=$langNameList[$i]?>翻译)</font></td> 
		</tr>
        <?php }
		}?>
		</table>
	</td>
  </tr>
</form>
</table>