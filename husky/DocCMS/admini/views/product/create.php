<script language="javascript" type="text/javascript">
var pic_num = 1;
function createPic()
{
	if(pic_num<6)
	{
		document.getElementById('addpic'+pic_num).style.display="block";
		pic_num++;
	}else
	{
		alert("不可上传超过6张图片！");
	}
}
</script>

<h2 class="title"><?php echo $pageInfo['submenuName'] ?></h2>
<form name="form1" enctype="multipart/form-data" method="post" action="./index.php?p=<?php echo $request['p'] ?>&c=<?php echo $request['c'];?>&a=create">
  <table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
    <tr class="adtbtitle">
      <td width="892"><h3>添加产品：</h3><a href="javascript:history.back(1)" class="creatbt">返回</a></td>
      <td width="72"><div align="right">
          <input name="submit" type="submit" value=" 保存 " class="savebt" />
        </div></td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="#FFFFFF">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[&nbsp;<span style="color:#FF0000">温馨提示</span>：如要对该篇文章分页，请在您要分页地方加上  <span style="color:#0000FF">{#page#}</span> 即可&nbsp;]
        <table width="96%" border="0" align="center" cellpadding="0" cellspacing="4">
          <tr>
            <td width="100">产品图片</td>
            <td width="818"><div class="picText">
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
			for($i=1;$i<6;$i++)
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
            <td width="100">产品名称</td>
            <td width="818"><input name="title" type="text" class="txt" value="" size="60"></td>
          </tr>
          <tr>
            <td width="100">市场价格</td>
            <td width="818"><input name="sellingPrice" type="text" class="txt" value="0" size="60">
              元</td>
          </tr>
          <tr>
            <td width="100">优惠价格</td>
            <td width="818"><input name="preferPrice" type="text" class="txt" value="0" size="60">
              元</td>
          </tr>
          <?php 
          $rs = $db->get_row('SELECT * FROM `'.TB_PREFIX.'models_set` WHERE channelId = '.getMenu_info('id',true,$request['p']));
		  if(!empty($rs))
		  {
			  $fields = explode('@',$rs->field);
		 	  for($i=0;$i<count($fields);$i++)
			  {
				  ?>
          <tr>
            <td width="100"><?php echo $fields[$i]?></td>
            <td width="818"><input name="spec[]" type="text" class="txt" size="40" value=""></td>
          </tr>  
                  <?php
			  }
		  }
		  ?>
          <tr>
            <td width="100">初始化点击数</td>
            <td width="818"><input name="counts" value="0" type="text" class="txt" size="8" />
              次 </td>
          </tr>
          <tr>
            <td width="100"></td>
            <td width="818"><a href="javascript:showHide('field_pane_on_3')"><img src="images/expand.gif" border="0"> 相关分类</a>
              <div id="field_pane_on_3" style="display: none; padding:0; margin:0;">
                <select name="category[]" multiple="multiple" style="width:200px; height:250px;;">
                <?php edit_category(getMenu_info('id',true,$request['p']),$product->categoryId);?>
              </select>
              可按Ctrl键进行多选（建议使用默认值） 
              </div></td>
          </tr>
          <tr>
            <td colspan="2"><a href="javascript:showHide('field_pane_on_2')"><img src="images/expand.gif" border="0"> 填写关键词&摘要 </a><a href="http://www.doccms.com/seo/#guanjianzi" target="_blank"><img src="./images/help.gif" alt="不知道怎么写关键字？" border="0" /></a>
              <div id="field_pane_on_2" style="display: none; padding:0; margin:0;">
                <table width="100%" border="0" align="center" cellpadding="0">
                  <tr>
                    <td width="100"> 页面关键词：</td>
                    <td width="818"><textarea style='width:400px;' name='keywords' id='keywords' cols='90' rows='3'></textarea></td>
                  </tr>
                  <tr>
                    <td> 页面摘要：</td>
                    <td><textarea style='width:400px;' name='description' id='description' cols='90' rows='3'></textarea></td>
                  </tr>
                </table>
              </div></td>
          </tr>
          <?php 
		  global $customs;
		  if($customs['field_tab'])
		  {
		  sys_push($product->content,'<tr>
            <td width="100">{name}</td>
            <td width="818">{value}</td>
          </tr>',1);
		  }else
		  {
			 echo '<tr>
            <td width="100">产品详情</td>
            <td width="818">'.ewebeditor(EDITORSTYLE,'content',$product->content).'</td>
          </tr>';
		  }
		  ?>
          <tr>
            <td width="100"></td>
            <td width="818">
              <img src="./images/light.gif" alt="产品优化小建议" border="0" /> 产品内容优化小建议：段落小标题请用h2,h3标签。文章主题关键词请使用strong,em标签加强语气！ <a href="http://www.doccms.com/seo/#neirong" target="_blank"><img src="./images/help.gif" alt="了解更多优化小知识" border="0" /></a></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
