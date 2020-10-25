<?php
function index()
{
	global $order,$db,$request,$customs;
	$sb = new sqlbuilder('zdt','SELECT * FROM `'.TB_PREFIX.'order` WHERE channelId='.$request['p'],'id desc',$db,20);
	$order = new DataTable($sb,'表单列表');
	$order->add_col('编号','id','db',40,'"$rs[id]"');
	$order->add_col('表单标题','title','db',0,'"<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">$rs[title]</a>"');

	$order->add_col('提交时间','dtTime','db',180,'"<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">$rs[dtTime]</a>"');
	$order->add_col('表单处理','handling','db',140,'$rs["handling"]?"已处理":"未处理"');
	$order->add_col('处理','oper','text',140,'"<a href=\"./index.php?a=edit&p=$rs[channelId]&n=$rs[id]\">[查看]</a> <a href=\"./index.php?a=destroy&p=$rs[channelId]&n=$rs[id]\">[删除]</a> ".pross($rs["handling"],$rs["id"])." "');
}
function edit()
{
	global $order_item,$db,$request;
	$sql='SELECT * FROM `'.TB_PREFIX.'order` WHERE id='.$request['n'];
	$order_item = $db->get_row($sql);
	if($_POST)
	{
		$rs = $db->query("UPDATE `".TB_PREFIX."order` SET `handling` = '1' , `result` = '".$request['result']."' WHERE `id` ='".$request['n']."' LIMIT 1" );
		
		if($rs)
		{
			redirect_to($request['p'],'index');
		}
	}
}
function destroy()
{
	global $db,$request;
	if(!empty($request['n']))
	{
		$sql='DELETE FROM `'.TB_PREFIX.'order` WHERE id='.$request['n'].' LIMIT 1';
		if($db->query($sql))
		{
			redirect_to($request['p'],'index');
		}
		else 
		{
			echo '删除失败！';
		}
	}
}
function dealorder()
{ 
	global $db,$request;
	$db->query("UPDATE `".TB_PREFIX."order` SET `handling` = '1' WHERE `id` ='".$request['n']."' LIMIT 1" );
	redirect_to($request['p'],'index');
}
function pross($handling,$id)
{
	global $request;
	if($handling)
	{
		return ;
	}
	else
	{ 
		return '<a href="./index.php?p='.$request['p'].'&a=dealorder&n='.$id.'">[处理]</a>';
	}
}
?>