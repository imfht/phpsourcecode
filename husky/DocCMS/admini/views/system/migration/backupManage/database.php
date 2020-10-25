		<h1>数据库备份</h1>
		 <form method="post" name="myformdata" action="./index.php?m=system&s=migration&a=deleteXMLWebDatas">
		 <table width="100%">
		  <tr>
			  <td class="tablerow" align="center"><input type="submit" name="submit2" value="" class="delbtn"></td>
			  <td colspan=6 valign="top" class="tablerow"> </td>
			 </tr>
		  <tr align="center" id="appendFlag0">
			<td width="8%" class="tablerowhighlight"><input name="chkall" type="checkbox" id="chkall" onclick="checkall(this.form)" value="check"/></td>
			<td width="8%" class="tablerowhighlight">ID</td>
			<td width="30%" class="tablerowhighlight">文件名</td>
			<td width="10%" class="tablerowhighlight">文件大小</td>
			<td width="20%" class="tablerowhighlight">时间</td>
			<td width="20%" class="tablerowhighlight">操作</td>
			</tr>
			 <!-- 数据 -->
			</table>
		</form>
		<form name="uploaddata" method="post" action="./index.php?m=system&s=migration&a=uploadWebData" enctype="multipart/form-data">
			<table width="100%" border="0" cellpadding="4" cellspacing="1" bgcolor="#C5EAF5">
			  <tr>
			  <td></td>
			  <td width="500" height="30" align="left">
			       <input name="uploadfile" type="file" size="25" value="" />
			       <input type="hidden" name="max_file_size" value="<?php echo userUploadDataFileSize;?>"/>
			       <input type="submit" name="dosubmit" value="" class="uploadbtn"/>
			       <img src="./images/light.gif" alt="上传文件格式须为.xml格式" title="上传文件格式须为.zip格式" border="0" />
				</td>
			  </tr>
			</table>
		</form>