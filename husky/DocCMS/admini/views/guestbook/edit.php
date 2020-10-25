<h2 class="title"><?php echo $pageInfo['submenuName']; ?></h2>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
  <tr class="adtbtitle">
    <td><h3>回复留言：</h3>
      <a href="javascript:history.back(1)" class="creatbt">返回</a></td>
    <td width="91">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2">
	    <table width="100%" style="border: 1px solid #ccc;" border="0" bgcolor="f2f2f2">
          <tr height="30">
            <td>第<b><?php echo $request['i'] ?></b>条留言</td>
            <td><b>留言者：</b><?php echo $guestbook->name; ?>&nbsp;</td>
			<td><b>联系方式：</b><?php echo $guestbook->contact; ?>&nbsp;</td>
            <td><b>留言时间:</b><?php echo $guestbook->dtTime; ?>&nbsp;</td>
            <td><b>留言IP：</b><?php echo $guestbook->ip; ?>&nbsp;</td>
          </tr>
		  <?php sys_push($guestbook->custom,
		  '<tr height="30">
            <td colspan="5"><b>{name}：</b>{value}&nbsp;</td>
          </tr>')?>
          <tr height="30">
            <td colspan="4"><b>留言内容：</b><?php echo $guestbook->content;?>&nbsp;</td>
			<td colspan="1"><?php if($guestbook->auditing == "1"){ echo '<font color="Green">已审核</font>';}else{echo '<font color="Red">未审核</font>';}?></td>
          </tr>
		  <tr height="30">
		  	<td colspan="5"><b>回复内容：</b></td>
		  </tr>
		  <tr>
            <td colspan="5">
			  <form name="form1" id="form1" method="post" action="?a=edit&p=<?php echo $request['p']; ?>&n=<?php echo $request['n']; ?>">
				<textarea name="content1" style="width:90%;height:60px"><?php echo $guestbook->content1; ?></textarea>
				<div class="submit">
				  <input type="button" name="Submit" value="回复并通过审核" onClick="document.getElementById('form1').submit();">
				  <input type="button" name="bt2" value="返回" onclick="window.location='./index.php?p=<?php echo $request['p']; ?>'">
				</div>
			  </form>
			</td>
          </tr>
        </table>
	</td>
  </tr>
</table>
