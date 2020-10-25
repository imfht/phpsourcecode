<?php $originalPic = explode('|',$picture->originalPic);$smallPic = explode('|',$picture->smallPic);?>
<script language="javascript" type="text/javascript">
var pic_num = <?php echo count($originalPic)?>;
function createPic()
{
	if(pic_num<20)
	{
		document.getElementById('addpic'+pic_num).style.display="block";
		pic_num++;
	}else
	{
		alert("不可上传超过20张图片！");
	}
}
function enlargePic(i)
{
	switch(i)
	{
		<?php for($i=0;$i<count($originalPic);$i++){?>
		case <?php echo $i?>:document.getElementById('demoPic').src='<?php echo ispic($smallPic[$i])?>';break;
		<?php }?>	
	}
}
</script>

<form name="form1" enctype="multipart/form-data" method="post" action="?a=edit&p=<?php echo $request['p'] ?>&n=<?php echo $request['n'] ?>">
  <table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
    <tr class="adtbtitle">
      <td width="892"><h3>修改图片：</h3><a href="javascript:history.back(1)" class="creatbt">返回</a></td>
      <td width="72"><div align="right">
          <input name="submit" type="submit" value=" 保存 " class="savebt" />
        </div></td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[&nbsp;<span style="color:#FF0000">温馨提示</span>：如要对该篇文章分页，请在您要分页地方加上  <span style="color:#0000FF">{#page#}</span> 即可&nbsp;]
        <table width="96%" border="0" align="center" cellpadding="0" cellspacing="4">
          <tr>
            <td width="100">图片预览</td>
            <td width="818" height="120"><img src="<?php echo ispic($smallPic[0])?>" id="demoPic"></td>
          </tr>
          <tr>
            <td width="57">标题</td>
            <td width="90%"><input name="title" type="text" id="title" size="60" value="<?php echo $picture->title ?>"></td>
          </tr>
          <tr>
            <td width="57">图片路径</td>
            <td><div class="picText">
                <div style="float:left;">
                  <input name="originalPic[0]" id="originalPic0" type="text"  value="<?php echo $originalPic[0]?>" size="60">
                  <input name="uploadfile[0]" id="uploadfile0" type="file" size="50" maxlength= "50" style="display: none;">
                  <input type="button" name="bt1" id="bt10" value="本地上传" class="bluebutton" onclick="document.getElementById('originalPic0').disabled=true;document.getElementById('uploadfile0').disabled=false;document.getElementById('uploadfile0').style.display='block';document.getElementById('originalPic0').style.display='none';document.getElementById('bt10').style.display='none';">
                </div>
                <div class="enlarge" onmousemove="enlargePic(0)">&nbsp;</div>
              </div>
              <span id="picTips">[可直接贴入网络图片，例：http://www.doccms.com/logo.jpg]</span></td>
          </tr>
          <tr>
            <td width="57"></td>
            <td><div id="newPic">
                <?php 		 
			for($i=1;$i<20;$i++)
			{
				?>
                <div id="addpic<?php echo $i?>" <?php if($i>=count($originalPic)) echo 'style="display:none;" '; ?>class="picText">
                  <div style="float:left;">
                    <input name="originalPic[<?php echo $i?>]" id="originalPic<?php echo $i?>" type="text"  value="<?php echo $originalPic[$i]?>" size="60">
                    <input name="uploadfile[<?php echo $i?>]" id="uploadfile<?php echo $i?>" type="file" size="50" maxlength= "50" style="display: none;">
                    <input type="button" name="bt1" id="bt1<?php echo $i?>" value="本地上传" class="bluebutton" onclick="document.getElementById('originalPic<?php echo $i?>').disabled=true;document.getElementById('uploadfile<?php echo $i?>').disabled=false;document.getElementById('uploadfile<?php echo $i?>').style.display='block';document.getElementById('originalPic<?php echo $i?>').style.display='none';document.getElementById('bt1<?php echo $i?>').style.display='none';">
                  </div>
                  <div class="enlarge" onmousemove="enlargePic(<?php echo $i?>)">&nbsp;</div>
                </div>
                <?php
			}
			?>
              </div></td>
          </tr>
          <tr>
            <td width="57"></td>
            <td height="30"><span class="newPic"><a href="javascript:void(0)" onclick="createPic()">添加新图片</a></span></td>
          </tr>
          <tr>
            <td colspan="2"><a href="javascript:showHide('field_pane_on_2')"><img src="images/expand.gif" border="0"> 填写关键词&摘要 </a><a href="http://www.doccms.com/seo/#guanjianzi" target="_blank"><img src="./images/help.gif" alt="不知道怎么写关键字？" border="0" /></a>
              <div id="field_pane_on_2" style="display: none; padding:0; margin:0;">
                <table width="100%" border="0" align="center" cellpadding="0">
                  <tr>
                    <td> 页面关键词：</td>
                    <td width="90%"><textarea style='width:400px;' name='keywords' id='keywords' cols='90' rows='3'><?php echo $picture->keywords ?></textarea></td>
                  </tr>
                  <tr>
                    <td> 页面摘要：</td>
                    <td width="90%"><textarea style='width:400px;' name='description' id='description' cols='90' rows='3'><?php echo $picture->description ?></textarea></td>
                  </tr>
                </table>
              </div></td>
          </tr>
          <tr>
            <td width="57">图片简介</td>
            <td><?php
			echo ewebeditor(EDITORSTYLE,'content',$picture->content);
		  ?></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
