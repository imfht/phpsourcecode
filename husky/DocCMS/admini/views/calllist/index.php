<script language="JavaScript">
<!--
function moveOption(e1, e2){
	try{
		for(var i=0;i<e1.options.length;i++){
			if(e1.options[i].selected){
				var e = e1.options[i];
				e2.options.add(new Option(e.text, e.value));
				e1.remove(i);
				i=i-1
			}
		}
		document.myform.calllist.value=getvalue(document.myform.list2);
	}
	catch(e){}
}
function getvalue(geto){
	var allvalue = "";
	for(var i=0;i<geto.options.length;i++){
		allvalue +=geto.options[i].value + ",";
	}
	return allvalue;
}
window.onload=function(){
	document.myform.calllist.value=getvalue(document.myform.list2);
}
//-->
</script>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb"> 
<form method="post" name="myform" action="./index.php?p=<?php echo $request['p'] ?>&a=calllist">
  <tr class="adtbtitle">
    <td><h3>列表管理：</h3></td>
    <td align="right"><input type="submit" name="submit" value="保存列表调用" class="savebt" />&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">	
	<table width="70%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="45%" style="border:solid #ddd 1px; padding:5px;">
		<span>[*系统内可被调用的列表模块*]</span>
		</td>
        <td align="center">&nbsp;
		
		</td>
        <td width="45%" style="border:solid #ddd 1px; padding:5px;">
		<span>[*您已选中调用的列表模块*]</span>
		</td>
      </tr>
      <tr>
        <td width="45%">
		<select multiple name="list1" style="height:200px; width:100%; font-size:14px; padding:5px 0; font-weight:bold;" size="12" ondblclick="moveOption(document.myform.list1, document.myform.list2)">
		<?php
		if(!empty($calllist))
		{
			foreach($calllist as $o)
			{
				?>		
				<option value="<?php echo $o->id?>"><?php echo $o->title?></option>		
				<?php 
			}
		}
		?>	
		</select>
		</td>
        <td style="padding-left:14px;">
		<input type="button" value="添加 >>" class="creatsb" onClick="moveOption(document.myform.list1, document.myform.list2)" style=" margin-bottom:15px;">		
		<input type="button" value="<< 移除" class="creatsb" onClick="moveOption(document.myform.list2, document.myform.list1)">
		</td>
        <td width="45%">
		<select multiple name="list2" style="height:200px; width:100%; font-size:14px; padding:5px 0; font-weight:bold;" size="12" ondblclick="moveOption(document.myform.list2, document.myform.list1)">
		<?php
		if(!empty($selectcalllist))
		{
			foreach($selectcalllist as $o)
			{
				?>		
				<option value="<?php echo $o->id?>"><?php echo $o->title?></option>		
				<?php 
			}
		}
		?>	
		</select>
		<input name="calllist" type="hidden" />
		</td>
      </tr>
    </table>
	<p style="color:#FF6600; padding:15px 0;">[*友好提示：选定一项或多项然后点击添加或移除(按住shift或ctrl可以多选)，或在选择项上双击进行添加和移除。]</p>
	</td>
  </tr>
</form>
</table>