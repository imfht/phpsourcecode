<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php">操作员后台</a> → <a href="./index.php?m=system&s=options">语言设置</a></div>
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#C5EAF5"> 
  <tr>
    <td width="892">语言设置|<a href="#">添加新语言</a></td>
    <td width="72"></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">
		<table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
		  <td colspan="3"><hr></td>
		</tr>
		<tr> 
		  <td width="200"><b>已有语种：</b></td> 
		  <td colspan="2"></td> 
		</tr> 
	    <tr> 
        <tr>
         <td colspan="2" bgcolor="#FFFFFF">
	   <?php 		
		$langList     = explode('@',QD_lang);
		$langNameList = explode('@',QD_lang_name);
		for($i=0;$i<count($langList)-1;$i++)
		{
			if(!empty($langNameList[$i])){?>
		  <a href="javascript:;"><?=$langNameList[$i]?></a>&nbsp;&nbsp;&nbsp;&nbsp;		
		<?php }
		}?> 
	      </td>
        </tr>
		<td width="200">
		
      </td> 
		<td colspan="2"></td> 
		</tr> 
        
		</table>
	</td>
  </tr>
</table>
<br /><br /><br />
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#C5EAF5"> 
  <tr>
    <td width="892">语言包标签设置|<a href="./index.php?m=system&s=lang&a=createTags">添加新标签</a></td>
    <td width="72"></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">
		<table width="96%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
		  <td colspan="3"><hr></td>
		</tr>
		<tr> 
		  <td width="200"><b>已有标签：</b></td> 
		  <td colspan="2"></td> 
		</tr> 
	    <tr> 
        <tr>
         <td colspan="2" bgcolor="#FFFFFF">
	   <?php 		
		$tagsList = explode('@',QD_lang_tags);

		for($i=0;$i<count($tagsList)-1;$i++)
		{
			if(!empty($tagsList[$i])){?>
		  <a href="./index.php?m=system&s=lang&a=editTags&tags=<?=$i?>"><?=$tagsList[$i]?></a>&nbsp;&nbsp;&nbsp;&nbsp;		
		<?php }
		}?></td>
        </tr>
		<td width="200">
		
      </td> 
		<td colspan="2"></td> 
		</tr>    
		</table>
	</td>
  </tr>
</table>