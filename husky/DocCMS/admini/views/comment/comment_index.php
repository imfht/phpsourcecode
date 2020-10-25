<a name="adminiCommentPosition" id="adminiCommentPosition"></a>
<script>
function checkAll()
{
	var m = document.getElementsByName('comment_operation_all[]');
	for ( var i=0; i< m.length ; i++ )
	{
		m[i].checked == true
			? m[i].checked = false
			: m[i].checked = true;
	}
}

function deleteAll()
{
	var m = document.getElementsByName('comment_operation_all[]');
	var l = m.length;
	for ( var i=0; i< l; i++)
	{
		m[i].checked == true 
			? m[i].checked = false
			: m[i].checked = true;
	}
}
</script>
<?php 
comment_index();
function comment_index()
{
	global $db,$request,$commentStr,$count,$comment_sb;
	
	if(empty($request['n']))$request['n']=0;
	if($request['s'] == 'newcomment')
	{
		$count = $db->get_var('SELECT count(*) FROM `'.TB_PREFIX.'comment` WHERE auditing=0 AND channelId='.$request['p'].' AND recordId='.$request['n']);
		if(!empty($request['comment_mdtp'])&&$request['comment_mdtp'] != '1')
		$count = $count - (intval($request['comment_mdtp']) - 1)*6;
		$comment_sb = new sqlbuilder('comment_mdt','SELECT * FROM `'.TB_PREFIX.'comment` WHERE auditing=0 AND channelId='.$request['p'].' AND recordId='.$request['n'],'id desc',$db,6,true,'./index.php','#adminiCommentPosition');
	}
	else
	{
		$count = $db->get_var('SELECT count(*) FROM `'.TB_PREFIX.'comment` WHERE channelId='.$request['p'].' AND recordId='.$request['n']);
		if(!empty($request['comment_mdtp'])&&$request['comment_mdtp'] != '1')
		$count = $count - (intval($request['comment_mdtp']) - 1)*6;
		$comment_sb = new sqlbuilder('comment_mdt','SELECT * FROM `'.TB_PREFIX.'comment` WHERE channelId='.$request['p'].' AND recordId='.$request['n'],'id desc',$db,6,true,'./index.php','#adminiCommentPosition');
	}
	?>
	<form name="commentForm" method="post">
	<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb" style="float:left;"> 
	  <tr class="adtbtitle">
		<td><h3>该内容评论管理：</h3><?php echo $comment_sb->get_pager_show() ?></td>
		<td align="right"><input type="button" name="button1" value="全选" onclick="checkAll()" /><input type="button" name="button2" value="反选" onclick="deleteAll()" />&nbsp;&nbsp;<input type="button" name="button3" value="审核所选" onclick="commentForm.action='./index.php?a=auditingAllComment&p=<?php echo $request['p'] ?>&n=<?php echo $request['n'] ?>';commentForm.submit();" /><input type="button" name="button4" value="删除所选" onclick="commentForm.action='./index.php?a=destroyAllComment&p=<?php echo $request['p'] ?>&n=<?php echo $request['n'] ?>';commentForm.submit();" />&nbsp;&nbsp;</td>
	  </tr>
	  <tr>
		<td colspan="2" bgcolor="#FFFFFF">
		  <?php
			if(isset($comment_sb->results))
			{
				foreach ($comment_sb->results as $o)
				{			
					?>
					<table style="border: 1px solid #ccc;<?php if ($count%2 ==0) {?> background-color:#C5EAF5;<?php }?>"  width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#FFFFCC">
					<tr>
					<td style="text-align:center; background-color:#D9D5B3; height:14px;width:80px;">第<strong><font color="#0000FF"><?php echo $count ?></font></strong>条评论</td>
					<td style="width:100px;"><strong>记录ID：</strong><?php echo $o['id'] ?></td>
					<td style="width:130px;"><strong>评论者：</strong><?php echo $o['name'] ?></td>
					<td style="width:230px;"><strong>评论时间:</strong><?php echo $o['dtTime'] ?></td>
					<td style="width:200px;"><strong>IP：</strong><?php echo $o['ip'] ?></td>
					<td style="width:130px;"><?php
					if($o['auditing'] == "1")
					{
						?>
					  <font color="Green">已审核</font>
					  <?php
					}
					else
					{
						?>
					  <font color="Red">未审核</font>
					  <?php
					}
					?></td>
					<td style="width:130px;"><input type="checkbox" name="comment_operation_all[]" value="<?php echo $o['id'] ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="bt33" value="审核" onclick="window.location='./index.php?a=auditingComment&p=<?php echo $request['p'] ?>&n=<?php echo $request['n'] ?>&comment=<?php echo $o['id'] ?>'" /><input type="button" name="bt32" value="删除" onclick="window.location='./index.php?a=destroyComment&p=<?php echo $request['p'] ?>&n=<?php echo $request['n'] ?>&comment=<?php echo $o['id'] ?>'" /></td>
					</tr>
					<tr>
					  <td style="text-align:center;height:25px;width:80px;<?php if ($count%2 ==0) {?> background-color:#D0FDF4;<?php } else {?> background-color:#E3E3E3;<?php }?>"><strong>评论内容</strong></td>
					  <td colspan="5" style="width:70%;<?php if ($count%2 ==0) {?> background-color:#D0FDF4;<?php } else {?> background-color:#E3E3E3;<?php }?>"><?php echo $o['content'];?></td>
					  <td style="width:130px;height:25px;<?php if ($count%2 ==0) {?> background-color:#D0FDF4;<?php } else {?> background-color:#E3E3E3;<?php }?>"><a href="mailto:<?php echo $o['email'] ?>">回复邮件</a></td>
					</tr>
		  			</table>					
					<?php
					$count--;
				}
			}
			else
			{
				?>
				<table><td>暂时还没有评论!</td></table>		
				<?php
			}
			?>
		</td>
	  </tr>	  
	</table>
	</form>
	<?php 
}
?>