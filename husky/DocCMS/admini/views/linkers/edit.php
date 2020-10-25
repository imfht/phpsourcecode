<script type="text/javascript">
$(document).ready(function(){
  pictureLinks();
  wordsLinks();
});
function pictureLinks()
{
	if($('input[name=links]').get(0).checked)
	{
		$('#logo_pics').show();
	}
}
function wordsLinks()
{
	if($('input[name=links]').get(1).checked)
	{
		$('#logo_pics').hide();
	}
}
</script>

<h2 class="title"><?php echo $pageInfo['submenuName'] ?></h2>
<form name="form1" enctype="multipart/form-data" method="post" action="?p=<?php echo $request['p'] ?>&a=edit&&n=<?php echo $request['n'] ?>">
  <table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
    <tr class="adtbtitle">
      <td width="892"><h3>友情链接页面：</h3><a href="javascript:history.back(1)" class="creatbt">返回</a></td>
      <td width="72"><div align="right">
          <input name="submit" type="submit" value=" 保存 "  class="savebt"/>
        </div></td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="#FFFFFF"><table width="96%" border="0" align="center" cellpadding="0" cellspacing="4">
          <tr>
            <td width="57">链接类型</td>
            <td colspan="2" valign="top"><?php if($linkers_item->links==0) {?>
              <input type="radio" checked="checked" value="0" id="links" onclick="javascript:pictureLinks();" name="links" />
              图片链接
              <input type="radio" value="1" id="links" onclick="javascript:wordsLinks();" name="links" />
              文字链接
              <?php }elseif($linkers_item->links==1){?>
              <input type="radio" value="0" id="links" onclick="javascript:pictureLinks();" name="links" />
              图片链接
              <input type="radio" value="1" checked="checked" id="links" onclick="javascript:wordsLinks();" name="links" />
              文字链接
              <?php }?></td>
          </tr>
          <tr>
            <td width="57">友情链接名称</td>
            <td width="861"><input type="text" class="txt" name="title" id="title" value="<?php echo $linkers_item->title ?>" style="width:30%"></td>
          </tr>
          <tr>
            <td width="57">链接地址</td>
            <td><input type="text" class="txt" name="linkAddress" id="linkAddress" style="width:30%" value="<?php echo $linkers_item->linkAddress ?>">
              (例如：http://www.doccms.com/ — 不要忘记输入http://)</td>
          </tr>
          <tr id="logo_pics">
            <td width="57">网站LOGO</td>
            <td><input name="originalPic" class="txt" type="text"  style="width:50%" value="<?php echo $linkers_item->originalPic ?>">
              <input disabled name="uploadfile" type="file" style="display: none;width:50%">
              <input type="button" name="bt2" value="本地上传" class="bluebutton" onclick="originalPic.disabled=true;uploadfile.disabled=false;uploadfile.style.display='';originalPic.style.display='none';this.style.display='none'"></td>
          </tr>
          <tr>
            <td  width="57">友情链接描述</td>
            <td><?php
			echo ewebeditor(EDITORSTYLE,'description',$linkers_item->description);
		  ?></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>