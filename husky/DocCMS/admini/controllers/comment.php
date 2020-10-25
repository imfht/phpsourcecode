<?php 
function auditingComment()
{
	global $db,$request;
	if(!empty($request['comment']))
	{
		$sql='UPDATE '.TB_PREFIX.'comment SET auditing = 1 WHERE id='.$request['comment'].' LIMIT 1';
		if($db->query($sql))
		{
			if(intval($request['n'])>0)
			redirect('./index.php?a=edit&p='.$request['p'].'&n='.$request['n'].'#adminiCommentPosition');
			else
			redirect('./index.php?p='.$request['p'].'#adminiCommentPosition');
		}
		else
		{
			echo '审核失败！';
		}
	}
}
function destroyComment()
{
	global $db,$request;
	if(!empty($request['comment']))
	{
		$sql='DELETE FROM '.TB_PREFIX.'comment WHERE id='.$request['comment'].' LIMIT 1';
		if($db->query($sql))
		{
			if(intval($request['n'])>0)
			redirect('./index.php?a=edit&p='.$request['p'].'&n='.$request['n'].'#adminiCommentPosition');
			else
			redirect('./index.php?p='.$request['p'].'#adminiCommentPosition');
		}
		else
		{
			echo '删除失败！';
		}
	}
}
function auditingAllComment()
{
	global $db,$request;	
	
	if(!empty($request['comment_operation_all']))
	{
		$comment_operation_all = $request['comment_operation_all'];
		foreach($comment_operation_all as $o)
		{
			if(empty($o))$o=0;
			$sql ='UPDATE '.TB_PREFIX.'comment SET auditing=1 WHERE id='.$o;
			$db->query($sql);
		}
		if(intval($request['n'])>0)
		redirect('./index.php?a=edit&p='.$request['p'].'&n='.$request['n'].'#adminiCommentPosition');
		else
		redirect('./index.php?p='.$request['p'].'&n='.$request['n'].'#adminiCommentPosition');
	}
}
function destroyAllComment()
{
	global $db,$request;	
	
	if(!empty($request['comment_operation_all']))
	{
		$comment_operation_all = $request['comment_operation_all'];
		foreach($comment_operation_all as $o)
		{
			if(empty($o))$o=0;
			$sql ='DELETE FROM '.TB_PREFIX.'comment WHERE id='.$o;
			$db->query($sql);
		}
		if(intval($request['n'])>0)
		redirect('./index.php?a=edit&p='.$request['p'].'&n='.$request['n'].'#adminiCommentPosition');
		else
		redirect('./index.php?p='.$request['p'].'#adminiCommentPosition');
	}
}
?>
