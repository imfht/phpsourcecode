<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a> → <a href="./index.php?m=system&s=flashoptions">Flash调用管理</a> → <a href="./index.php?m=system&s=flashoptions&a=edit_group&group_id=<?php echo $request['group_id'] ?>">修改Flash信息</a> → <a href="./index.php?m=system&s=flashoptions&a=edit&group_id=<?php echo $request['group_id']?>&n=<?php echo $request['n']?>">修改图片</a> </div>
<form name="form1" method="post" enctype="multipart/form-data" action="?m=system&s=flashoptions&a=edit&n=<?php echo $request['n']; ?>&group_id=<?php echo $request['group_id']; ?>&adType=<?php echo $request['adType']; ?>">
  <table width="100%" border="0" cellpadding="2" cellspacing="1"  class="admintb">
    <tr class="adtbtitle">
      <td width="892"><h3>修改图片信息</h3><a href="javascript:history.back(1)" class="creatbt">返回</a></td>
      <td width="72"><div align="right">
          <input name="submit" type="submit" value=" 保存 " class="savebt"/>
        </div></td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="#FFFFFF"><table width="96%" border="0" align="center" cellpadding="0" cellspacing="4">
          <tr>
            <td width="57">标题</td>
            <td><input type="text" class="txt" name="title" id="title" value="<?php echo $edit_item->title ?>" style="width:300px">
              出现顺序
              <input type="text" name="ordering" class="txt" id="ordering" value="<?php echo $edit_item->ordering ?>" style="width:50px"></td>
          </tr>
          <tr>
            <td width="57">链接URL</td>
            <td><input type="text" name="url" class="txt" id="url" value="<?php echo $edit_item->url ?>" style="width:60%"></td>
          </tr>
          <tr>
            <td width="57">图片地址</td>
            <td><input name="picpath" class="txt" type="text" value="<?php echo $edit_item->picpath ?>" id="picpath" style="width:60%">
              <input disabled name="uploadfile" type="file" style="display: none;width:60%">
              <input type="button" name="bt2" value="本地上传" class="bluebutton" onClick="picpath.disabled=true;uploadfile.disabled=false;uploadfile.style.display='';picpath.style.display='none';this.style.display='none'"></td>
          </tr>
          <tr>
            <td colspan="2">图片注释
              <p>
                <textarea name="description" rows="3" class="txt" id="description" style="width:96%"><?php echo $edit_item->description ?></textarea>
                <br />
                [如果您的图片上已经有了注释,这里也可以不填写] </p></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
