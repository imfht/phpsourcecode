<script language="javascript" type="text/javascript">
var pic_num = 1;
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
</script>

<h2 class="title"><?php echo $pageInfo['submenuName'] ?></h2>
<form name="form1" enctype="multipart/form-data" method="post" action="?p=<?php echo $request['p'] ?>&a=create">
  <table width="98%" border="0" cellpadding="2" cellspacing="1" class="admintb">
    <tr class="adtbtitle">
      <td width="892"><h3>创建新图片：</h3><a href="javascript:history.back(1)" class="creatbt">返回</a></td>
      <td width="72"><div align="right">
          <input name="submit" type="submit" value=" 保存 "  class="savebt"/>
        </div></td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[&nbsp;<span style="color:#FF0000">温馨提示</span>：如要对该篇文章分页，请在您要分页地方加上  <span style="color:#0000FF">{#page#}</span> 即可&nbsp;]
        <table width="96%" border="0" align="center" cellpadding="0" cellspacing="4">
          <tr>
            <td width="57">标题</td>
            <td width="90%"><input type="text" class="txt" name="title" id="title" size="60"></td>
          </tr>
          <tr>
            <td width="57">添加图片</td>
            <td><div class="picText">
                <div style="float:left;">
                  <input name="originalPic[0]" id="originalPic0" type="text"  value="" size="60">
                  <input name="uploadfile[0]" id="uploadfile0" type="file" size="50" maxlength= "50" style="display: none;">
                  <input type="button" name="bt1" id="bt10" value="本地上传" class="bluebutton" onclick="document.getElementById('originalPic0').disabled=true;document.getElementById('uploadfile0').disabled=false;document.getElementById('uploadfile0').style.display='block';document.getElementById('originalPic0').style.display='none';document.getElementById('bt10').style.display='none';">
                </div>
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
                <div id="addpic<?php echo $i?>" style="display:none;" class="picText">
                  <div style="float:left;">
                    <input name="originalPic[<?php echo $i?>]" id="originalPic<?php echo $i?>" type="text"  value="<?php echo $originalPic[$i]?>" size="60">
                    <input name="uploadfile[<?php echo $i?>]" id="uploadfile<?php echo $i?>" type="file" size="50" maxlength= "50" style="display: none;">
                    <input type="button" name="bt1" id="bt1<?php echo $i?>" value="本地上传" class="bluebutton" onclick="document.getElementById('originalPic<?php echo $i?>').disabled=true;document.getElementById('uploadfile<?php echo $i?>').disabled=false;document.getElementById('uploadfile<?php echo $i?>').style.display='block';document.getElementById('originalPic<?php echo $i?>').style.display='none';document.getElementById('bt1<?php echo $i?>').style.display='none';">
                  </div>
                </div>
                <?php
			}
			?>
              </div></td>
          </tr>
          <tr>
            <td width="57"></td>
            <td><span class="newPic"><a href="javascript:void(0)" onclick="createPic()">添加新图片</a></span></td>
          </tr>
          <tr>
            <td colspan="2"><a href="javascript:showHide('field_pane_on_2')"><img src="images/expand.gif" border="0"> 填写关键词&摘要 </a><a href="http://www.doccms.com/seo/#guanjianzi" target="_blank"><img src="./images/help.gif" alt="不知道怎么写关键字？" border="0" /></a>
              <div id="field_pane_on_2" style="display: none; padding:0; margin:0;">
                <table width="100%" border="0" align="center" cellpadding="0">
                  <tr>
                    <td> 页面关键词：</td>
                    <td width="90%"><textarea style='width:400px;' name='keywords' id='keywords' cols='90' rows='3'></textarea></td>
                  </tr>
                  <tr>
                    <td> 页面摘要：</td>
                    <td width="90%"><textarea style='width:400px;' name='description' id='description' cols='90' rows='3'></textarea></td>
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
