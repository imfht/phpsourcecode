<h2 class="title"><?php echo $pageInfo['submenuName'] ?></h2>
<form name="form1" enctype="multipart/form-data" method="post" action="?p=<?php echo $request['p'] ?>&a=create">
  <table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
    <tr class="adtbtitle">
      <td width="892"><h3>创建视频：</h3><a href="javascript:history.back(1)" class="creatbt" class="creatbt">返回</a></td>
      <td width="72"><div align="right">
          <input name="submit" type="submit" value=" 保存 " class="savebt" />
        </div></td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="#FFFFFF"><table width="96%" border="0" align="center" cellpadding="0" cellspacing="4">
          <tr>
            <td width="80">标题</td>
            <td width="838"><input type="text" class="txt" name="title" id="title" size="60">
              给您上传的视频起个名字 </td>
          </tr>
          <tr>
            <td width="80">视频文件</td>
            <td><input name="filePath" id="filePath" type="text" class="txt" size="60">
              <input name="uploadfile" id="uploadfile" type="file" size="50" maxlength= "50" style="display: none;">
              <input type="button" name="bt1" id="bt1" value="本地上传" class="bluebutton" onclick="document.getElementById('filePath').disabled=true;document.getElementById('uploadfile').disabled=false;document.getElementById('uploadfile').style.display='block';document.getElementById('filePath').style.display='none';document.getElementById('bt1').style.display='none';document.getElementById('picTips').style.display='none';">
              <span id="picTips">[可直接贴入在线视频，例：http://www.doccms.com/doccms.flv]</span></td>
          </tr>
          <tr>
            <td width="80">视频缩略图</td>
            <td><input name="picture" id="picture" type="text" class="txt" size="60">
              <input name="uploadfile2" id="uploadfile2" type="file" size="50" maxlength= "50" style="display: none;">
              <input type="button" name="bt2" id="bt2" value="本地上传" class="bluebutton" onclick="document.getElementById('picture').disabled=true;document.getElementById('uploadfile2').disabled=false;document.getElementById('uploadfile2').style.display='block';document.getElementById('picture').style.display='none';document.getElementById('bt2').style.display='none';document.getElementById('picTips2').style.display='none';">
              <span id="picTips2">[可直接贴入在线图片，例：http://www.doccms.com/logo.jpg]</span></td>
          </tr>
          <tr>
            <td colspan="2"><a href="javascript:showHide('field_pane_on_2')"><img src="images/expand.gif" border="0"> 填写关键词&摘要 </a><a href="http://www.doccms.com/seo/#guanjianzi" target="_blank"><img src="./images/help.gif" alt="不知道怎么写关键字？" border="0" /></a>
              <div id="field_pane_on_2" style="display: none; padding:0; margin:0;">
                <table width="100%" border="0" align="center" cellpadding="0">
                  <tr>
                    <td width="80"> 页面关键词：</td>
                    <td width="838"><textarea style='width:400px;' name='keywords' id='keywords' cols='90' rows='3'></textarea></td>
                  </tr>
                  <tr>
                    <td> 页面摘要：</td>
                    <td><textarea style='width:400px;' name='description' id='description' cols='90' rows='3'></textarea></td>
                  </tr>
                </table>
              </div></td>
          </tr>
          <tr>
            <td colspan="2">视频描述</td>
          </tr>
          <tr>
            <td colspan="2"><?php
			echo ewebeditor(EDITORSTYLE,'content',$video_item->content)
			?></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
