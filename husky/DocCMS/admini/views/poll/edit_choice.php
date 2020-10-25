<h2 class="title"><?php echo $pageInfo['submenuName'] ?></h2>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
 <form name="form1" enctype="multipart/form-data" method="post" action="./index.php?a=edit_choice&p=<?php echo $request['p'] ?>&c=<?php echo $request['c'] ?>&n=<?php echo $request['n'] ?>">
  <tr class="adtbtitle">
    <td width="892"><h3>当前主题：</h3><span class="creatbt"><?php echo $poll_category->title?></span> <a href="javascript:history.back(1)" class="creatbt">返回</a> </td>
    <td width="72"><div align="right">
      <input name="submit" type="submit" value=" 保存 " class="savebt"/>
    </div></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">
	  <table width="96%" border="0" align="center" cellpadding="0" cellspacing="4">	
        <tr>
          <td width="100">选项内容</td>
          <td width="818"><input name="choice" type="text" class="txt" value="<?php echo $poll->choice?>" size="60"></td>
        </tr>
		 <tr>
          <td width="100">选项票数</td>
          <td width="818"><input name="num" type="text" class="txt" value="<?php echo $poll->num?>" size="60"></td>
        </tr>		
        <tr>
          <td width="100">是否默认选中</td>
          <td width="818"><?php if ($poll->isdefault=="a") {?>
           <input name='isdefault' type='radio' value='a' checked>是
           <input type='radio' name='isdefault' value='b'>否
		   <?php } else {?>
           <input name='isdefault' type='radio' value='a'>是
           <input type='radio' name='isdefault' value='b' checked>否
		   <?php } ?></td>
        </tr>	
      </table>	
	  </td>
  </tr>
  </form>
</table>