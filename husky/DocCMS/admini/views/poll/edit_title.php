<h2 class="title"><?php echo $pageInfo['submenuName'] ?></h2>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
 <form name="form1" method="post" action="./index.php?a=edit_title&p=<?php echo $request['p'] ?>&c=<?php echo $request['c'] ?>">
  <tr class="adtbtitle">
    <td width="892"><h3>修改投票主题：</h3><a href="javascript:history.back(1)" class="creatbt">返回</a> </td>
    <td width="72"><div align="right">
      <input name="submit" type="submit" value=" 保存 " class="savebt" />
    </div></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">
	  <table width="96%" border="0" align="center" cellpadding="0" cellspacing="4">
        <tr>
          <td width="100">标题内容</td>
          <td width="818"><input name="title" class="txt" value="<?php echo $poll_category->title ?>" type="text" size="60"></td>
        </tr>
        <tr>
          <td width="100">单选/多选</td>
          <td width="818"><?php if ($poll_category->choice=="a") {?>
           <input name='choice' type='radio' value='a' checked>单选
           <input type='radio' name='choice' value='b'>多选
		   <?php } else {?>
           <input name='choice' type='radio' value='a'>单选
           <input type='radio' name='choice' value='b' checked>多选
		   <?php } ?>
		</td>
        </tr>
      </table>	
	  </td>
  </tr>
  </form>
</table>