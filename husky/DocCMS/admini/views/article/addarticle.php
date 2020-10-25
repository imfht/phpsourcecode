<style type="text/css">
.c-box { float:left; }
.col2-lbox { width:20%; }
.col2-rbox { width:78.9%; }
</style>
<div class="c-box">
  <div class="col2-lbox">
    <?php require(ABSPATH.'/admini/views/article/nav.php') ?>
  </div>
  <div class="col2-rbox">
    <form name="form1" method="post"  enctype="multipart/form-data"  action="?a=newarticle&p=<?php echo $request['p'] ?>">
      <div class='box'>
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="4">
          <tr>
            <td width="60">页面标题</td>
            <td width="90%"><input type="text" name="title" id="title" class="txt" style="width:60%" />
              <a href="http://www.doccms.com/seo/#biaoti" target="_blank"><img src="./images/help.gif" border="0" alt="页面标题SEO中的作用" /></a>
              <input style="float:right;" name="submit" type="submit" value=" 保存 " class="savebt" /></td>
          </tr>
          <tr>
            <td colspan="2"><a href="javascript:showHide('field_pane_on_2')"><img src="images/expand.gif" border="0"> 填写关键词&摘要 </a><a href="http://www.doccms.com/seo/#guanjianzi" target="_blank"><img src="./images/help.gif" alt="不知道怎么写关键字？" border="0" /></a>
              <div id="field_pane_on_2" style="display: none; padding:0; margin:0;">
                <table width="100%" border="0" align="center" cellpadding="0">
                  <tr>
                    <td> 页面关键词：</td>
                    <td width="90%"><textarea style='width:400px;' name='keywords' id='keywords' cols='90' rows='3'><?php echo $article->keywords ?></textarea></td>
                  </tr>
                  <tr>
                    <td> 页面摘要：</td>
                    <td width="90%"><textarea style='width:400px;' name='description' id='description' cols='90' rows='3'><?php echo $article->description ?></textarea></td>
                  </tr>
                </table>
              </div></td>
          </tr>
          <tr>
            <td colspan="2" valign="top"><?php echo ewebeditor(EDITORSTYLE,'content'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><img src="./images/light.gif" alt="内容编辑小建议" border="0" /> 内容优化小建议：段落小标题请用h2,h3标签。文章主题关键词请使用strong,em标签加强语气！ <a href="http://www.doccms.com/seo/#neirong" target="_blank"><img src="./images/help.gif" alt="内容优化小常识" /></a></td>
          </tr>
        </table>
      </div>
      <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#C5EAF5">
        <tr>
          <td width="892"><a href="javascript:showHide('field_pane_on_3')"><img src="images/expand.gif" border="0"> 添加缩略图</a> | <a href="javascript:history.back(1)">返回</a> <a href="http://www.doccms.com/instruction-manual/" target="_blank"><img src="./images/help.gif" alt="关于缩略图" border="0" /></a></td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF"><table width="96%" border="0" align="center" cellpadding="0" cellspacing="4">
              <tr>
                <td colspan="2"><div id="field_pane_on_3" style="display: none; padding:0; margin:0;"> 上传缩略图：
                    <input name="originalPic" value="<?php echo $article->originalPic; ?>" class="txt" type="text"  style="width:50%">
                    <input disabled name="uploadfile" type="file" style="display: none;width:50%">
                    <input type="button" name="bt2" value="本地上传" class="bluebutton" onclick="originalPic.disabled=true;uploadfile.disabled=false;uploadfile.style.display='';originalPic.style.display='none';this.style.display='none'">
                    <input name="submit" type="submit" value=" 保存并上传 " />
                  </div></td>
              </tr>
            </table></td>
        </tr>
      </table>
    </form>
  </div>
</div>
