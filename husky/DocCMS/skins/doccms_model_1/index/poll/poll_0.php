<form name="vote" method="post" action="<?php echo sys_href($poll_cateaory['channelId'],'poll_send',$poll_cateaory['id']);?>">
<table cellSpacing=0 cellPadding=0 width="100%" border=0>
<tbody>
<tr>
	<td align=left colSpan=2 height=20>&nbsp;&nbsp;<?php echo $poll_cateaory['title']; ?></td>
</tr>
<?php
if($results)
{
	foreach($results as $data)
	{
	?>
	<tr><td align=left colSpan=2 height=20><input type="radio" name="choice<?php echo $poll_cateaory['choice']=='a'?'':'[]'?>" value="<?php echo $data['id']; ?>" <?php echo $data['isdefault']=='a'?checked:'';?>><?php echo $data['choice']; ?></td></tr>
	<?php
	}
}
else
{
	echo '您的投票标题没有任何选项';
}
?>
<tr>
	<td align=middle height=30><input type="submit" value="投票"></td>
	<td align=middle><a href="<?php echo sys_href($poll_cateaory['channelId'],'poll',$poll_cateaory['id']);?>" target="_blank">查看投票</a></td>
</tr>
</tbody>
</table>
</form>