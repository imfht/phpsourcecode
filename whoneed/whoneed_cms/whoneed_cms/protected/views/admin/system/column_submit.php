<form action="/admin/system/column_add_save" method="post" onsubmit="return validateCallback(this, navTabAjaxDone)">
  <div class="mypageFormContent" style="clear:both;margin-left:24px;padding:8px 0px;">
	<input type="hidden" name="navTabId" value="article_add" />
	<table>
	  <tr>
		<th style="width:120px;">游戏编号：</th>
		<td style="padding:8px;"><input type="text" name="number" size="30" class="required textInput" /></td>
	  </tr>

	  <tr>
		<th style="width:120px;">服务器编号：</th>
		<td style="padding:8px;">
		 <input type="text" name="servernumber" size="30" class="required textInput" />
		</td>
	  </tr> 
	  <tr>
		<th style="width:120px;">服务器名：</th>
		<td style="padding:8px;">
		 <input type="text" name="server" size="30" class="required textInput" />
		</td>
	  </tr>
	  <tr>
	  <th style="width:120px;">跳转方式：</th>
	   <td style="padding:8px;">
		<input type="radio" name="jump" value="0" checked>默认
		<input type="radio" name="jump" value="1" >第一种
		<input type="radio" name="jump" value="2">第二种
		</td>
		<td><font color=red>（只针对盛世三国）</font></td>
	  </tr>
	  <tr>
		<th style="width:120px;"></th>
		<td><div class="buttonActive">
			<div class="buttonContent">
			  <button type="submit">&nbsp;&nbsp;提交&nbsp;&nbsp;</button>
			</div>
		  </div></td>
	  </tr>
	</table>
  </div>
</form>