<?php
function index()
{
	global $db,$request,$guestbookStr,$count,$sb;

	$count = $db->get_var('SELECT count(*) FROM `'.TB_PREFIX.'guestbook` WHERE channelId='.$request['p']);
	if(!empty($request['mdtp'])&&$request['mdtp'] != '1')
		$count = $count - (intval($request['mdtp']) - 1)*6;
	
	$sb = new sqlbuilder('mdt','SELECT * FROM `'.TB_PREFIX.'guestbook` WHERE channelId='.$request['p'],'id desc',$db,6);
}
function edit()
{
	global $db,$request,$guestbook;
	$id = $request['n'];
	if(empty($request['content1']))
	{
		$sql = "SELECT * FROM ".TB_PREFIX."guestbook WHERE id='$id'";
		$guestbook = $db->get_row($sql);
	}
	else
	{		
		$rs = $db->query("UPDATE `".TB_PREFIX."guestbook` SET `auditing` = '1' ,`isPublic` = '1' , `content1` = '".$request['content1']."' WHERE `id` ='".$request['n']."' LIMIT 1" );
		
		redirect_to($request['p'],'index');
	}
}
function destroy()
{
	global $db,$request;
	if(!empty($request['n']))
	{
		$sql='DELETE FROM '.TB_PREFIX.'guestbook WHERE id='.$request['n'].' LIMIT 1';
		if($db->query($sql))
		{
			redirect_to($request['p'],'index');
		}
		else {
			echo '删除失败！';
		}
	}
}
?>